<?php

namespace App\Observers;

use App\Models\OrdersItems;
use App\Models\ProductsVariants;

class OrdersItemsObserver
{
    /**
     * Handle the OrdersItems "created" event.
     */
    public function created(OrdersItems $ordersItems): void
    {
        $variant = ProductsVariants::find($ordersItems->product_variant_id);
        if ($variant) {
            $variant->decrement('stock', $ordersItems->quantity);
        }
    }

    /**
     * Handle the OrdersItems "updated" event.
     * Jika quantity berubah, sesuaikan stok.
     */
    public function updated(OrdersItems $ordersItems): void
    {
        if ($ordersItems->wasChanged('quantity')) {
            $oldQuantity = $ordersItems->getOriginal('quantity');
            $newQuantity = $ordersItems->quantity;
            $diff = $newQuantity - $oldQuantity;

            $variant = ProductsVariants::find($ordersItems->product_variant_id);
            if ($variant) {
                if ($diff > 0) {
                    $variant->decrement('stock', $diff);
                } else {
                    $variant->increment('stock', abs($diff));
                }
            }
        }
    }

    /**
     * Handle the OrdersItems "deleted" event.
     */
    public function deleted(OrdersItems $ordersItems): void
    {
        $variant = ProductsVariants::find($ordersItems->product_variant_id);
        if ($variant) {
            $variant->increment('stock', $ordersItems->quantity);
        }
    }
}