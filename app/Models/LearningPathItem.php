<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningPathItem extends Model
{
    protected $fillable = [
        'learning_path_id',
        'item',
        'order',
        'is_required',
        'completion_threshold',
    ];

    public function item()
    {
        return $this->morphTo();
    }
}
