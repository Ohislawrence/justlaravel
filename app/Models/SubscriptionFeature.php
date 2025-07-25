<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function plans()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_feature')
                    ->withPivot('value')
                    ->using(PlanFeaturePivot::class);
    }
}
