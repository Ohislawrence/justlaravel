<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuestionPool;
use App\Services\ActivityService;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    // Display all questions for a quiz
    public function index(Quiz $quiz)
    {
        $organization = auth()->user()->organizations()->first();

        $questions = $quiz->questions()->with('pools')->get();
        $pools = $quiz->questionPools()->withCount('questions')->get();
        $questionsLimit = $organization->getFeatureValue('questions_limit'); 
        $currentQuestionsCount = $organization->questions()->count(); 

        return Inertia::render('Examiner/Questions/Index', [
            'quiz' => $quiz,
            'questions' => $questions,
            'pools' => $pools,
            'questionsLimit' => $questionsLimit,
            'currentQuestionsCount' => $currentQuestionsCount,
        ]);
    }

    // Show question creation form
    public function create(?Quiz $quiz = null, ?QuestionPool $pool = null)
    {
        $questionTypes = [
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'essay' => 'Essay',
            'short_answer' => 'Short Answer',
            'fill_in_blank' => 'Fill in the Blank',
            'matching' => 'Matching',
            'ordering' => 'Ordering',
        ];

        //$pools = $quiz->questionPools()->get();

        return Inertia::render('Examiner/Questions/Create', [
            'quiz' => $quiz ?? null,
            'questionTypes' => $questionTypes,
            'pool' => $pool ?? null,
        ]);
    }

    // Store a new question
    public function store(Request $request, ?Quiz $quiz = null, ?QuestionPool $pool = null)
    { 
        $organization = auth()->user()->organizations()->first();
        
        if (!$organization->canAddQuestion()) {
            return redirect()->back()->withErrors([
                'limit' => "You have reached your question limit. Upgrade your plan to add more."
            ]);
        }
        
        $validated = $request->validate([
            'type' => 'required|string',
            'question' => 'required|string',
            'description' => 'nullable|string',
            'points' => 'required|numeric|min:0',
            'time_limit' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            'options' => 'nullable|array', 
            'correct_answers' => 'nullable|array',
            'correct_answers.*' => ['required', function ($attribute, $value, $fail) {
                                    if (!is_string($value) && !is_int($value)) {
                                        $fail("The $attribute must be a string or integer.");
                                    }
                                }],
            'settings' => 'nullable|array',
        ]);

        $validated['is_required'] = $request->boolean('is_required');
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileSizeMB = $image->getSize() / 1024 / 1024;

            if (!$organization->hasStorageSpace($fileSizeMB)) {
                return back()->with('error', 'Not enough storage space available');
            }

            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('questions', $filename, 'public');
            $validated['image'] = Storage::url($imagePath);
            
        }
        if($request->is_pool == null){
            $question = $quiz->questions()->create($validated);
            return redirect()->route('examiner.quizzes.questions.index', $quiz)
                         ->with('success', 'Question created successfully.');
        }else{
            $question = Question::create($validated);
            $pool->questions()->attach($question->id);
            ActivityService::logQuizCreated($pool, auth()->user());
            return redirect()->route('examiner.question-pools.manage-questions', $pool)
                         ->with('success', 'Question created and added successfully.');
        }
   
    }

    // Show question edit form
    public function edit(?Quiz $quiz = null, ?QuestionPool $pool = null, Question $question)
    {
        $questionTypes = [
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'short_answer' => 'Short Answer',
            'essay' => 'Essay',
            'fill_in_the_blank' => 'Fill in the Blank',
            'matching' => 'Matching',
            'ordering' => 'Ordering',
        ];

        //$pools = $quiz->questionPools()->get();
        //$question->load('pools');

        return Inertia::render('Examiner/Questions/Edit', [
            'quiz' => $quiz ?? null,
            'pool' => $pool ?? null,
            'question' => $question,
            'questionTypes' => $questionTypes,
        ]);
    }

    // Update a question
    public function update(Request $request, ?Quiz $quiz = null, ?QuestionPool $pool = null, Question $question)
    {
        
        // Debug: Check what data is being received
        // dd($request->all(), $request->files->all());
        
        // Validate the request
        $validated = $request->validate([
            'type' => 'required|string',
            'question' => 'required|string',
            'description' => 'nullable|string',
            'points' => 'nullable|numeric|min:0',
            'time_limit' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean', // Changed to nullable
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            'remove_image' => 'nullable|boolean', // Flag to remove existing image
            'options' => 'nullable|array', 
            'correct_answers' => 'nullable|array',
            'correct_answers.*' => ['nullable', function ($attribute, $value, $fail) {
                if ($value !== null && !is_string($value) && !is_int($value)) {
                    $fail("The $attribute must be a string or integer.");
                }
            }],
            'settings' => 'nullable|array',
        ]);

        // Handle boolean conversion for is_required
        $validated['is_required'] = $request->boolean('is_required');
        $validated['organization_id'] = auth()->user()->organizations()->first()->id;
        
        // Handle image upload/removal
        if ($request->boolean('remove_image')) {
            // Delete existing image if it exists
            if ($question->image) {
                $this->deleteQuestionImage($question->image);
            }
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($question->image) {
                $this->deleteQuestionImage($question->image);
            }
            
            $image = $request->file('image');
            
            // Generate unique filename
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            
            // Store image in public disk under 'questions' folder
            $imagePath = $image->storeAs('questions', $filename, 'public');
            
            // Save the full URL to database
            $validated['image'] = Storage::url($imagePath);
        }
        // If neither remove_image nor new image file, keep existing image unchanged
        
        // Remove the remove_image flag from validated data before updating
        unset($validated['remove_image']);

        // Filter out null values to avoid overwriting existing data with null
        $updateData = array_filter($validated, function ($value) {
            return $value !== null;
        });
        
        // Always include boolean fields even if they're false
        $updateData['is_required'] = $validated['is_required'];
        
        // If image was set to null (for removal), include it
        if (array_key_exists('image', $validated) && $validated['image'] === null) {
            $updateData['image'] = null;
        }

        $question->update($updateData);
        
        if($request->is_pool == null){
            return redirect()->route('examiner.quizzes.questions.index', $quiz)
                         ->with('success', 'Question updated successfully.');
        }else{
            return redirect()->route('examiner.question-pools.manage-questions', $pool)
                         ->with('success', 'Question updated successfully.');
        }
    }

    /**
     * Helper method to delete question image
     */
    private function deleteQuestionImage($imageUrl)
    {
        try {
            // Extract the path from the URL
            // If storing full URL: /storage/questions/filename.jpg
            // Extract: questions/filename.jpg
            $path = str_replace('/storage/', '', parse_url($imageUrl, PHP_URL_PATH));
            
            // Delete from storage
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Exception $e) {
            // Log error but don't fail the update
            \Log::error('Failed to delete question image: ' . $e->getMessage());
        }
    }

    // Delete a question
    public function destroy(Quiz $quiz, Question $question)
    {
        $question->delete();

        return redirect()->route('examiner.quizzes.questions.index', $quiz)
                         ->with('success', 'Question deleted successfully.');
    }
}
