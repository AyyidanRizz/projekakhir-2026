<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Orders;
use App\Models\Payments;
use App\Enums\PaymentMethod;

class PaymentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data order yang ada
        $orders = Orders::all();

        foreach ($orders as $order) {
            Payments::create([
                'order_id' => $order->id,
                'type' => 'full', // atau sesuaikan dengan kebutuhan logic akad
                'payment_method' => collect([PaymentMethod::VIRTUAL_ACCOUNT, PaymentMethod::QRIS])->random(), // 👈 Mengisi acak antara VA / QRIS
                'amount' => $order->total_price,
                'status' => 'pending',
                'proof_file' => null,
                'verified_by' => null,
                'verified_at' => null,
                'notes' => 'Pembayaran dummy via seeder.',
            ]);
        }
    }
}