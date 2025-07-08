<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionResponse extends Model
{
    protected $fillable = [
        'attempt_id',
        'question_id',
        'answer',
        'file_path',
        'audio_path',
        'video_path',
        'points_earned',
        'is_correct',
        'time_spent',
        'feedback',
        'grading_data',
    ];

    protected $casts = [
        'grading_data' => 'array'
    ];

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function attempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
