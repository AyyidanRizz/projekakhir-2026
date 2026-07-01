<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsVariants extends Model
{
    protected $fillable = ['product_id', 'size', 'material', 'price', 'stock'];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrdersItems::class);
    }
}
