<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PlanFeaturePivot extends Pivot
{
    protected $table = 'plan_feature';
}
