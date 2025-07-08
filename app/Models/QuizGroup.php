<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_group_items');
    }
}
