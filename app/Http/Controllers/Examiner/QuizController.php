<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\GradingSystem;
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
            ->with(['questions', 'questionPools' => function($query) {
                $query->withCount('questions');
            }])
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(10);
    
        // Transform the collection to include total_questions
        $quizzes->getCollection()->transform(function($quiz) {
            // Calculate total questions (direct + from pools)
            $directQuestions = $quiz->questions_count;
            $poolQuestions = $quiz->questionPools->sum(function($pool) {
                return min(
                    $pool->questions_count, 
                    $pool->pivot->questions_to_show ?? $pool->questions_count
                );
            });
            
            //$quiz->questions_count = $directQuestions ;
            $quiz->total_questions = $directQuestions + $poolQuestions;
            return $quiz;
        });
    
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
       // Get default systems and the organization's custom systems
       $defaultSystems = GradingSystem::where('is_default', true)->get();
       $customSystems = $organization->gradingSystems()->get();
       $gradingSystems = $defaultSystems->merge($customSystems);
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
        'certificateTemplates' => CertificateTemplate::all(),
        'initialData' => [
            'enable_certificates' => false,
            'certificate_template_id' => null,
            'certificate_pass_percentage' => 70,
            'certificate_expiry_days' => null,
        ],
        'availableQuestions' => Question::query()
            ->where(function($query) use ($organization) {
                $query->whereNull('organization_id')
                      ->orWhere('organization_id', $organization->id);
            })
            ->whereDoesntHave('pools')
            ->select('id', 'question')
            ->get(),
        'gradingSystems' => $gradingSystems,
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
            'max_attempts' => ['nullable', 'integer', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'settings' => ['nullable', 'array'],
            'survey_thank_you_message' => ['nullable', 'string', 'max:500'],
            'enable_certificates' => ['boolean'],
            'certificate_template_id' => ['nullable', 'exists:certificate_templates,id'],
            'certificate_pass_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'certificate_expiry_days' => ['nullable', 'integer', 'min:0'],
            'grading_system_id' => ['nullable', 'integer', 'exists:grading_systems,id'],
            'require_guest_info' => ['nullable','boolean'],
            'guest_info_required' => ['nullable', Rule::in(['none', 'name', 'email', 'both'])],
        ]);
    
        $organization = auth()->user()->organizations()->first();

        
    
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
            'survey_thank_you_message' => $validated['survey_thank_you_message'] ?? null,
            'enable_certificates' => $validated['enable_certificates'] ?? false,
            'certificate_template_id' => $validated['certificate_template_id'] ?? null,
            'certificate_pass_percentage' => $validated['certificate_pass_percentage'] ?? 70,
            'certificate_expiry_days' => $validated['certificate_expiry_days'] ?? null,
            'grading_system_id' => $validated['grading_system_id'] ?? null,
            'created_by' => auth()->id(),
            'require_guest_info' => $validated['require_guest_info'],
            'guest_info_required' => $validated['guest_info_required'],
        ];
    
        $quiz = $organization->quizzes()->create($quizData);
    
        ActivityService::logQuizCreated($quiz, auth()->user());
    
        //return redirect()->route('examiner.quizzes.edit', $quiz->id)
         //   ->with('success', 'Quiz created successfully! You can now add question pools.');
        return redirect()->route('examiner.quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully! You can now add questions from pools or create new ones for this quiz.');
            
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $quiz)
    {
        $organization = auth()->user()->organizations()->first();

        $quiz->load([
            'organization',
            'user',
            'questions',
            'questionPools' => function($query) {
                $query->withCount('questions')
                      ->withPivot('questions_to_show');
            },
            'groups'
        ]);

        $availablePools = $organization->questionPools()
        ->whereDoesntHave('quiz', function($q) use ($quiz) {
            $q->where('id', $quiz->id);
        })
        ->withCount('questions')
        ->get();
        $quizPools = $quiz->questionPools()->withCount('questions')->get();

        return inertia('Examiner/Quizzes/Show', [
            'quiz' => $quiz,
            'availablePools' => $availablePools,
            'quizPools' => $quizPools,
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
        $organization = auth()->user()->organizations()->first();
        $gradingSystems = GradingSystem::where('is_default', true)
        ->orWhere('organization_id', $organization->id)
        ->get()
        ->map(function ($system) {
            // Ensure grade_ranges is properly formatted as an array
            if (is_string($system->grade_ranges)) {
                try {
                    $system->grade_ranges = json_decode($system->grade_ranges, true);
                } catch (\Exception $e) {
                    $system->grade_ranges = [];
                }
            } elseif (!is_array($system->grade_ranges)) {
                $system->grade_ranges = [];
            }
            return $system;
        });
        return inertia('Examiner/Quizzes/Edit', [
            'quiz' => $quiz,
            'gradingSystems' => GradingSystem::where('is_default', true)
                    ->orWhere('organization_id', $organization->id)
                    ->get(),
            
            'certificateTemplates' => CertificateTemplate::all(),
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
            'is_published' => ['boolean'],
            'quiz_type' => ['required', Rule::in(['test', 'exam', 'practice', 'survey'])],
            'is_public' => ['boolean'],
            'randomize_questions' => ['boolean'],
            'randomize_answers' => ['boolean'],
            'show_correct_answers' => ['boolean'],
            'show_leaderboard' => ['boolean'],
            'max_attempts' => ['nullable', 'integer', 'min:0'],
            'max_participants' => ['nullable', 'integer', 'min:0'],
            'time_limit' => ['nullable', 'integer', 'min:1'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'settings' => ['nullable', 'array'],
            'survey_thank_you_message' => ['nullable', 'string', 'max:500'],
            'enable_certificates' => ['boolean'],
            'certificate_template_id' => ['nullable', 'exists:certificate_templates,id'],
            'certificate_pass_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'certificate_expiry_days' => ['nullable', 'integer', 'min:0'],
            'grading_system_id' => ['nullable', 'integer', 'min:0'],
        ]);

        // Only update slug if title changed
        if ($quiz->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $validated['last_updated_by'] = auth()->id();

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
            $quiz->questions()->delete();
            $quiz->attempts()->delete();
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
        //dd($attempt);
        // Ensure the attempt belongs to the quiz
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }
        //dd($quiz->questionPools);
        // Load necessary relationships
        $attempt->load([
            'user',
            'responses.question',
            'quiz.questions',
            'proctoringViolations', 
        ]);

        return Inertia::render('Examiner/QuizAttempts/Show', [
            'quiz' => $quiz,
            'questionpools' => $quiz->questionPools,
            'attempt' => $attempt,
            'violations' => $attempt->where('id', $attempt->id)->get(),
            'questions' => $quiz->questions()->with(['responses' => function($q) use ($attempt) {
                $q->where('attempt_id', $attempt->id);
            }])->get()
        ]);
    }

    public function attachPool(Request $request, Quiz $quiz)
    {
        $request->validate([
            'pool' => 'required|exists:question_pools,id',
            'questions_to_show' => 'nullable|integer|min:1' // Add if you want to customize
        ]);

        $pool = QuestionPool::findOrFail($request->pool);

        // Check if pool belongs to same organization
        if ($pool->organization_id !== $quiz->organization_id) {
            return back()->with('error', 'Invalid question pool');
        }

        // Attach with pivot data
        $quiz->questionPools()->syncWithoutDetaching([
            $pool->id => [
                'questions_to_show' => $request->questions_to_show ?? $pool->questions_to_show
            ]
        ]);

        return back()->with('success', 'Question pool added successfully');
    }

    public function detachPool(Quiz $quiz, QuestionPool $pool)
    {
        $quiz->questionPools()->detach($pool->id);
        return back()->with('success', 'Question pool removed successfully');
    }
}
