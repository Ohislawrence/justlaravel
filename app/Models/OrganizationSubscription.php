<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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

    protected $dates = [
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'cancelled_at',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function isActive()
    {
        return $this->is_active && Carbon::now()->lt($this->ends_at);
    }

    public function isOnTrial()
    {
        return $this->is_trial && Carbon::now()->lt($this->trial_ends_at);
    }

    public function hasFeature($featureSlug)
    {
        if (!$this->plan) {
            return false;
        }
    
        $feature = $this->plan->features()->where('slug', $featureSlug)->first();
        
        if (!$feature) {
            return false;
        }
    
        return (bool) $feature->pivot->value;
    }

    public function getFeatureValue($featureSlug)
    {
        if (!$this->plan) {
            return null;
        }
    
        $feature = $this->plan->features()->where('slug', $featureSlug)->first();
        
        if (!$feature) {
            return null;
        }
    
        return $feature->pivot->value;
    }
}
