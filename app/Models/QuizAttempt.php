<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'started_at',
        'completed_at',
        'time_spent',
        'score',
        'max_score',
        'percentage',
        'is_passed',
        'answers',
        'grading_data',
        'feedback' ,
        'questions_data',
    ];

    protected $casts = [
        'answers' => 'json',
        'grading_data' => 'json',
        'questions_data' => 'array', // Add this line
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function responses()
    {
        return $this->hasMany(QuestionResponse::class, 'attempt_id');
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    public function getQuestionsAttribute()
    {
        return collect(json_decode($this->questions_data, true));
    }
}
