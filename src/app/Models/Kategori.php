<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kategori extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'images',
        'is_active',
    ];

    public function produks() {
        return $this->hasMany(Produk::class);
    }
}
