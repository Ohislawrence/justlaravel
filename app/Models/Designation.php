<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $fillable = ['organization_id', 'name','last_updated_by','created_by'];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
