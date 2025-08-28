<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'certificate_seal',
        'office_address',
        'official_phone_number',
        'official_email',
        'official_Whatsapp_contact',
        'created_by',
        'last_updated_by',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_members')
                    ->withTimestamps();
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function designation(): HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function certificateTemplates(): HasMany
    {
        return $this->hasMany(CertificateTemplate::class);
    }

    public function learningPaths(): HasMany
    {
        return $this->hasMany(LearningPath::class);
    }

    public function integrations(): HasMany
    {
        return $this->hasMany(Integration::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function questionPools(): HasMany
    {
        return $this->hasMany(QuestionPool::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function quizGroups(): HasMany
    {
        return $this->hasMany(QuizGroup::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(OrganizationSubscription::class);
    }

    /**
     * The currently active subscription
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(OrganizationSubscription::class)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('ends_at', '>', now())
                      ->orWhereNull('ends_at');
            });
    }

    /**
     * Accessor: $organization->active_subscription
     */
    public function getActiveSubscriptionAttribute()
    {
        return $this->activeSubscription()
                    ->with('plan.features')
                    ->latest()
                    ->first();
    }

    /**
     * Current plan based on active subscription
     */
    public function currentPlan()
    {
        return $this->active_subscription?->plan;
    }

    public function current_subscription()
    {
        return $this->belongsTo(OrganizationSubscription::class, 'current_subscription_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Feature Checks (Delegating to OrganizationSubscription)
    |--------------------------------------------------------------------------
    */

    public function getFeatureValue($featureSlug)
    {
        $subscription = $this->getActiveSubscriptionAttribute();
        if (!$subscription || !$subscription->plan) {
            return null;
        }

        $feature = $subscription->plan->features[$featureSlug];

        return $feature;
    }

    public function hasFeature(string $featureSlug): bool
    {
        return $this->active_subscription?->hasFeature($featureSlug) ?? false;
    }

    public function isOnTrial(): bool
    {
        return $this->active_subscription?->isOnTrial() ?? false;
    }

    /*
    |--------------------------------------------------------------------------
    | Business Logic
    |--------------------------------------------------------------------------
    */

    public function canCreateQuiz(): bool
    {
        $limit = $this->getFeatureValue('quiz_groups_limit'); // Adjust slug if needed
        return $limit === null || $this->quizzes()->count() < $limit;
    }

    public function canCreateQuestionPool(): bool
    {
        $limit = $this->getFeatureValue('question_pool_limit');
        return $limit === null || $this->questionPools()->count() < $limit;
    }

    public function canAddQuestion(): bool
    {
        $limit = $this->getFeatureValue('questions_limit');
        return $limit === null || $this->questions()->count() < $limit;
    }

    public function canAddUser(): bool
    {
        $limit = $this->getFeatureValue('users_limit');
        return $limit === null || $this->members()->count() < $limit;
    }

    public function hasStorageSpace(float $sizeInMB): bool
    {
        $usedSpace = $this->calculateUsedStorage();
        $availableSpace = $this->getFeatureValue('storage_space') ?? 100; // default 100 MB
        return ($usedSpace + $sizeInMB) <= $availableSpace;
    }

    protected function calculateUsedStorage(): float
    {
        // Assuming file_size stored in bytes
        return $this->questions()->sum('file_size') / 1024 / 1024;
    }

    public function latestSubscription()
    {
        return $this->hasOne(OrganizationSubscription::class)
            ->latest('updated_at');
    }

    public function canGenerateAiQuestion(): bool
    {
        // Get the active subscription
        $subscription = $this->latestSubscription()->first();
        if (!$subscription) {
            return false;
        }

        // Feature limit
        $limit = $this->getFeatureValue('ai_question_generation') ?? 0;

        // Count AI questions created in the current billing cycle
        $aiQuestionsThisCycle = $this->questions()
            ->where('is_ai', true)
            ->whereBetween('created_at', [
                $subscription->starts_at,
                $subscription->ends_at,
            ])
            ->count();

        return $aiQuestionsThisCycle < $limit;
    }

    public function canAttemptQuiz(): bool
    {
        $subscription = $this->latestSubscription()->first();

        if (!$subscription) {
            return false;
        }

        // Feature limit from subscription features JSON
        $limit = $this->getFeatureValue('quiz_attempts_limit') ?? 0;
        $attemptsThisCycle = $this->quizAttempts()
        ->whereBetween('created_at', [
            $subscription->starts_at,
            $subscription->ends_at,
        ])
        ->count();

        return $attemptsThisCycle < $limit;
    }

    public function quizAttempts()
    {
        return QuizAttempt::whereHas('quiz', function ($query) {
            $query->where('organization_id', $this->id);
        });
    }

    public function gradingSystems()
    {
        return $this->hasMany(GradingSystem::class);
    }
}
