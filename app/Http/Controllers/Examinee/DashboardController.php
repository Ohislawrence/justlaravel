<?php

namespace App\Http\Controllers\Examinee;

use App\Http\Controllers\Controller;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        // Get stats
        $stats = [
            'completed_quizzes' => QuizAttempt::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
            'average_score' => number_format(QuizAttempt::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->avg('percentage') ?? 0,2
                ),
            'time_spent' => QuizAttempt::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->sum('time_spent'),
            'available_quizzes' => $user->availableQuizzes()->count(),
        ];
        
        // Get recent attempts (last 5)
        $recentAttempts = QuizAttempt::with('quiz')
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get available quizzes
        $availableQuizzes = $user->availableQuizzes()
            ->with(['organization'])
            ->latest() // Order by most recent first
            ->take(5)  // Limit to 5 quizzes
            ->get()
            ->map(function ($quiz) use ($user) {
                $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->count();
                    
                $quiz->attempts_remaining = $quiz->max_attempts 
                    ? max(0, $quiz->max_attempts - $attempts)
                    : null;
                    
                return $quiz;
            });
        
        return inertia('Examinee/Dashboard', [
            'stats' => $stats,
            'recentAttempts' => $recentAttempts,
            'availableQuizzes' => $availableQuizzes,
        ]);
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        
        $filters = $request->only(['status', 'sort']);
        
        $query = QuizAttempt::with(['quiz', 'quiz.organization'])
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at');
        
        // Apply status filter
        if ($request->status === 'passed') {
            $query->where('is_passed', true);
        } elseif ($request->status === 'failed') {
            $query->where('is_passed', false);
        }
        
        // Apply sorting
        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('completed_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('percentage', 'desc');
                break;
            case 'lowest':
                $query->orderBy('percentage', 'asc');
                break;
            default: // 'newest'
                $query->orderBy('completed_at', 'desc');
                break;
        }
        
        $attempts = $query->paginate(10)->withQueryString();
        
        return inertia('Examinee/QuizHistory', [
            'attempts' => $attempts,
            'filters' => $filters,
        ]);
    }

    public function quizzes(Request $request)
    {
        $user = auth()->user();
        
        $filters = $request->only(['status', 'organization']);
        //dd($user->availableQuizzes());
        // Get all quizzes available to the user
        $quizzes = $user->availableQuizzes()
            ->with(['organization', 'attempts' => function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orderBy('percentage', 'desc');
            }])
            ->get()
            ->map(function ($quiz) use ($user) {
                // Determine quiz status
                $now = now();
                $status = 'available';
                $statusDisplay = 'Available';
                
                if ($quiz->starts_at && $quiz->starts_at > $now) {
                    $status = 'upcoming';
                    $statusDisplay = 'Upcoming';
                } elseif ($quiz->ends_at && $quiz->ends_at < $now) {
                    $status = 'expired';
                    $statusDisplay = 'Expired';
                }
                
                // Check if completed
                $attempts = $quiz->attempts->where('user_id', $user->id);
                if ($attempts->isNotEmpty()) {
                    $status = 'completed';
                    $statusDisplay = 'Completed';
                }
                
                // Calculate attempts remaining
                $attemptsCount = $attempts->count();
                $attemptsRemaining = $quiz->max_attempts 
                    ? max(0, $quiz->max_attempts - $attemptsCount)
                    : null;
                
                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'share_token' => $quiz->share_token,
                    'time_limit' => $quiz->time_limit,
                    'passing_score' => $quiz->passing_score,
                    'starts_at' => $quiz->starts_at?->toDateTimeString(),
                    'ends_at' => $quiz->ends_at?->toDateTimeString(),
                    'allow_retake' => $quiz->allow_retake,
                    'max_attempts' => $quiz->max_attempts,
                    'attempts_remaining' => $attemptsRemaining,
                    'organization' => [
                        'id' => $quiz->organization->id,
                        'name' => $quiz->organization->name,
                    ],
                    'status' => $status,
                    'statusDisplay' => $statusDisplay,
                    'best_attempt' => $attempts->first() ? [
                        'id' => $attempts->first()->id,
                        'percentage' => $attempts->first()->percentage,
                        'is_passed' => $attempts->first()->is_passed,
                    ] : null,
                ];
            });
        
        // Get organizations the user has quizzes from
        $organizations = $user->organizations()
            ->whereHas('quizzes', function($query) use ($user) {
                $query->whereHas('users', function($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->orWhereHas('groups.members', function($query) use ($user) {
                    $query->where('users.id', $user->id);
                });
            })
            ->get();
        
        return inertia('Examinee/MyQuizzes', [
            'quizzes' => $quizzes,
            'organizations' => $organizations,
            'filters' => $filters,
            
        ]);
    }
}
