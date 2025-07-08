<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningPath extends Model
{
    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'image',
        'is_published',
        'is_sequential',
        'completion_threshold',
    ];


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function items()
    {
        return $this->hasMany(LearningPathItem::class);
    }

    public function userProgress()
    {
        return $this->hasMany(LearningPathProgress::class);
    }
}
