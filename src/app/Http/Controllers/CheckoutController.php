<?php

namespace App\Http\Controllers;

use App\Enums\Akad;
use App\Enums\OrderStatus;
use App\Models\Orders;
use App\Models\OrdersItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // Ambil data cart dari session
        $cart = session()->get('cart', []);

        // Jika cart kosong dan bukan redirect sukses checkout
        if (empty($cart) && !session()->has('success_checkout')) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang belanja Anda masih kosong!');
        }
        // Hitung total belanja dan jumlah barang
        $totalBelanja = 0;
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalBelanja += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }
        return view('front.checkout', compact(
            'cart',
            'totalBelanja',
            'totalQuantity'
        ));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'province' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'akad' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Update data user
        $user->update([
            'phone'       => $request->phone,
            'address'     => $request->address,
            'province'    => $request->province,
            'city'        => $request->city,
            'postal_code' => $request->postal_code,
        ]);
        // Ambil cart
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()
                ->back()
                ->with('error', 'Keranjang belanja Anda kosong!');
        }
        // Hitung total
        $totalBelanja = 0;
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalBelanja += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }

        if ($totalQuantity < 12) {
            $akad = Akad::SALAM;
        } else {
            $akad = Akad::from($request->akad);
        }
        
        if ($akad === Akad::ISTISHNA) {
            $dp = $totalBelanja * 0.5;

        } else {
            $dp = $totalBelanja;

        }

        $order = Orders::create([
            'user_id'     => Auth::id(),
            'total_price' => $totalBelanja,
            'status'      => OrderStatus::MENUNGGU_VALIDASI_DESAIN,
            'akad'        => $akad,
            'shipping_address' => json_encode([
                'nama'           => $user->name,
                'email'          => $user->email,
                'telepon'        => $request->phone,
                'alamat_lengkap' => $request->address,
                'provinsi'       => $request->province,
                'kota'           => $request->city,
                'kodepos'        => $request->postal_code,
            ]),
            'note' => $request->note,
            'order_date'    => now(),
            'dp_amount'     => $dp,
            'paid_amount'   => 0,
            'refund_amount' => 0,
        ]);

        foreach ($cart as $item) {

            OrdersItems::create([
                'order_id'           => $order->id,
                'product_variant_id' => $item['variant_id'] 
                    ?? $item['product_variant_id'] 
                    ?? 1,
                'quantity'   => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal'   => $item['price'] * $item['quantity'],
            ]);

        }
        session()->forget('cart');
        
        return redirect()
            ->route('checkout.index')
            ->with(
                'success_checkout',
                'Pesanan Anda berhasil dibuat! Silakan lakukan validasi desain.'
            );
    }
}