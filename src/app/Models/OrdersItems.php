<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdersItems extends Model
{
    protected $fillable = ['order_id', 'product_variant_id', 'quantity', 'unit_price', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductsVariants::class, 'product_variant_id');
    }
}
