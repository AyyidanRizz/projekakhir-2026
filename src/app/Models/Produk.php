<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_id',
        'name',
        'images',
        'deskripsi',
        'harga',
        'is_active',
        'is_featured',
        'jumlah_stok',
        'sale',
    ];

    protected $casts = [
        'images'=>'array',
    ];

    public function kategori(){
        return $this->BelongsTo(Kategori::class);
    }

    public function pesananItem(){
        return $this->hasMany(PesananItem::class);
    }
}
