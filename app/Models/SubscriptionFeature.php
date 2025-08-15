<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubscriptionFeature extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Plans that include this feature
     */
    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_feature')
                    ->withPivot('value')
                    ->using(PlanFeaturePivot::class);
    }
}
