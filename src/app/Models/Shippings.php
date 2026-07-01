<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shippings extends Model
{
    protected $fillable = ['order_id', 'courier', 'tracking_number', 'shipped_at', 'delivered_at'];

    protected function casts(): array
    {
        return [
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }
}
