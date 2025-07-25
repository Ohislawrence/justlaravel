<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $feature = null)
    {
        $user = $request->user();
        $organization = $user->currentOrganization();
        
        if (!$organization) {
            return redirect()->route('organizations.select')
                ->with('error', 'Please select an organization to continue');
        }
    
        // Skip subscription check for these routes
        $exemptRoutes = [
            'examiner.subscription.*',
            'subscribe',
            'payment.callback',
            'paystack.webhook',
            'logout'
        ];
    
        if ($request->routeIs($exemptRoutes)) {
            return $next($request);
        }
    
        // Get the subscription instance
        $subscription = $organization->activeSubscription;

        // Allow access if free plan exists (even if technically not "active")
        if (!$subscription && $organization->current_subscription?->plan?->is_free) {
            return $next($request);
        }
        
        if (!$subscription) {
            if (!$organization->isOnTrial()) {
                return redirect()->route('examiner.subscription.plans')
                    ->with('error', 'You need an active subscription to access this feature');
            }
        }
    
        // Check specific feature if provided
        if ($feature && $subscription && !$subscription->hasFeature($feature)) {
            abort(403, 'This feature is not available in your subscription plan');
        }
    
        return $next($request);
    }
}
