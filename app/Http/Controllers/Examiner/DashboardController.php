<?php

namespace App\Http\Controllers\Examiner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;


class DashboardController extends Controller
{
    public function index()
    {
        // Basic stats
        $stats = [
            'total_quizzes' => Quiz::count(),
            'quizzes_growth' => $this->getGrowthPercentage(Quiz::class),
            'active_attempts' => QuizAttempt::whereNull('completed_at')->count(),
            'attempts_change' => $this->getAttemptsChange(),
            'avg_score' => round(QuizAttempt::avg('percentage') ?? 0),
            'pass_rate' => round($this->calculatePassRate()),
            'pass_rate_change' => $this->getPassRateChange(),
        ];

        // Recent activity
        $recentActivity = ActivityLog::latest()
            ->take(5)
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

        // Top quizzes
        $topQuizzes = Quiz::withCount('attempts')
            ->withAvg('attempts', 'percentage')
            ->orderByDesc('attempts_count')
            ->take(5)
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

        // Performance data (last 7 days by default)
        $performanceData = $this->getPerformanceData(7);

        // Quiz type distribution
        $quizTypes = Quiz::selectRaw('quiz_type, count(*) as count')
            ->groupBy('quiz_type')
            ->pluck('count', 'quiz_type')
            ->toArray();

        return inertia('Examiner/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'topQuizzes' => $topQuizzes,
            'performanceData' => $performanceData,
            'quizTypes' => $quizTypes
        ]);
    }

    protected function getGrowthPercentage(string $modelClass, string $period = 'month'): float
    {
        $currentPeriodStart = now()->startOfMonth(); // Changed from generic period to specific
        $previousPeriodStart = now()->subMonth()->startOfMonth();
        $previousPeriodEnd = now()->subMonth()->endOfMonth();

        $currentCount = $modelClass::where('created_at', '>=', $currentPeriodStart)->count();
        $previousCount = $modelClass::whereBetween('created_at', [
            $previousPeriodStart,
            $previousPeriodEnd
        ])->count();

        if ($previousCount === 0) {
            return $currentCount > 0 ? 100.0 : 0.0;
        }

        return round((($currentCount - $previousCount) / $previousCount) * 100, 1);
    }

    /**
     * Calculate change in active attempts compared to previous period
     */
    protected function getAttemptsChange(): float
    {
        $currentPeriodStart = now()->subDays(7);
        $previousPeriodStart = now()->subDays(14);
        $previousPeriodEnd = now()->subDays(7);

        $current = QuizAttempt::whereNull('completed_at')
            ->where('created_at', '>=', $currentPeriodStart)
            ->count();
            
        $previous = QuizAttempt::whereNull('completed_at')
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
    protected function calculatePassRate(): float
    {
        $totalAttempts = QuizAttempt::count();
        $passedAttempts = QuizAttempt::where('is_passed', true)->count();

        return $totalAttempts > 0 ? round(($passedAttempts / $totalAttempts) * 100, 1) : 0.0;
    }

    /**
     * Calculate pass rate change compared to previous period
     */
    protected function getPassRateChange(): float
    {
        $currentPeriodStart = now()->subDays(30);
        $previousPeriodStart = now()->subDays(60);
        $previousPeriodEnd = now()->subDays(30);

        $currentPeriodQuery = QuizAttempt::where('created_at', '>=', $currentPeriodStart);
        $previousPeriodQuery = QuizAttempt::whereBetween('created_at', [$previousPeriodStart, $previousPeriodEnd]);

        $currentPassRate = $currentPeriodQuery->count() > 0 
            ? ($currentPeriodQuery->where('is_passed', true)->count() / $currentPeriodQuery->count()) * 100
            : 0;
            
        $previousPassRate = $previousPeriodQuery->count() > 0
            ? ($previousPeriodQuery->where('is_passed', true)->count() / $previousPeriodQuery->count()) * 100
            : 0;

        if ($previousPassRate === 0) {
            return $currentPassRate > 0 ? 100.0 : 0.0;
        }

        return round($currentPassRate - $previousPassRate, 1);
    }

    /**
     * Get performance data for the given days
     */
    protected function getPerformanceData(int $days): array
    {
        return Cache::remember("performance_data_{$days}", now()->addHours(1), function () use ($days) {
            $labels = [];
            $attemptsData = [];
            $avgScoresData = [];

            for ($i = $days; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels[] = $date->format('M j');

                $startOfDay = $date->copy()->startOfDay();
                $endOfDay = $date->copy()->endOfDay();

                $attempts = QuizAttempt::whereBetween('created_at', [$startOfDay, $endOfDay])->count();
                $attemptsData[] = $attempts;

                $avgScore = QuizAttempt::whereBetween('created_at', [$startOfDay, $endOfDay])
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
