<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Organization;
use App\Models\OrganizationSubscription;
use App\Models\SubscriptionFeature;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    // List all subscription plans (for management)
    public function index()
    {
        // Get all subscription plans with their features
        $plans = SubscriptionPlan::with('features')->get();
        
        // Get all organizations with their active subscriptions
        $organizations = Organization::with(['subscriptions' => function($query) {
            $query->where('is_active', true)
                  ->where('ends_at', '>', now())
                  ->latest()
                  ->with('plan');
        }])->paginate(15);

        return inertia('Landlord/Subscriptions/Index', [
            'plans' => $plans,
            'organizations' => $organizations->map(function($org) {
                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'active_subscription' => $org->subscriptions->first() ? [
                        'id' => $org->subscriptions->first()->id,
                        'plan' => $org->subscriptions->first()->plan,
                        'billing_cycle' => $org->subscriptions->first()->billing_cycle,
                        'price' => $org->subscriptions->first()->price,
                        'ends_at' => $org->subscriptions->first()->ends_at,
                        'is_active' => $org->subscriptions->first()->is_active,
                    ] : null
                ];
            }),
        ]);
    }

    // Show form to create/edit a plan
    public function create()
    {
        return inertia('Landlord/Subscriptions/Create');
    }

    // Store a new subscription plan
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'monthly_price' => 'required|numeric|min:0',
            'quarterly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);
    
        // Generate slug from name
        $slug = Str::slug($request->name);
        
        // Ensure slug is unique
        $count = SubscriptionPlan::where('slug', $slug)->count();
        if ($count > 0) {
            $slug = $slug . '-' . ($count + 1);
        }
    
        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'slug' => $slug,
            'monthly_price' => $request->monthly_price,
            'quarterly_price' => $request->quarterly_price,
            'yearly_price' => $request->yearly_price,
            'trial_days' => $request->trial_days,
            'is_active' => $request->is_active ?? true,
        ]);
    
        return redirect()->route('landlord.subscriptions.index')
            ->with('success', 'Subscription plan created successfully');
    }

    public function showAssignForm(Organization $organization)
    {
        $plans = SubscriptionPlan::active()->get();
        
        // Eager load the active subscription with its plan
        $organization->load(['subscriptions' => function($query) {
            $query->where('is_active', true)
                  ->where('ends_at', '>', now())
                  ->with('plan')
                  ->latest()
                  ->limit(1);
        }]);
        
        return inertia('Landlord/Subscriptions/Assign', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'active_subscription' => $organization->subscriptions->first() ? [
                    'id' => $organization->subscriptions->first()->id,
                    'plan' => $organization->subscriptions->first()->plan,
                    'billing_cycle' => $organization->subscriptions->first()->billing_cycle,
                    'price' => $organization->subscriptions->first()->price,
                    'ends_at' => $organization->subscriptions->first()->ends_at,
                    'is_active' => $organization->subscriptions->first()->is_active,
                ] : null
            ],
            'plans' => $plans,
        ]);
    }

    // Assign a plan to an organization
    public function assign(Request $request, Organization $organization)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,quarterly,yearly',
        ]);
    
        try {
            $plan = SubscriptionPlan::findOrFail($request->plan_id);
            
            // End current subscription if exists
            if ($organization->activeSubscription) {
                $organization->activeSubscription->update(['is_active' => false]);
            }
            
            // Calculate dates and price
            $price = $plan->getPriceForBillingCycle($request->billing_cycle);
            $startDate = Carbon::now();
            $endDate = $this->calculateEndDate($startDate, $request->billing_cycle);
            
            // Create new subscription
            $organization->subscriptions()->create([
                'plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'price' => $price,
                'starts_at' => $startDate,
                'ends_at' => $endDate,
                'is_active' => true,
                'is_trial' => false,
            ]);
    
            return redirect()->route('landlord.subscriptions.index')
                ->with('success', 'Subscription assigned successfully');
    
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign subscription: ' . $e->getMessage());
        }
    }



    // Edit a subscription plan
    public function edit($plan)
    {
        $features = SubscriptionFeature::all();
        $plandata = SubscriptionPlan::where('id', $plan)->first();
        return Inertia::render('Landlord/Subscriptions/Edit', [
            'plan' => $plandata->load('features'),
            'features' => $features,
        ]);
    }

    // Update a subscription plan
    public function update(Request $request, $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'quarterly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'trial_days' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'features' => 'nullable|array',
        ]);

        $plan = SubscriptionPlan::where('id', $plan)->first();

        // Update plan details
        $plan->update($request->only([
            'name',
            'description',
            'monthly_price',
            'quarterly_price',
            'yearly_price',
            'trial_days',
            'is_active',
        ]));

        // Sync features
        if ($request->has('features')) {
            $featuresToSync = [];
            foreach ($request->features as $slug => $value) {
                $feature = SubscriptionFeature::where('slug', $slug)->first();
                if ($feature) {
                    $featuresToSync[$feature->id] = ['value' => $value];
                }
            }
            $plan->features()->sync($featuresToSync);
        }

        return redirect()->route('landlord.subscriptions.index')
            ->with('success', 'Subscription plan updated successfully');
    }

    protected function calculateEndDate(Carbon $startDate, string $billingCycle): Carbon
    {
        $endDate = $startDate->copy(); // Clone the start date

        switch ($billingCycle) {
            case 'monthly':
                return $endDate->addMonth();
            case 'quarterly':
                return $endDate->addMonths(3);
            case 'yearly':
                return $endDate->addYear();
            default:
                return $endDate->addMonth();
        }
    }
}