<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Quiz;
use App\Models\Organization;
use App\Models\Question;
use App\Models\QuestionPool;
use App\Models\QuizAttempt;
use App\Services\ActivityService;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $organization = auth()->user()->organizations()->first();
        $quizzes = $organization->quizzes()
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(10);

        return inertia('Examiner/Quizzes/Index', [
            'organization' => $organization,
            'quizzes' => $quizzes->withQueryString(),
            'filters' => request()->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       $organization = auth()->user()->organizations()->first();
        return inertia('Examiner/Quizzes/Create', [
            'organization' => $organization,
            'quizTypes' => [
                'test' => 'Standard Test',
                'exam' => 'Formal Exam',
                'practice' => 'Practice Quiz',
                'survey' => 'Survey'
            ],
            'industries' => [
                'education' => 'Education',
                'corporate' => 'Corporate',
                'hr' => 'Human Resources',
                'healthcare' => 'Healthcare'
            ],
            'availablePools' => $organization->questionPools()
            ->with(['questions' => function($query) {
                $query->select('questions.id', 'questions.question');
            }])
            ->get()
            ->map(function($pool) {
                return [
                    'id' => $pool->id,
                    'name' => $pool->name,
                    'description' => $pool->description,
                    'questions_count' => $pool->questions->count(),
                    'questions_to_show' => $pool->questions_to_show,
                    'questions' => $pool->questions->map(fn($q) => $q->id)
                ];
            }),
        'availableQuestions' => Question::query()
            ->where(function($query) use ($organization) {
                $query->whereNull('organization_id')
                      ->orWhere('organization_id', $organization->id);
            })
            ->whereDoesntHave('pools')
            ->select('id', 'question')
            ->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'quiz_type' => ['required', Rule::in(['test', 'exam', 'practice', 'survey'])],
            'industry' => ['nullable', Rule::in(['education', 'corporate', 'hr', 'healthcare'])],
            'is_published' => ['boolean'],
            'is_public' => ['boolean'],
            'randomize_questions' => ['boolean'],
            'randomize_answers' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'show_leaderboard' => ['boolean'],
            'enable_discussions' => ['boolean'],
            'max_attempts' => ['nullable', 'integer', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'settings' => ['nullable', 'array'],
            'pools' => 'nullable|array',
            'pools.*.name' => 'required|string',
            'pools.*.description' => 'nullable|string', // Added description validation
            'pools.*.questions_to_show' => 'required|integer|min:1',
            'pools.*.questions' => 'required|array|min:1'
        ]);

        $organization = auth()->user()->organizations()->first();

        // Use transaction to ensure data consistency
        return DB::transaction(function () use ($validated, $organization, $request) {
            // Handle empty values conversion
            $quizData = [
                'user_id' => auth()->id(),
                'slug' => Str::slug($validated['title']),
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'instructions' => $validated['instructions'] ?? null,
                'quiz_type' => $validated['quiz_type'],
                'industry' => $validated['industry'] ?? null,
                'is_published' => $validated['is_published'] ?? false,
                'is_public' => $validated['is_public'] ?? false,
                'randomize_questions' => $validated['randomize_questions'] ?? false,
                'randomize_answers' => $validated['randomize_answers'] ?? false,
                'show_correct_answers' => $validated['show_correct_answers'] ?? false,
                'show_leaderboard' => $validated['show_leaderboard'] ?? false,
                'enable_discussions' => $validated['enable_discussions'] ?? false,
                'max_attempts' => $validated['max_attempts'] ? (int)$validated['max_attempts'] : null,
                'max_participants' => $validated['max_participants'] ? (int)$validated['max_participants'] : null,
                'time_limit' => $validated['time_limit'] ? (int)$validated['time_limit'] : null,
                'passing_score' => $validated['passing_score'] ? (int)$validated['passing_score'] : null,
                'starts_at' => !empty($validated['starts_at']) ? Carbon::parse($validated['starts_at']) : null,
                'ends_at' => !empty($validated['ends_at']) ? Carbon::parse($validated['ends_at']) : null,
                'settings' => $validated['settings'] ?? [],
            ];

            $quiz = $organization->quizzes()->create($quizData);

            // Create pools and attach questions
           // if (!empty($validated['pools'])) {
           //     foreach ($validated['pools'] as $poolData) {
           //         $pool = $quiz->pools()->create([
            //            'name' => $poolData['name'],
            //            'description' => $poolData['description'] ?? null,
            //            'questions_to_show' => (int)$poolData['questions_to_show'],
            //            'organization_id' => $organization->id // Ensure organization_id is set
            //        ]);

             //       // Ensure questions are integers and exist
             //       $questionIds = array_map('intval', $poolData['questions']);
            //        $pool->questions()->sync($questionIds);
            //    }
           // }

            ActivityService::logQuizCreated($quiz, auth()->user());

            return redirect()->route('examiner.quizzes.index', $organization->id)
                ->with('success', 'Quiz created successfully!');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        //$this->authorize('view', $quiz);

        $quiz->load([
            'organization',
            'user',
            'questions',
            'questionPools',
            'groups'
        ]);

        return inertia('Examiner/Quizzes/Show', [
            'quiz' => $quiz,
            'stats' => [
                'attempts_count' => $quiz->attempts()->count(),
                'average_score' => $quiz->attempts()->avg('score'),
                'completion_rate' => $quiz->attempts()->whereNotNull('completed_at')->count() / 
                                    max($quiz->attempts()->count(), 1) * 100
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $quiz)
    {
        return inertia('Examiner/Quizzes/Edit', [
            'quiz' => $quiz,
            'quizTypes' => [
                'test' => 'Standard Test',
                'exam' => 'Formal Exam',
                'practice' => 'Practice Quiz',
                'survey' => 'Survey'
            ],
            'industries' => [
                'education' => 'Education',
                'corporate' => 'Corporate',
                'hr' => 'Human Resources',
                'healthcare' => 'Healthcare'
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'quiz_type' => ['required', Rule::in(['test', 'exam', 'practice', 'survey'])],
            'industry' => ['nullable', Rule::in(['education', 'corporate', 'hr', 'healthcare'])],
            'is_published' => ['boolean'],
            'is_public' => ['boolean'],
            'randomize_questions' => ['boolean'],
            'randomize_answers' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'show_leaderboard' => ['boolean'],
            'enable_discussions' => ['boolean'],
            'max_attempts' => ['nullable', 'integer', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'settings' => ['nullable', 'array'],
            'pools.*.questions_to_show' => 'required|integer|min:1',
            'pools.*.questions' => 'array|min:1'
        ]);

        // Only update slug if title changed
        if ($quiz->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $quiz->update($validated);

        return redirect()->route('examiner.quizzes.show', $quiz)
            ->with('success', 'Quiz updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $quiz)
    {
        try {
            // Check if user has permission to delete this quiz
            if ($quiz->user_id !== auth()->id()) {
                return redirect()->back()->with('error', 'You do not have permission to delete this quiz.');
            }
    
            // Delete related records if needed
            //$quiz->pools()->delete();
            //$quiz->attempts()->delete();
            
            $quiz->delete();
            
            return redirect()->back()->with('success', 'Quiz deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete quiz. Please try again.');
        }
    }

    // Toggle Publish Status
    public function togglePublish(Quiz $quiz)
    {
        //$this->authorize('update', $quiz);

        $quiz->update(['is_published' => !$quiz->is_published]);

        return back()->with('success', $quiz->is_published 
            ? 'Quiz published successfully!' 
            : 'Quiz unpublished successfully!');
    }

    public function getPoolQuestions(Quiz $quiz, QuestionPool $pool)
    {
        return response()->json([
            'questions' => $pool->questions()
                ->inRandomOrder()
                ->take($pool->questions_to_show)
                ->get()
        ]);
    }

    public function startAttempt(Quiz $quiz)
    {
        $questions = $quiz->pools->flatMap(function($pool) {
            return $pool->questions()
                ->inRandomOrder()
                ->take($pool->questions_to_show)
                ->get();
        });
    
        return view('quiz.attempt', [
            'questions' => $questions
        ]);
    }

    // Generate shareable link
    public function shareLink(Quiz $quiz)
    {
        // Ensure user is authorized
        //$this->authorize('update', $quiz);
        
        try {
            if (!$quiz->share_token) {
                $quiz->update([
                    'share_token' => Str::random(32),
                    'shareable_link' => route('quiz.show', [
                        'quiz' => $quiz->id,
                        'token' => $quiz->share_token
                    ])
                ]);
            }

            return response()->json([
                'link' => $quiz->shareable_link ?? route('quiz.show', [
                    'quiz' => $quiz->id,
                    'token' => $quiz->share_token
                ])
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Share link error: '.$e->getMessage());
            return response()->json([
                'error' => 'Failed to generate share link'
            ], 500);
        }
    }

    public function showAttempts(Quiz $quiz, QuizAttempt $attempt)
    {
        // Ensure the attempt belongs to the quiz
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }

        // Load necessary relationships
        $attempt->load([
            'user',
            'responses.question',
            'quiz.questions'
        ]);

        return Inertia::render('Examiner/QuizAttempts/Show', [
            'quiz' => $quiz,
            'attempt' => $attempt,
            'questions' => $quiz->questions()->with(['responses' => function($q) use ($attempt) {
                $q->where('attempt_id', $attempt->id);
            }])->get()
        ]);
    }
}
