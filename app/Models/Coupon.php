<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_uses',
        'uses',
        'valid_from',
        'valid_until',
        'is_active',
    ];
}
