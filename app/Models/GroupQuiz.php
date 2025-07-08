<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupQuiz extends Model
{
    protected $fillable = [
        'group_id',
        'quiz_id',
    ];
}
