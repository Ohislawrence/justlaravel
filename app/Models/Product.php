<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
    'type',
    'description',
    'price', 
    'currency',
    'is_active',
    'settings',
    ];
    
    public function items()
    {
        return $this->hasMany(ProductItem::class);
    }
    
    public function orders()
    {
        return $this->hasManyThrough(OrderItem::class, ProductItem::class);
    }
}
