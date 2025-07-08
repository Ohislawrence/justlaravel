<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'organization_id',
    'name',
    'subject',
    'content',
    'event',
    'is_active',
    ];
    
}
