<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Services\PaystackService;

class PaymentController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Initialize subscription payment
     */

     public function index()
    {
        $plans = SubscriptionPlan::active()->get();
        $organization = auth()->user()->organizations()
            ->with([
                'current_subscription.plan',
                'activeSubscription.plan'
            ])
            ->first();
        
        // Debug output (temporarily)
        // dd($organization->toArray());
        
        return inertia('Examiner/Subscription/Index', [
            'plans' => $plans,
            'organization' => $organization
        ]);
    }

    public function initializeSubscription(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly'
        ]);
    
        $user = $request->user();
        $organization = $user->organizations()->first();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);
    
        // Don't process free plans
        if ($plan->is_free) {
            return back()->with('error', 'You are already on the free plan');
        }
    
        $data = [
            'email' => $user->email,
            'amount' => $plan->getPriceForBillingCycle($request->billing_cycle) * 100,
            'reference' => 'SUB_'.uniqid(),
            'callback_url' => route('examiner.payment.callback'),
            'metadata' => json_encode([
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
            ])
        ];
    
        $response = $this->paystack->initializeTransaction($data);
    
        if (!$response['status']) {
            return back()->with('error', 'Payment initialization failed: '.$response['message']);
        }
    
        // Store temporary subscription data in session
        $request->session()->put('pending_subscription', [
            'plan_id' => $plan->id,
            'billing_cycle' => $request->billing_cycle,
            'price' => $plan->getPriceForBillingCycle($request->billing_cycle),
            'reference' => $data['reference']
        ]);
    
        // Return an Inertia location visit response
        return inertia()->location($response['data']['authorization_url']);
    }

    /**
     * Payment callback handler
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->query('reference');
        
        // Verify the transaction
        $response = $this->paystack->verifyTransaction($reference);

        if (!$response['status']) {
            return redirect()->route('examiner.subscription.plans')
                ->with('error', 'Payment verification failed: ' . $response['message']);
        }

        // Get pending subscription data from session
        $subscriptionData = $request->session()->pull('pending_subscription');
        
        if (!$subscriptionData || $subscriptionData['reference'] !== $reference) {
            return redirect()->route('examiner.subscription.plans')
                ->with('error', 'Invalid subscription data');
        }

        // Create the subscription
        $subscription = $this->createOrganizationSubscription(
            $response['data']['metadata']['organization_id'],
            $subscriptionData['plan_id'],
            $subscriptionData['billing_cycle'],
            $subscriptionData['price'],
            $response['data']
        );

        return redirect()->route('examiner.subscription.plans')
            ->with('success', 'Subscription activated successfully!');
    }

    protected function createOrganizationSubscription($organizationId, $planId, $billingCycle, $price, $paymentData)
    {
        $subscription = OrganizationSubscription::create([
            'organization_id' => $organizationId,
            'plan_id' => $planId,
            'billing_cycle' => $billingCycle,
            'price' => $price,
            'starts_at' => now(),
            'ends_at' => $this->getEndDate($billingCycle),
            'payment_data' => json_encode($paymentData),
            'paystack_reference' => $paymentData['reference'],
            'is_active' => true,
        ]);

        // Update organization's current subscription
        $organization = Organization::find($organizationId);
        $organization->update(['current_subscription_id' => $subscription->id]);

        return $subscription;
    }

    protected function getEndDate($billingCycle)
    {
        switch ($billingCycle) {
            case 'monthly': return now()->addMonth();
            case 'quarterly': return now()->addMonths(3);
            case 'yearly': return now()->addYear();
            default: return now()->addMonth();
        }
    }
}
