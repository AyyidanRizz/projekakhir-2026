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
