<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'base_price',
        'image',
        'is_active'
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductsVariants::class, 'product_id');
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrdersItems::class, ProductsVariants::class);
    }
}
