<?php

namespace App\Models;

use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'akad',
        'status',
        'total_price',
        'dp_amount',
        'paid_amount',
        'refund_amount',
        'shipping_address',
        'note',
        'order_date',
    ];

    protected $casts = [
        'akad' => Akad::class,
        'status' => OrderStatus::class,
        'order_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            // Generate order number otomatis jika belum ada
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(uniqid()) . '-' . time();
            }
        });
    }

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke items
    public function items()
    {
        return $this->hasMany(OrdersItems::class, 'order_id');
    }

    // Relasi ke design
    public function design()
    {
        return $this->hasOne(Designs::class, 'order_id');
    }

    // Relasi ke payments
    public function payment()
    {
        return $this->hasMany(Payments::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'order_id');
    }

    // Relasi ke refunds
    public function refund()
    {
        return $this->hasMany(Refunds::class, 'order_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refunds::class, 'order_id');
    }

    // Relasi ke shipping
    public function shipping()
    {
        return $this->hasOne(Shippings::class, 'order_id');
    }

    /**
     * Mengembalikan stok semua item dalam order (saat order dibatalkan/ditolak)
     */
    public function restoreStock(): void
    {
        foreach ($this->items as $item) {
            $variant = ProductsVariants::find($item->product_variant_id);
            if ($variant) {
                $variant->increment('stock', $item->quantity);
            }
        }
    }
}