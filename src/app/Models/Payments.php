<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'payment_method',
        'amount',
        'status',
        'proof_file',
        'verified_by',
        'verified_at',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'type' => PaymentType::class,
            'status' => PaymentStatus::class,
            'payment_method' => PaymentMethod::class,
            'verified_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
