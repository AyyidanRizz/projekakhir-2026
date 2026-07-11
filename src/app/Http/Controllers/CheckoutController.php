<?php

namespace App\Http\Controllers;

use App\Enums\Courier;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\Payments;
use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum as EnumRule;

class CheckoutController extends Controller
{
    public function index(){
    $checkoutType = session()->get('checkout_type');
        if ($checkoutType === 'direct') {
            $cart = session()->get('direct_checkout', []);
        } else {
            $cart = session()->get('cart', []);
        }

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
        ))->with('couriers', Courier::cases());
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
            'courier' => [
                'required',
                new EnumRule(Courier::class),
            ],
            'akad' => 'nullable|string',
            'payment_method' => 'required|in:virtual_account,qris',
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
        // Menentukan sumber checkout
        $isDirectCheckout = session()->has('direct_checkout');

        // Prioritaskan Direct Checkout.
        // Jika tidak ada, gunakan Cart biasa.
        $cart = session()->get('direct_checkout');

        if (!$cart) {
            $cart = session()->get('cart', []);
        }

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
                    'kurir'          => $request->courier,
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

        Payments::create([

            'order_id' => $order->id,

            'type' => $akad === Akad::ISTISHNA
                ? PaymentType::DP
                : PaymentType::FULL,
            'payment_method' => PaymentMethod::from(
                $request->payment_method
            ),
            'amount' => $dp,
            'status' => PaymentStatus::PENDING,
        ]);
        // Menentukan sumber checkout
        $isDirectCheckout = session()->has('direct_checkout');
        $checkoutType = session()->get('checkout_type');
        if ($checkoutType === 'direct') {
            $cart = session()->get('direct_checkout', []);
        } else {
            $cart = session()->get('cart', []);
        }
        
        return redirect()
            ->route('checkout.index')
            ->with(
                'success_checkout',
                'Pesanan Anda berhasil dibuat! Silakan lakukan validasi desain.'
            );
    }
}