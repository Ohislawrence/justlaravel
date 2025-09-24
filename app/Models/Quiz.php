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
        'survey_thank_you_message',
        'enable_certificates',
        'certificate_template_id',
        'certificate_pass_percentage',
        'certificate_expiry_days',
        'created_by',
        'last_updated_by',
        'grading_system_id',
        'require_guest_info',
        'guest_info_required'
        
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
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'enable_certificates' => 'boolean'
    ];

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

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
        return $this->belongsToMany(QuestionPool::class, 'quiz_question_pool')
            ->withPivot('questions_to_show')
            ->withTimestamps()->with('questions')
            ->withCount('questions'); 
    }

    public function pools()
    {
        return $this->belongsToMany(QuestionPool::class, 'quiz_question_pool')
            ->withPivot('questions_to_show'); 
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

    public function getTotalQuestionsAttribute()
    {
        // Count direct questions
        $directQuestions = $this->questions()->count();
        
        // Count questions from pools
        $poolQuestions = $this->questionPools->sum(function($pool) {
            return min($pool->questions_count, $pool->pivot->questions_to_show);
        });
        
        return $directQuestions + $poolQuestions;
    }

    public function quizGroups()
    {
        return $this->belongsToMany(QuizGroup::class, 'quiz_group_items')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function scopeInGroup($query, $groupId)
    {
        return $query->whereHas('quizGroups', function($q) use ($groupId) {
            $q->where('quiz_group_id', $groupId);
        });
    }

    public function gradingSystem()
    {
        return $this->belongsTo(GradingSystem::class);
    }

    
    /**
    public function products()
    {
        return $this->morphMany(ProductItem::class, 'item');
    }
         */
}
