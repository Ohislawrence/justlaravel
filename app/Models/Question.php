<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Question extends Model
{
    protected $fillable = [
        'quiz_id',
        'type',
        'question',
        'description',
        'image',
        'audio',
        'video',
        'points',
        'time_limit',
        'order',
        'is_required',
         'options',
        'correct_answers',
        'settings',
        'pools',
        'organization_id',
        'is_ai'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'settings' => 'array',
    ];


    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function pools()
    {
        return $this->belongsToMany(QuestionPool::class, 'question_pool_items')
                    ->withTimestamps();
    }
    
    public function poolItems()
    {
        return $this->hasMany(QuestionPoolItem::class);
    }


    public function responses()
    {
        return $this->hasMany(QuestionResponse::class);
    }

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        // If already a full URL, return as is
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // If starts with /storage/, return as is
        if (str_starts_with($this->image, '/storage/')) {
            return $this->image;
        }

        // Otherwise, prepend storage URL
        return Storage::url($this->image);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
