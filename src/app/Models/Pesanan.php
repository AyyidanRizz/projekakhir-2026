<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'metode_bayar',
        'status_bayar',
        'status',
        'mata_uang',
        'ongkir',
        'ekspedisi',
        'catatan',
    ];

    public function users(){
        return $this->belongsTo(User::class);
    }

    public function items(){
        return $this->hasMany(PesananItem::class);
    }

    public function alamat(){
        return $this->hasOne(Alamat::class);
    }
}
