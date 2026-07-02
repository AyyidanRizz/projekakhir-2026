<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Orders;
use App\Models\Shippings;
use App\Enums\Courier;

class ShippingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data order yang ada
        $orders = Orders::all();

        foreach ($orders as $order) {
            Shippings::create([
                'order_id' => $order->id,
                'courier' => collect([Courier::JNE, Courier::JNT])->random(), // 👈 Mengisi acak kurir sesuai Enum kamu (contoh JNE / JNT)
                'tracking_number' => 'REG-' . strtoupper(uniqid()),
                'shipped_at' => now(),
                'delivered_at' => null,
            ]);
        }
    }
}