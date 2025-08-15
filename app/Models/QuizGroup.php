<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'parent_id',
        'organization_id'
    ];

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_group_items')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(QuizGroup::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(QuizGroup::class, 'parent_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function scopeRootGroups($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPathAttribute()
    {
        $path = [];
        $group = $this;
        
        while ($group) {
            array_unshift($path, $group);
            $group = $group->parent;
        }
        
        return $path;
    }

    public function getFullNameAttribute()
    {
        return collect($this->path)->pluck('name')->join(' > ');
    }

 
}
