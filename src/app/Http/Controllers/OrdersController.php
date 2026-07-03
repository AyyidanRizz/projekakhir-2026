<?php

namespace App\Http\Controllers;

use App\Models\Orders;
use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * Menyimpan pesanan baru dan menentukan akad syariah secara dinamis.
     */
    public function store(Request $request)
    {
        // 1. Validasi input request demi keamanan data
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'shipping_address' => 'required|string',
            'note' => 'nullable|string',
            'chosen_akad' => 'nullable|string|in:salam,istishna',
        ]);

        // Hitung total kuantitas barang dari input items
        $cartItems = $request->input('items');
        $totalQuantity = array_sum(array_column($cartItems, 'quantity'));
        $totalPrice = $request->input('total_price');

        // 2. Gunakan Database Transaction agar jika ada error di tengah jalan, data tidak corrupt
        DB::beginTransaction();

        try {
            // 3. Inisialisasi object Order baru
            $order = new Orders();
            $order->user_id = \Illuminate\Support\Facades\Auth::id();
            $order->total_price = $totalPrice;
            $order->shipping_address = $request->input('shipping_address');
            $order->note = $request->input('note');
            $order->status = OrderStatus::from('menunggu_validasi_desain');

            // 4. LOGIKA BISNIS SYARIAH DINAMIS
            if ($totalQuantity < 12) {
                // Jika < 12 pcs, otomatis menggunakan Akad Salam dan DP = 0
                $order->akad = Akad::SALAM;
                $order->dp_amount = 0;
            } else {
                // Jika >= 12 pcs, ambil pilihan akad dari input form user
                $chosenAkad = $request->input('chosen_akad'); 
                
                // Jika user entah bagaimana tidak memilih, default-kan ke Salam atau sesuaikan kebutuhan
                $order->akad = $chosenAkad ? Akad::from($chosenAkad) : Akad::SALAM;
                
                // Kalkulasi DP jika memilih Istishna
                if ($order->akad === Akad::ISTISHNA) {
                    $order->dp_amount = $totalPrice * 0.50; // DP 50%
                } else {
                    $order->dp_amount = 0; // Memilih Salam walau >= 12 pcs (Bayar Full)
                }
            }

            // Simpan data order utama
            $order->save();

            // 5. Simpan detail items pesanan ke tabel orders_items
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Jika semua proses aman, commit database
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat dengan logika syariah!',
                'order_number' => $order->order_number,
                'akad_digunakan' => $order->akad->value,
                'total_harga' => $order->total_price,
                'harus_dibayar_sekarang' => $order->akad === Akad::ISTISHNA ? $order->dp_amount : $order->total_price
            ], 201);

        } catch (\Exception $e) {
            // Jika ada yang gagal, batalkan semua manipulasi database di atas
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pesanan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}