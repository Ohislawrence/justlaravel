<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
    'name',
    'token', 
    'abilities',
    'last_used_at',
    'expires_at',
    ];
    
}
