<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class OrganizationSubscription extends Model
{
    protected $fillable = [
        'organization_id',
        'plan_id',
        'billing_cycle',
        'price',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'is_active',
        'is_trial',
    ];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'cancelled_at'  => 'datetime',
        'is_active'     => 'boolean',
        'is_trial'      => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Subscription Status
    |--------------------------------------------------------------------------
    */
    public function isActive(): bool
    {
        return $this->is_active && Carbon::now()->lt($this->ends_at);
    }

    public function isOnTrial(): bool
    {
        return $this->is_trial && Carbon::now()->lt($this->trial_ends_at);
    }

    /*
    |--------------------------------------------------------------------------
    | Feature Access
    |--------------------------------------------------------------------------
    */
    /**
     * Check if the plan has a feature (boolean or numeric > 0)
     */
    public function hasFeature(string $featureSlug): bool
    {
        $feature = $this->getFeature($featureSlug);
        if (!$feature) {
            return false;
        }

        // Boolean features typically stored as 1/0, numeric features must be > 0
        return (bool) $feature->pivot->value;
    }

    /**
     * Get the numeric/boolean value of a feature from the pivot
     */
    public function getFeatureValue(string $featureSlug)
    {
        $feature = $this->getFeature($featureSlug);
        return $feature?->pivot->value;
    }

    /**
     * Internal helper to fetch a feature once
     */
    protected function getFeature(string $featureSlug)
    {
        if (!$this->relationLoaded('plan') || !$this->plan) {
            $this->load('plan.features');
        }

        return $this->plan
            ? $this->plan->features->firstWhere('slug', $featureSlug)
            : null;
    }
}
