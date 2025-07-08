<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizUserAssignment extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
    ];
}
