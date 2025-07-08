<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'description',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_members')
                    ->withTimestamps();
    }

    public function quizzes(): BelongsToMany
    {
        return $this->belongsToMany(Quiz::class, 'group_quizzes')
                    ->withTimestamps();
    }
    
}
