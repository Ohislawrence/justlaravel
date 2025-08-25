<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'content',
        'settings',
        'is_default',
        'organization_id',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_default' => 'boolean'
    ];
    
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
