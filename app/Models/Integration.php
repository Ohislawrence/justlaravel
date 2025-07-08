<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Integration extends Model
{

    protected $fillable = [
        'organization_id',
    'service',
    'name',
    'is_active',
    'credentials',
    'settings',
    ];


    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
}
