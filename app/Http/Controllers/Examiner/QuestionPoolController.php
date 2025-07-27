<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionPool;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Inertia\Inertia;


class QuestionPoolController extends Controller
{
    // Display list of pools for the current organization
    public function index()
    {
        $organization = auth()->user()->organizations()->first();
        //$pools = QuestionPool::with(['quiz', 'questions'])->where('organization_id', $organization->id)
        //->latest()->paginate(10);
        
        $pools = QuestionPool::withCount(['questions', 'quizzes'])
            ->with(['quiz', 'questions'])
            ->where('organization_id', $organization->id)
            ->latest()
            ->paginate(10);
        /**
        $pools = QuestionPool::with(['quiz', 'questions'])
            ->where(function($query) use ($organization) {
                $query->whereHas('quiz', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })
                ->orWhere('is_global', true);
            })
            ->latest()
            ->paginate(10);
         */
        return Inertia::render('Examiner/QuestionPools/Index', [
            'pools' => $pools,
        ]);
    }

    // Show create form
    public function create()
    {
        $organization = auth()->user()->organizations()->first();
        
        $quizzes = Quiz::where('organization_id', $organization->id)
            ->where('user_id', auth()->id()) // Only quizzes created by this examiner
            ->get();

        return Inertia::render('Examiner/QuestionPools/CreateEditForm', [
            'quizzes' => $quizzes,
        ]);
    }

    // Store new pool
    public function store(Request $request)
    {
        $organization = auth()->user()->organizations()->first();
        
        $validated = $request->validate([
            'quiz_id' => [
                'nullable',
                'exists:quizzes,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value && !Quiz::where('id', $value)
                        ->where('organization_id', $organization->id)
                        ->where('user_id', auth()->id())
                        ->exists()) {
                        $fail('The selected quiz is invalid.');
                    }
                }
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions_to_show' => 'nullable|integer|min:1',
        ]);

        $validated['is_global'] = false;
        $validated['organization_id'] = $organization->id;

        QuestionPool::create($validated);

        return redirect()->route('examiner.question-pools.index')
            ->with('success', 'Question pool created successfully.');
    }

    // Show edit form
    public function edit(QuestionPool $questionPool)
    {
        //$this->authorize('update', $questionPool);
        
        $organization = auth()->user()->organizations()->first();
        
        $quizzes = Quiz::where('organization_id', $organization->id)
            ->where('user_id', auth()->id())
            ->get();

        return Inertia::render('Examiner/QuestionPools/CreateEditForm', [
            'pool' => $questionPool,
            'quizzes' => $quizzes,
        ]);
    }

    // Update pool
    public function update(Request $request, QuestionPool $questionPool)
    {
        //$this->authorize('update', $questionPool);
        
        $organization = auth()->user()->organizations()->first();
        
        $validated = $request->validate([
            'quiz_id' => [
                'nullable',
                'exists:quizzes,id',
                function ($attribute, $value, $fail) use ($organization) {
                    if ($value && !Quiz::where('id', $value)
                        ->where('organization_id', $organization->id)
                        ->where('user_id', auth()->id())
                        ->exists()) {
                        $fail('The selected quiz is invalid.');
                    }
                }
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions_to_show' => 'nullable|integer|min:1',
            'is_global' => 'boolean',
        ]);

        $questionPool->update($validated);

        return redirect()->route('examiner.question-pools.index')
            ->with('success', 'Question pool updated successfully.');
    }

    // Delete pool
    public function destroy(QuestionPool $questionPool)
    {
        //$this->authorize('delete', $questionPool);
        
        $questionPool->delete();

        return redirect()->route('examiner.question-pools.index')
            ->with('success', 'Question pool deleted successfully.');
    }

    // Manage pool questions
    public function manageQuestions(QuestionPool $pool)
{
    $pool->load(['questions' => function($query) {
        $query->select(
            'questions.id',
            'questions.question',
            'questions.type',
            'questions.points'
        );
    }]);

    $availableQuestions = Question::whereDoesntHave('pools', function($query) use ($pool) {
            $query->where('question_pool_id', $pool->id);
        })
        ->select(
            'id',
            'question',
            'type',
            'points'
        )
        ->get();

    return inertia('Examiner/QuestionPools/ManageQuestions', [
        'pool' => $pool,
        'availableQuestions' => $availableQuestions,
    ]);
}

    // Attach questions to pool
    public function attachQuestions(Request $request, QuestionPool $pool)
    {
        //$this->authorize('update', $pool);
        
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => [
                'exists:questions,id',
                function ($attribute, $value, $fail) use ($pool) {
                    $validQuestion = Question::where('id', $value)
                        ->when($pool->quiz_id, function($query) use ($pool) {
                            $query->where('quiz_id', $pool->quiz_id);
                        }, function($query) {
                            $query->whereHas('quiz', function($q) {
                                $q->where('organization_id', auth()->user()->organizations()->first()->id);
                            });
                        })
                        ->exists();
                    
                    if (!$validQuestion) {
                        $fail('One or more selected questions are invalid for this pool.');
                    }
                }
            ],
        ]);

        $pool->questions()->attach($request->question_ids);

        return redirect()->back()
            ->with('success', 'Questions added to pool successfully.');
    }

    // Detach question from pool
    public function detachQuestion(QuestionPool $pool, Question $question)
    {
        //$this->authorize('update', $pool);
        
        $pool->questions()->detach($question->id);

        return redirect()->back()
            ->with('success', 'Question removed from pool successfully.');
    }
}
