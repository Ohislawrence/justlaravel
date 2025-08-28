<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingSystem extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'type',
        'grade_ranges',
        'description',
        'is_default',
        'last_updated_by',
        'created_by'
    ];

    protected $casts = [
        'grade_ranges' => 'array',
        'is_default' => 'boolean'
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function calculateGrade($percentage)
    {
        foreach ($this->grade_ranges as $range) {
            if ($percentage >= $range['min'] && $percentage <= $range['max']) {
                return $range;
            }
        }
        
        return null;
    }

    public function scopeDefaultSystems($query)
    {
        return $query->where('is_default', true);
    }
}
