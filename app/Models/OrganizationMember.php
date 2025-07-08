<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationMember extends Model
{
    //
    protected $fillable = [
        'organization_id',
        'user_id',
        'role',
        'permissions',
    ];
    
}
