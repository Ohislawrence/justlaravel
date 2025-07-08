<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    protected $fillable = [
        'organization_id',
    'title',
    'slug',
    'content',
    'type',
    'is_published',
     'metadata',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
}
