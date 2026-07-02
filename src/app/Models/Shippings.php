<?php

namespace App\Models;

use App\Enums\Courier;
use Illuminate\Database\Eloquent\Model;

class Shippings extends Model
{
    protected $fillable = ['order_id', 'courier', 'tracking_number', 'shipped_at', 'delivered_at'];

    protected function casts(): array
    {
        return [
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'courier'=>Courier::class,
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }
}
