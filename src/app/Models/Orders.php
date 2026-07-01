<?php

namespace App\Models;

use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'akad', 'status', 'total_price',
        'dp_amount', 'paid_amount', 'refund_amount', 'shipping_address',
        'note', 'order_date'
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
        return $this->hasMany(OrdersItems::class);
    }

    public function design()
    {
        return $this->hasOne(Designs::class);
    }

    public function payments()
    {
        return $this->hasMany(Payments::class);
    }

    public function refunds()
    {
        return $this->hasMany(Refunds::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shippings::class);
    }
}
