<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdersItems extends Model
{
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductsVariants::class, 'product_variant_id');
    }

    public function product(): BelongsTo
    {
        // Parameter kedua adalah foreign key di tabel items Anda (misal: 'product_id')
        // Parameter ketiga adalah primary key di tabel products (misal: 'id')
        return $this->belongsTo(Products::class, 'product_id'); 
    }
}
