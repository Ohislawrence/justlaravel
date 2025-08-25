<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProctoringViolation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_attempt_id',
        'violation_type',
        'violation_data',
        'violation_time'
    ];

    protected $casts = [
        'violation_data' => 'array',
        'violation_time' => 'datetime'
    ];

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }
}
