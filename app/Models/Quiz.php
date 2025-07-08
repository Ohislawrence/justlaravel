<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'organization_id',
        'user_id',
        'title',
        'slug',
        'description',
        'instructions',
        'quiz_type',
        'industry',
        'is_published',
        'is_public',
        'randomize_questions',
        'randomize_answers',
        'show_correct_answers',
        'show_leaderboard',
        'enable_discussions',
        'max_attempts',
        'max_participants',
        'time_limit',
       'passing_score',
        'starts_at',
       'ends_at',
        'settings',
        'share_token',
    ];
    protected $casts = [
        'settings' => 'array',
        'is_published' => 'boolean',
        'is_public' => 'boolean',
        'randomize_questions' => 'boolean',
        'randomize_answers' => 'boolean',
        'show_correct_answers' => 'boolean',
        'show_leaderboard' => 'boolean',
        'enable_discussions' => 'boolean',
    ];



    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function questions()
    {
        return $this->hasMany(Question::class);
    }
    
    public function questionPools()
    {
        return $this->hasMany(QuestionPool::class);
    }
    
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
    
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }
    
    public function accessRules()
    {
        return $this->hasMany(QuizAccessRule::class);
    }
    
    public function quizGroups()
    {
        return $this->belongsToMany(QuizGroup::class, 'quiz_group_items');
    }
    
    public function learningPathItems()
    {
        return $this->morphMany(LearningPathItem::class, 'item');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'quiz_attempts');
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'quiz_user_assignments');
    }

    

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_quizzes')
                    ->withTimestamps();
    }

    
    /**
    public function products()
    {
        return $this->morphMany(ProductItem::class, 'item');
    }
         */
}
