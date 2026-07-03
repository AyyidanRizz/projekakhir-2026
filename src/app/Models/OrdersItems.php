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

    /**
     * Logika Siklus Hidup Model (Gantikan Fungsi Observer)
     */
    protected static function booted(): void
    {
        // 1. Ketika item pesanan baru dibuat -> Kurangi Stok Varian
        static::created(function ($ordersItems) {
            $variant = ProductsVariants::find($ordersItems->product_variant_id);
            if ($variant) {
                $variant->decrement('stock', $ordersItems->quantity);
            }
        });

        // 2. Ketika jumlah item pesanan diubah -> Sesuaikan Stok Varian
        static::updated(function ($ordersItems) {
            if ($ordersItems->isDirty('quantity')) {
                $oldQuantity = $ordersItems->getOriginal('quantity');
                $newQuantity = $ordersItems->quantity;
                $diff = $newQuantity - $oldQuantity;

                $variant = ProductsVariants::find($ordersItems->product_variant_id);
                if ($variant) {
                    if ($diff > 0) {
                        $variant->decrement('stock', $diff);
                    } else {
                        $variant->increment('stock', abs($diff));
                    }
                }
            }
        });

        // 3. Ketika item pesanan dihapus -> Kembalikan Stok Varian
        static::deleted(function ($ordersItems) {
            $variant = ProductsVariants::find($ordersItems->product_variant_id);
            if ($variant) {
                $variant->increment('stock', $ordersItems->quantity);
            }
        });
    }

    /**
     * Relasi Model
     */
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
        return $this->belongsTo(Products::class, 'product_id'); 
    }
}