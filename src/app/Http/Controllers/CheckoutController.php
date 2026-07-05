<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Orders; 
use App\Models\OrdersItems; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // 1. Ambil data cart dari session.
        $cart = session()->get('cart', []);

        // 2. MODIFIKASI: Jika keranjang kosong, TAPI ada session sukses checkout, jangan di-kick ke cart.
        if (empty($cart) && !session()->has('success_checkout')) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda masih kosong!');
        }

        // 3. Hitung total belanja secara dinamis
        $totalBelanja = 0;
        foreach ($cart as $item) {
            $totalBelanja += $item['price'] * $item['quantity'];
        }

        // 4. Kirim data cart dan totalBelanja ke file view blade
        return view('front.checkout', compact('cart', 'totalBelanja'));
    }

    public function store(Request $request)
    {
        // 1. Validasi data yang masuk terlebih dahulu
        $request->validate([
            'c_fname' => 'required|string|max:255',
            'c_lname' => 'required|string|max:255',
            'c_address' => 'required|string',
            'c_email_address' => 'required|email',
            'c_phone' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        // 2. Ambil data keranjang dari session
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Keranjang belanja Anda kosong!');
        }

        // 3. Hitung total belanjaan kembali demi keamanan
        $totalBelanja = 0;
        foreach ($cart as $item) {
            $totalBelanja += $item['price'] * $item['quantity'];
        }

        // 4. Simpan data ke tabel Orders
        $order = Orders::create([
            'user_id'          => Auth::id(), 
            'total_price'      => $totalBelanja,
            'status'           => \App\Enums\OrderStatus::MENUNGGU_VALIDASI_DESAIN,
            'akad'             => \App\Enums\Akad::SALAM, 
            'shipping_address' => json_encode([
                'nama'          => $request->c_fname . ' ' . $request->c_lname,
                'perusahaan'    => $request->c_companyname,
                'alamat_lengkap'=> $request->c_address,
                'negara_bagian' => $request->c_state_country,
                'kodepos'       => $request->c_postal_zip,
                'email'         => $request->c_email_address,
                'telepon'       => $request->c_phone,
            ]),
            'note'             => $request->c_order_notes,
            'order_date'       => now(),
            'dp_amount'        => 0, 
            'paid_amount'      => 0,
            'refund_amount'    => 0,
        ]);

        // 5. Simpan detail produk yang dibeli ke tabel Order Items
        foreach ($cart as $id => $item) {
            $hargaSatuan = $item['price'];
            $jumlahBeli  = $item['quantity'];

            OrdersItems::create([
                'order_id'           => $order->id,
                'product_variant_id' => $item['variant_id'] ?? $item['product_variant_id'] ?? 1, 
                'quantity'           => $jumlahBeli,
                'unit_price'         => $hargaSatuan,
                'subtotal'           => $hargaSatuan * $jumlahBeli,
            ]);
        }

        // 6. Kosongkan keranjang belanja setelah sukses order
        // 6. Kosongkan keranjang belanja setelah sukses order
        session()->forget('cart');

        // 7. UBAH BARIS INI: Alihkan kembali ke halaman checkout index
        return redirect()->route('checkout.index')->with('success_checkout', 'Pesanan Anda berhasil dibuat! Silakan lakukan validasi desain.');
    }
}