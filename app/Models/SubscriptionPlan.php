<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'monthly_price',
        'quarterly_price',
        'yearly_price',
        'trial_days',
        'is_active',
        'is_default',
        'description',
    ];

    protected $appends = ['is_free'];

    public function features()
    {
        return $this->belongsToMany(SubscriptionFeature::class, 'plan_feature')
        ->withPivot('value')
        ->using(PlanFeaturePivot::class); 
    }

    public function organizations()
    {
        return $this->hasMany(OrganizationSubscription::class);
    }

    public function getPriceForBillingCycle($cycle)
    {
        return $this->{$cycle.'_price'};
    }

    /**
     * Scope a query to only include active plans.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include default plans.
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Get the features as an accessible attribute
     */
    public function getFeaturesAttribute()
    {
        return $this->features()->get()->mapWithKeys(function ($feature) {
            return [$feature->slug => $feature->pivot->value];
        });
    }

    public function getIsFreeAttribute()
    {
        return $this->monthly_price == 0 && 
            $this->quarterly_price == 0 && 
            $this->yearly_price == 0;
    }


}
