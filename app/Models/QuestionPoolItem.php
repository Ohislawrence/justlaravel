<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionPoolItem extends Model
{
    protected $fillable = [
        'question_pool_id',
        'question_id',
    ];
}
