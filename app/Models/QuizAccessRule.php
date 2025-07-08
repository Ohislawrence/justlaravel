<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAccessRule extends Model
{
    protected $fillable = [
        'quiz_id',
    'type',
    'value',
    'action',
    ];
    
}
