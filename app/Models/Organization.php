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
}
