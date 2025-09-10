<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionPool;
use Illuminate\Http\Request;
use App\Services\ActivityService;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoolQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(QuestionPool $pool)
    {
        $questionTypes = [
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'short_answer' => 'Short Answer',
            'essay' => 'Essay',
            'fill_in_blank' => 'Fill in the Blank',
            'matching' => 'Matching',
            'ordering' => 'Ordering',
        ];

        return Inertia::render('Examiner/QuestionPools/AddNewQuestion', [
            'pool' => $pool,
            'questionTypes' => $questionTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, QuestionPool $pool)
    { 
        $organization = auth()->user()->organizations()->first();
        // Get subscription question limit
        

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
            'options' => 'nullable|array|min:2', 
            'correct_answers' => 'nullable|array',
            'correct_answers.*' => ['required', function ($attribute, $value, $fail) {
                                    if (!is_string($value) && !is_int($value)) {
                                        $fail("The $attribute must be a string or integer.");
                                    }
                                }],
            'settings' => 'nullable|array',
        ]);
        
        $validated['is_required'] = $request->boolean('is_required');
        $validated['organization_id'] = $organization->id;
        $validated['created_by'] = auth()->id();
        

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('questions', $filename, 'public');
            $validated['image'] = Storage::url($imagePath);
        }

        $question = Question::create($validated);
        $pool->questions()->attach($question->id);

        ActivityService::logQuizCreated($pool, auth()->user());
        
        return redirect()->route('examiner.question-pools.manage-questions', $pool)
                         ->with('success', 'Question created and added successfully.');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //last_updated_by
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
