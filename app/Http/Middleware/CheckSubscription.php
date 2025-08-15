<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Map feature slugs to usage sources
     * Key = feature slug in DB, Value = usage source
     */
    protected array $usageMap = [
        'questions_limit'        => 'questions',
        'quiz_attempts_limit'    => 'quizAttempts',
        'question_pool_limit'    => 'questionPools',
        'users_limit'            => 'users',
        'storage_space'          => 'storage',
        'quiz_groups_limit'      => 'quizGroups',
        'examiners_limit'        => 'examiners',
        'examinees_limit'        => 'examinees',
        'user_groups_limit'      => 'groups',
        'questions_per_quiz'     => 'questionsPerQuiz',
    ];

    public function handle(Request $request, Closure $next, $feature = null): Response
    {
        $user = $request->user();
        $organization = $user->currentOrganization();

        if (!$organization) {
            return redirect()->route('organizations.select')
                ->with('error', 'Please select an organization to continue');
        }

        // Skip subscription check for exempt routes
        if ($request->routeIs(...$this->exemptRoutes())) {
            return $next($request);
        }

        // Examiner-specific feature check
        if ($user->hasRole('examiner') && $feature) {
            return $this->checkFeatureAccess($request, $next, $organization, $feature);
        }

        return $next($request);
    }

    /**
     * Check if organization has access to a feature
     */
    protected function checkFeatureAccess($request, $next, $organization, string $feature)
    {
        // Check feature existence in plan
        if (!$organization->hasFeature($feature)) {
            return $this->deny($request, "This feature is not available in your subscription plan.");
        }

        // Check numeric limits if feature has a value
        $limit = $organization->featureLimit($feature);

        if (is_numeric($limit)) {
            $usage = $this->getUsage($organization, $feature);

            if ($usage >= $limit) {
                return $this->deny($request,
                    "You have reached your limit for {$feature} ({$usage}/{$limit}). Upgrade your plan for higher limits."
                );
            }
        }

        return $next($request);
    }

    /**
     * Determine current usage for a feature
     */
    protected function getUsage($organization, string $feature): int
    {
        switch ($this->usageMap[$feature] ?? null) {
            case 'questions':
                return $organization->questions()->count();
            case 'quizAttempts':
                return $organization->quizAttempts()->count();
            case 'questionPools':
                return $organization->questionPools()->count();
            case 'users':
                return $organization->users()->count();
            case 'storage':
                return $organization->storageUsageInMB();
            case 'quizGroups':
                return $organization->quizGroups()->count();
            case 'examiners':
                return $organization->users()->role('examiner')->count();
            case 'examinees':
                return $organization->users()->role('examinee')->count();
            case 'groups':
                return $organization->groups()->count();
            case 'questionsPerQuiz':
                // Example: implement actual per quiz check elsewhere
                return 0;
            default:
                return 0;
        }
    }

    /**
     * Return routes exempt from subscription checks
     */
    protected function exemptRoutes(): array
    {
        return [
            'examiner.subscription.*',
            'subscribe',
            'payment.callback',
            'paystack.webhook',
            'logout',
        ];
    }

    /**
     * Handle denial responses
     */
    protected function deny(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }
        return redirect()->back()->with('error', $message);
    }
}
