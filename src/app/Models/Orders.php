<?php

namespace App\Models;

use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected static function booted()
    {
        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(uniqid()) . '-' . time();
        });
    }
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
        'order_date'
    ];

    protected function casts(): array
    {
        return [
            'akad' => Akad::class,
            'status' => OrderStatus::class,
            'order_date' => 'datetime',
        ];
    }

    /**
     * Menghitung total kuantitas item dalam order ini.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Mengecek apakah pesanan memenuhi syarat untuk memilih Akad Istishna (>= 12 pcs).
     */
    public function isEligibleForIstishna(): bool
    {
        return $this->total_quantity >= 12;
    }

    /**
     * Mengatur nilai DP secara otomatis berdasarkan akad yang dipilih.
     * Panggil method ini sebelum order disimpan atau saat checkout selesai hitung harga.
     */
    public function calculatePaymentSchema(): void
    {
        if ($this->akad === \App\Enums\Akad::ISTISHNA) {
            // Jika Istishna, DP adalah 50% dari total harga
            $this->dp_amount = $this->total_price * 0.50;
        } else {
            // Jika Salam, tidak ada DP (harus lunas 100% di awal)
            $this->dp_amount = 0;
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrdersItems::class, 'order_id');
    }

    public function design()
    {
        return $this->hasOne(Designs::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'order_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refunds::class, 'order_id');
    }

    public function shipping()
    {
        return $this->hasOne(Shippings::class, 'order_id');
    }
}
