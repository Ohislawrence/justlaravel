<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    protected $casts = [
        'is_active'  => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $appends = ['is_free'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionFeature::class, 'plan_feature')
                    ->withPivot('value')
                    ->using(PlanFeaturePivot::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(OrganizationSubscription::class, 'plan_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsFreeAttribute(): bool
    {
        return ($this->monthly_price == 0) &&
               ($this->quarterly_price == 0) &&
               ($this->yearly_price == 0);
    }

    /**
     * Return features as an array keyed by slug => value
     */
    public function getFeaturesAttribute()
    {
        $features = $this->relationLoaded('features')
            ? $this->getRelation('features')
            : $this->features()->get();
    
        return $features->mapWithKeys(fn($feature) => [
            $feature->slug => $feature->pivot->value
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function getPriceForBillingCycle(string $cycle)
    {
        return $this->{$cycle . '_price'} ?? null;
    }

    public function getFeatureValue(string $featureSlug)
    {
        if (!$this->relationLoaded('features')) {
            $this->load('features');
        }

        $feature = $this->features->firstWhere('slug', $featureSlug);
        return $feature?->pivot->value;
    }

    public function hasFeature(string $featureSlug): bool
    {
        $value = $this->getFeatureValue($featureSlug);
        return !empty($value) && $value !== '0';
    }

    /*
    |--------------------------------------------------------------------------
    | Specific Feature Helpers
    |--------------------------------------------------------------------------
    */

    public function canUseAI(): bool
    {
        return (bool) $this->getFeatureValue('ai_question_generation');
    }

    public function getQuizAttemptsLimit()
    {
        return $this->getFeatureValue('quiz_attempts_limit');
    }

    public function getQuestionPoolLimit()
    {
        return $this->getFeatureValue('question_pool_limit');
    }

    public function getQuestionsLimit()
    {
        return $this->getFeatureValue('questions_limit');
    }

    public function getUsersLimit()
    {
        return $this->getFeatureValue('users_limit');
    }

    public function getStorageSpace()
    {
        return $this->getFeatureValue('storage_space');
    }
}
