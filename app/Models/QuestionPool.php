<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionPool extends Model
{
    protected $fillable = [
        'quiz_id',
        'name',
        'description',
        'questions_to_show',
        'is_global',
        'organization_id',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'question_pool_items')
                    ->withPivot(['question_pool_id', 'question_id', 'created_at', 'updated_at'])
                    ->select('questions.id', 'questions.question', 'questions.description');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
}
