<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'order'];

    public function articles()
    {
        return $this->hasMany(HelpArticle::class, 'category_id'); 
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
