<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'name',
        'no_telfon',
        'detail_jalan',
        'kota',
        'kode_pos',
    ];

    public function pesanan(){
        return $this->belongsTo(Pesanan::class);
    }
}
