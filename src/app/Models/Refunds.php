<?php

namespace App\Models;

use App\Enums\RefundStatus;
use Illuminate\Database\Eloquent\Model;

class Refunds extends Model
{
    protected $fillable = ['order_id', 'amount', 'reason', 'status', 'processed_by', 'processed_at'];

    protected function casts(): array
    {
        return [
            'status' => RefundStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
