<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model 
{
    protected $fillable = [
    'name',
    'slug',
    'description',
    'logo',
    'website',
    'industry',
    'settings',
    'is_active',
    ];

    public function members()
    {
        return $this->belongsToMany(User::class, 'organization_members')
                    ->withTimestamps();
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }

    public function designation()
    {
        return $this->hasMany(Designation::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function certificateTemplates()
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function learningPaths()
    {
        return $this->hasMany(LearningPath::class);
    }

    public function integrations()
    {
        return $this->hasMany(Integration::class);
    }

    public function webhooks()
    {
        return $this->hasMany(Webhook::class);
    }

    public function questionPools()
    {
        return $this->hasMany(QuestionPool::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(OrganizationSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(OrganizationSubscription::class)
            ->where('is_active', true)
            ->where(function($query) {
                $query->where('ends_at', '>', now())
                    ->orWhereNull('ends_at');
            })
            ->latest()
            ->with('plan'); // Eager load the plan
    }

    

    public function isOnTrial()
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->isOnTrial();
    }

    public function canUseAI()
    {
        $subscription = $this->activeSubscription();
        return $subscription && $subscription->hasFeature('ai_question_generation');
    }

    public function getQuizAttemptsLimit()
    {
        $subscription = $this->activeSubscription();
        return $subscription ? $subscription->getFeatureValue('quiz_attempts_limit') : 0;
    }

    public function currentSubscription()
    {
        return $this->belongsTo(OrganizationSubscription::class, 'current_subscription_id');
    }

    public function current_subscription()
    {
        return $this->belongsTo(OrganizationSubscription::class, 'current_subscription_id');
    }

    public function canCreateQuiz()
    {
        if ($this->current_subscription?->plan?->is_free) {
            $quizzesCount = $this->quizzes()->count();
            $quizzesLimit = $this->getFeatureValue('quizzes');
            return $quizzesCount < $quizzesLimit;
        }
        
        return true; // Paid plans have no limits
    }

    
}
