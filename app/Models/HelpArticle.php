<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HelpArticle extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'category_id', 
        'views', 'is_featured', 'is_published'
    ];

    public function category()
    {
        return $this->belongsTo(HelpCategory::class, 'category_id'); 
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include featured articles.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
