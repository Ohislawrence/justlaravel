<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningPathProgress extends Model
{
    //
    protected $fillable = [
        'learning_path_id',
        'user_id',
        'progress_percentage',
        'is_completed',
        'completed_at',
    ];
    
}
