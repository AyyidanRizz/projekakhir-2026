<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductsVariants extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'material',
        'price',
        'stock',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }

    // Scope untuk menampilkan varian yang tersedia (stock > 0)
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    // Scope untuk menampilkan varian dengan stok tertentu
    public function scopeInStock(Builder $query, int $minStock = 1): Builder
    {
        return $query->where('stock', '>=', $minStock);
    }
}