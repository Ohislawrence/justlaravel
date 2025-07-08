<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{

    protected $fillable = [
        'organization_id',
    'name',
    'url',
    'event',
    'is_active',
    'settings',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
