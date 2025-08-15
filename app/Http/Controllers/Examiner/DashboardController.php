<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ActivityLog;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $organization = auth()->user()->organizations()->first();
        
        if (!$organization) {
            return redirect()->route('organizations.select')
                ->with('error', 'Please select an organization first');
        }

        // Get days parameter or default to 7
        $days = $request->input('days', 7); 

        // Basic stats - all scoped to organization
        $stats = [
            'total_quizzes' => Quiz::where('organization_id', $organization->id)->count(),
            'quizzes_growth' => $this->getGrowthPercentage($organization),
            'active_attempts' => QuizAttempt::whereHas('quiz', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->whereNull('completed_at')->count(),
            'attempts_change' => $this->getAttemptsChange($organization),
            'avg_score' => round(QuizAttempt::whereHas('quiz', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->avg('percentage') ?? 0),
            'pass_rate' => round($this->calculatePassRate($organization)),
            'pass_rate_change' => $this->getPassRateChange($organization),
        ];

        // Recent activity - scoped to organization
    $recentActivity = ActivityLog::where('organization_id', $organization->id)
    ->latest()
    ->take(4)
    ->get()
    ->map(function ($log) {
        return [
            'id' => $log->id,
            'type' => $log->type,
            'title' => $this->getActivityTitle($log),
            'description' => $this->getActivityDescription($log),
            'timestamp' => $log->created_at
        ];
    });

    // Top quizzes - scoped to organization
    $topQuizzes = Quiz::where('organization_id', $organization->id)
        ->withCount('attempts')
        ->withAvg('attempts', 'percentage')
        ->orderByDesc('attempts_count')
        ->take(4)
        ->get()
        ->map(function ($quiz) {
            $passRate = $quiz->attempts_count > 0 
                ? round(($quiz->attempts()->where('is_passed', true)->count() / $quiz->attempts_count) * 100)
                : 0;
            
            return [
                'id' => $quiz->id,
                'title' => $quiz->title,
                'attempts_count' => $quiz->attempts_count,
                'avg_score' => round($quiz->attempts_avg_percentage ?? 0),
                'pass_rate' => $passRate
            ];
        });

    // Performance data - now uses the $days parameter
    $performanceData = $this->getPerformanceData($organization, $days);

    // Quiz type distribution - scoped to organization
    $quizTypes = Quiz::where('organization_id', $organization->id)
        ->selectRaw('quiz_type, count(*) as count')
        ->groupBy('quiz_type')
        ->pluck('count', 'quiz_type')
        ->toArray();

    $features = [];
    if ($organization->activeSubscription && $organization->activeSubscription->plan) {
        foreach ($organization->activeSubscription->plan->features as $feature) {
            $features;
        }
    }

    $planCurrent = $organization->latestSubscription;
    //questionlimits
    $UsedQuestion = $organization->questions()->count();
    $AllowedQuestionLimit = $organization->getFeatureValue('questions_limit');
    $percentageQuestionUsed = ($UsedQuestion/$AllowedQuestionLimit) * 100;
    $remainingQuestion = $AllowedQuestionLimit - $UsedQuestion;
    //AttemptsLimits
    $UsedAttempt = $organization->quizAttempts()->whereBetween('quiz_attempts.created_at', [
                    $planCurrent->starts_at,
                    $planCurrent->ends_at])->count();
    $AllowedAttemptLimit = $organization->getFeatureValue('quiz_attempts_limit');
    $percentageAttempt = ($UsedAttempt/$AllowedAttemptLimit) * 100;
    $remainingAttempts = $AllowedAttemptLimit - $UsedAttempt;
    //AiQuestionLimits
    $UsedAiQuestion = $organization->questions()->where('is_ai', true)->whereBetween('created_at', [
                        $planCurrent->starts_at,
                        $planCurrent->ends_at])->count();
    $AllowedAiQuestiontLimit = $organization->getFeatureValue('ai_question_generation');
    $percentageAiused = ($UsedAiQuestion/$AllowedAiQuestiontLimit) * 100;
    $remainingAiQuestion = $AllowedAiQuestiontLimit - $UsedAiQuestion;

    
    return inertia('Examiner/Dashboard', [
        'stats' => $stats,
        'recentActivity' => $recentActivity,
        'topQuizzes' => $topQuizzes,
        'performanceData' => $performanceData,
        'quizTypes' => $quizTypes,
        'filters' => ['days' => $days],
        'organization' => $organization,
        'planCurrent' => $planCurrent->plan->name,
        'UsedQuestion' => (int)$UsedQuestion,
        'AllowedQuestionLimit' => (int)$AllowedQuestionLimit,
        'UsedAttempt' => (int)$UsedAttempt,
        'AllowedAttemptLimit' => (int)$AllowedAttemptLimit,
        'UsedAiQuestion' => (int)$UsedAiQuestion,
        'AllowedAiQuestiontLimit' => (int)$AllowedAiQuestiontLimit,
        'percentageQuestionUsed' => (int)$percentageQuestionUsed,
        'remainingQuestion' => (int)$remainingQuestion,
        'percentageAttempt' => (int)$percentageAttempt,
        'remainingAttempts' => (int)$remainingAttempts,
        'percentageAiused' => (int)$percentageAiused,
        'remainingAiQuestion' => (int)$remainingAiQuestion,

    ]); 
    }

    protected function getGrowthPercentage(Organization $organization, string $period = 'month'): float
    {
        $currentPeriodStart = now()->startOfMonth(); 
        $previousPeriodStart = now()->subMonth()->startOfMonth();
        $previousPeriodEnd = now()->subMonth()->endOfMonth();
    
        $currentCount = Quiz::where('organization_id', $organization->id)
            ->where('created_at', '>=', $currentPeriodStart)
            ->count();
            
        $previousCount = Quiz::where('organization_id', $organization->id)
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();
    
        if ($previousCount === 0) {
            return $currentCount > 0 ? 100.0 : 0.0;
        }
    
        return round((($currentCount - $previousCount) / $previousCount) * 100, 1);
    }

    /**
     * Calculate change in active attempts compared to previous period
     */
    protected function getAttemptsChange(Organization $organization): float
    {
        $currentPeriodStart = now()->subDays(7);
        $previousPeriodStart = now()->subDays(14);
        $previousPeriodEnd = now()->subDays(7);
    
        $current = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->whereNull('completed_at')
            ->where('created_at', '>=', $currentPeriodStart)
            ->count();
            
        $previous = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->whereNull('completed_at')
            ->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd])
            ->count();
    
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }
    
        return round((($current - $previous) / $previous) * 100, 1);
    }

    /**
     * Calculate overall pass rate
     */
    protected function calculatePassRate(Organization $organization): float
    {
        $totalAttempts = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->count();
        
        $passedAttempts = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->where('is_passed', true)->count();
    
        return $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0.0;
    }

    /**
     * Calculate pass rate change compared to previous period
     */
    protected function getPassRateChange(Organization $organization): float
    {
        $currentPeriodStart = now()->subDays(30);
        $previousPeriodStart = now()->subDays(60);
        $previousPeriodEnd = now()->subDays(30);
    
        // Current period query
        $currentPeriodQuery = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->where('created_at', '>=', $currentPeriodStart);
    
        $currentPassed = (clone $currentPeriodQuery)->where('is_passed', true)->count();
        $currentTotal = $currentPeriodQuery->count();
        $currentPassRate = $currentTotal > 0 ? ($currentPassed / $currentTotal) * 100 : 0;
    
        // Previous period query
        $previousPeriodQuery = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd]);
    
        $previousPassed = (clone $previousPeriodQuery)->where('is_passed', true)->count();
        $previousTotal = $previousPeriodQuery->count();
        $previousPassRate = $previousTotal > 0 ? ($previousPassed / $previousTotal) * 100 : 0;
    
        // Calculate change
        if ($previousPassRate == 0) {
            return $currentPassRate > 0 ? 100.0 : 0.0;
        }
    
        return round($currentPassRate - $previousPassRate, 1);
    }

    /**
     * Get performance data for the given days
     */
    protected function getPerformanceData(Organization $organization, int $days): array
    {
        return Cache::remember("performance_data_{$organization->id}_{$days}", now()->addHours(1), function () use ($organization, $days) {
            $labels = [];
            $attemptsData = [];
            $avgScoresData = [];
    
            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('M j');
    
                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();
    
                $attempts = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })->whereBetween('created_at', [$startOfDay, $endOfDay])->count();
                
                $attemptsData[] = $attempts;
    
                $avgScore = QuizAttempt::whereHas('quiz', function($q) use ($organization) {
                    $q->where('organization_id', $organization->id);
                })->whereBetween('created_at', [$startOfDay, $endOfDay])
                  ->avg('percentage') ?? 0;
                  
                $avgScoresData[] = round($avgScore);
            }
    
            return [
                'labels' => $labels,
                'attempts' => $attemptsData,
                'avg_scores' => $avgScoresData
            ];
        });
    }

    /**
     * Generate human-readable activity title
     */
    protected function getActivityTitle(ActivityLog $log): string
    {
        return match ($log->type) {
            'quiz_completed' => 'Quiz Completed',
            'quiz_created' => 'New Quiz Created',
            'user_registered' => 'New User Registered',
            default => 'System Activity'
        };
    }

    /**
     * Generate descriptive activity message
     */
    protected function getActivityDescription(ActivityLog $log): string
    {
        $data = $log->data ?? [];
        
        return match ($log->type) {
            'quiz_completed' => sprintf(
                '%s completed %s with score %d%%',
                $data['user_name'] ?? 'A user',
                $data['quiz_title'] ?? 'a quiz',
                $data['percentage'] ?? 0
            ),
            'quiz_created' => sprintf(
                'New quiz "%s" created by %s',
                $data['quiz_title'] ?? 'Untitled Quiz',
                $data['creator_name'] ?? 'System'
            ),
            'user_registered' => sprintf(
                'New user %s registered',
                $data['user_name'] ?? 'Unknown'
            ),
            default => 'System activity occurred'
        };
    }
}
