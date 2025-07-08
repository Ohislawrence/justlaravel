<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizGroupItem extends Model
{
    protected $fillable = [
        'quiz_group_id',
        'quiz_id',
        'order',
    ];    //
}
