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
        'designation_id',
        'unique_code',
        'last_login_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }
    
}
