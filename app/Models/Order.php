<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable =[
        'user_id',
        'order_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'currency',
        'status',
        'coupon_id',
        'payment_method',
        'payment_id',
        'metadata',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
