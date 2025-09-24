<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    use HasFactory;

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
        'fingerprint',
        'fingerprint_recorded_at',
        'fingerprint_components',
        'status',
        'proctoring_data',
        'violation_count',
        'guest_id',
        'guest_email',
        'guest_name',
    ];

    protected $casts = [
        'answers' => 'json',
        'grading_data' => 'json',
        'questions_data' => 'array', // Add this line
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'fingerprint_components' => 'array',
    ];

    public function proctoringViolations(): HasMany
    {
        return $this->hasMany(ProctoringViolation::class);
    }

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
