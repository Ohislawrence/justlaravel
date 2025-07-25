<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PaystackWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Verify the event is from Paystack
        if (!$this->verifyPaystackSignature($request)) {
            Log::error('Invalid Paystack webhook signature', ['ip' => $request->ip()]);
            abort(401, 'Invalid signature');
        }

        $event = $request->event;
        $data = $request->data;

        Log::info("Paystack Webhook: {$event}", ['data' => $data]);

        switch ($event) {
            case 'charge.success':
                $this->handleRecurringCharge($data);
                break;
                
            case 'subscription.create':
                $this->handleSubscriptionCreated($data);
                break;
                
            case 'subscription.disable':
                $this->handleSubscriptionDisabled($data);
                break;
                
            case 'invoice.create':
                $this->handleInvoiceCreated($data);
                break;
                
            case 'invoice.update':
                $this->handleInvoiceUpdated($data);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    protected function verifyPaystackSignature(Request $request): bool
    {
        $computedSignature = hash_hmac(
            'sha512', 
            $request->getContent(), 
            config('services.paystack.secret_key')
        );

        return hash_equals($request->header('x-paystack-signature'), $computedSignature);
    }

    protected function handleRecurringCharge(array $data)
    {
        // Get the subscription from Paystack reference
        $subscription = OrganizationSubscription::where('paystack_subscription_code', $data['subscription']['subscription_code'])
            ->orWhere('payment_reference', $data['reference'])
            ->first();

        if (!$subscription) {
            Log::error('Subscription not found for recurring charge', ['data' => $data]);
            return;
        }

        $organization = $subscription->organization;
        $plan = $subscription->plan;

        // Check if this is a renewal payment
        if ($subscription->ends_at->isFuture()) {
            Log::info('Early renewal payment received', ['subscription_id' => $subscription->id]);
            return;
        }

        // Update subscription dates
        $subscription->update([
            'starts_at' => $subscription->ends_at,
            'ends_at' => $this->calculateNextBillingDate(
                $subscription->ends_at,
                $subscription->billing_cycle
            ),
            'last_payment_date' => now(),
            'last_payment_amount' => $data['amount'] / 100, // Convert from kobo
            'payment_data' => json_encode($data),
        ]);

        Log::info('Subscription renewed successfully', [
            'organization_id' => $organization->id,
            'subscription_id' => $subscription->id
        ]);
    }

    protected function handleSubscriptionCreated(array $data)
    {
        $organization = Organization::where('email', $data['customer']['email'])
            ->orWhere('id', $data['metadata']['organization_id'] ?? null)
            ->first();

        if (!$organization) {
            Log::error('Organization not found for subscription', ['data' => $data]);
            return;
        }

        $plan = SubscriptionPlan::where('paystack_plan_code', $data['plan']['plan_code'])
            ->orWhere('id', $data['metadata']['plan_id'] ?? null)
            ->first();

        if (!$plan) {
            Log::error('Plan not found for subscription', ['data' => $data]);
            return;
        }

        // Determine billing cycle from plan interval
        $billingCycle = $this->mapPaystackIntervalToCycle($data['plan']['interval']);

        // Create or update the subscription
        $subscription = OrganizationSubscription::updateOrCreate(
            ['paystack_subscription_code' => $data['subscription_code']],
            [
                'organization_id' => $organization->id,
                'plan_id' => $plan->id,
                'billing_cycle' => $billingCycle,
                'price' => $data['plan']['amount'] / 100,
                'starts_at' => Carbon::parse($data['createdAt']),
                'ends_at' => $this->calculateNextBillingDate(
                    Carbon::parse($data['createdAt']),
                    $billingCycle
                ),
                'paystack_customer_code' => $data['customer']['customer_code'],
                'paystack_plan_code' => $data['plan']['plan_code'],
                'paystack_subscription_code' => $data['subscription_code'],
                'payment_data' => json_encode($data),
                'is_active' => true,
            ]
        );

        // Update organization's current subscription
        $organization->update(['current_subscription_id' => $subscription->id]);

        Log::info('Subscription created/updated successfully', [
            'organization_id' => $organization->id,
            'subscription_id' => $subscription->id
        ]);
    }

    protected function handleSubscriptionDisabled(array $data)
    {
        $subscription = OrganizationSubscription::where('paystack_subscription_code', $data['subscription_code'])
            ->first();

        if (!$subscription) {
            Log::error('Subscription not found for disable event', ['data' => $data]);
            return;
        }

        $subscription->update([
            'is_active' => false,
            'cancelled_at' => now(),
            'ends_at' => Carbon::parse($data['next_payment_date']), // Last active date
        ]);

        Log::info('Subscription disabled', [
            'subscription_id' => $subscription->id,
            'reason' => $data['reason'] ?? 'Unknown'
        ]);
    }

    protected function handleInvoiceCreated(array $data)
    {
        // You can implement invoice tracking if needed
        Log::info('Invoice created', ['invoice_id' => $data['id']]);
    }

    protected function handleInvoiceUpdated(array $data)
    {
        // Handle invoice updates (e.g., payment status changes)
        Log::info('Invoice updated', [
            'invoice_id' => $data['id'],
            'status' => $data['status']
        ]);
    }

    protected function calculateNextBillingDate(Carbon $startDate, string $billingCycle): Carbon
    {
        return match ($billingCycle) {
            'monthly' => $startDate->addMonth(),
            'quarterly' => $startDate->addMonths(3),
            'yearly' => $startDate->addYear(),
            default => $startDate->addMonth(),
        };
    }

    protected function mapPaystackIntervalToCycle(string $interval): string
    {
        return match ($interval) {
            'monthly' => 'monthly',
            'quarterly' => 'quarterly',
            'biannually' => 'biannually',
            'annually' => 'yearly',
            default => 'monthly',
        };
    }
}
