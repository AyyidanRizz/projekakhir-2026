<?php

namespace App\Models;

use App\Enums\DesignStatus;
use Illuminate\Database\Eloquent\Model;

class Designs extends Model
{
    protected $fillable = ['order_id', 'file_path', 'status', 'rejection_reason', 'uploaded_at'];

    protected function casts(): array
    {
        return [
            'status' => DesignStatus::class,
            'uploaded_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
}
