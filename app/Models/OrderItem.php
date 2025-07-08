<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'item',
        'price',
        'quantity',
        'subtotal',
        'discount',
        'total',
    ];
}
