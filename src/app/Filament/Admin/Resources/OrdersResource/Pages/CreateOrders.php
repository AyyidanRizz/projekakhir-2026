<?php

namespace App\Filament\Admin\Resources\OrdersResource\Pages;

use App\Filament\Admin\Resources\OrdersResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateOrders extends CreateRecord
{
    protected static string $resource = OrdersResource::class;

    /**
     * Langkah 1: Memanipulasi atau menambahkan data form SEBELUM record dibuat
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan total_price sudah terhitung
        $totalPrice = $data['total_price'] ?? 0;
        $akad = $data['akad'] ?? 'salam';

        // Tentukan nominal pembayaran berdasarkan akad (seperti logika updateAkadDanHarga)
        // Jika berupa object Enum (karena Filament v3 terkadang melemparkan enum value/object), amankan nilainya
        $akadValue = is_object($akad) ? $akad->value : $akad;
        $paymentAmount = ($akadValue === 'istishna') ? ($totalPrice * 0.5) : $totalPrice;

        // Ambil data payment yang diinput dari form (jika ada) dan suntikkan 'amount'-nya
        if (!isset($data['payment'])) {
            $data['payment'] = [];
        }
        
        $data['payment']['amount'] = $paymentAmount;
        $data['payment']['user_id'] = $data['user_id'] ?? null; // Samakan user pembeli

        return $data;
    }

    /**
     * Langkah 2: Mengintersep pembuatan data untuk memisahkan data relasi HasOne / HasMany
     */
    protected function handleRecordCreation(array $data): Model
    {
        // 1. Ekstrak data relasi yang diisi dari form Orders
        // Data 'payment' di sini sudah membawa 'amount' dan 'user_id' berkat langkah mutate di atas
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

        // 5. Simpan ke tabel relasi payment
        if ($paymentData) {
            // Ambil string murni dari akad karena $order->akad berupa objek Enum setelah di-cast
            $akadValue = is_object($order->akad) ? $order->akad->value : $order->akad;
            
            $order->payment()->create([
                'type' => $akadValue === 'istishna' ? 'dp' : 'full', // Menggunakan 'full' sebagai ganti 'lunas'
                'payment_method' => $paymentData['payment_method'] ?? 'manual',
                'amount' => $paymentData['amount'] ?? 0, // Nilai ini otomatis terisi dari mutateFormDataBeforeCreate
                'status' => $paymentData['payment_status'] ?? 'pending',
                'user_id' => $paymentData['user_id'], // Menyimpan user_id pembeli ke data payment jika dibutuhkan
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