<?php

namespace App\Filament\Admin\Resources\OrdersResource\Pages;

use App\Filament\Admin\Resources\OrdersResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrders extends CreateRecord
{
    protected static string $resource = OrdersResource::class;

    /**
     * Intersep pembuatan data untuk memisahkan data relasi HasOne
     */
    protected function handleRecordCreation(array $data): Model
    {
        // 1. Ekstrak data relasi yang diisi dari form Orders
        $designData = $data['design'] ?? null;
        $paymentData = $data['payment'] ?? null;
        $shippingData = $data['shipping'] ?? null;

        // 2. Bersihkan array $data agar tidak memicu error "column not found" di tabel orders
        unset($data['design'], $data['payment'], $data['shipping']);

        // 3. Simpan data Order Utama
        $order = static::getModel()::create($data);

        // 4. OTOMATIS MASUK KE DASHBOARD DESIGN
        if ($designData && !empty($designData['file_path'])) {
            $order->design()->create([
                'file_path' => $designData['file_path'],
                'status' => \App\Enums\DesignStatus::PENDING, 
            ]);
        }

        // 5. PERBAIKAN DI SINI: Gunakan nilai backing value Enum yang tepat ('full' atau 'dp')
        if ($paymentData) {
            // Ambil string murni dari akad karena $order->akad berupa objek Enum setelah di-cast
            $akadValue = is_object($order->akad) ? $order->akad->value : $order->akad;
            
            $order->payment()->create([
                'type' => $akadValue === 'istishna' ? 'dp' : 'full', // Menggunakan 'full' sebagai ganti 'lunas'
                'payment_method' => $paymentData['payment_method'],
                'amount' => $paymentData['amount'] ?? 0,
                'status' => $paymentData['payment_status'] ?? 'pending',
            ]);
        }

        // 6. OTOMATIS MASUK KE DASHBOARD SHIPPING
        if ($shippingData) {
            $order->shipping()->create([
                'courier' => $shippingData['courier'],
                'tracking_number' => $shippingData['tracking_number'] ?? null,
                'shipping_address' => $shippingData['shipping_address'] ?? $data['shipping_address'] ?? '',
                'shipping_status' => 'pending',
            ]);
        }

        return $order;
    }
}