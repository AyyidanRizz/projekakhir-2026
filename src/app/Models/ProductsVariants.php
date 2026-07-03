<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductsVariants extends Model
{
    protected $fillable = ['product_id', 'size', 'material', 'price', 'stock'];

        /**
     * Scope untuk hanya menampilkan varian yang tersedia (stock > 0)
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope untuk menampilkan varian dengan stok tertentu
     */
    public function scopeInStock($query, $minStock = 1)
    {
        return $query->where('stock', '>=', $minStock);
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrdersItems::class);
    }
}
