<?php

namespace App\Http\Controllers;

use App\Models\Shippings;
use App\Models\Designs;
use App\Enums\Courier;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\Payments;
use App\Models\ProductsVariants;
use App\Enums\PaymentMethod;
use App\Enums\PaymentType;
use App\Enums\PaymentStatus;
use App\Enums\Akad;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('front.checkout', compact(
            'cart',
            'totalBelanja',
            'totalQuantity',
            'user',
        ))->with('couriers', Courier::cases());
    }

public function store(Request $request)
    {
        // 1. Validasi input form checkout
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
            'design_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        // 2. Tentukan sumber keranjang belanja (Direct atau Cart biasa)
        $checkoutType = session()->get('checkout_type');
        if ($checkoutType === 'direct') {
            $cart = session()->get('direct_checkout', []);
        } else {
            $cart = session()->get('cart', []);
        }

        if (empty($cart)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Keranjang belanja Anda kosong!');
        }

        // 3. Validasi real-time apakah stok di DB mencukupi sebelum memproses order
        foreach ($cart as $item) {

            $variantId = $item['variant_id'] ?? $item['product_variant_id'] ?? 1;
            $variant = ProductsVariants::find($variantId);

            if (!$variant || $variant->stock < $item['quantity']) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Maaf, stok untuk produk "' . $item['name'] . '" tidak mencukupi atau telah habis dibeli oleh user lain. Silakan periksa kembali keranjang Anda.'
                    );
            }
        }

        // 4. Update data profile user secara otomatis dari data form checkout terbaru
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->update([
            'phone'       => $request->phone,
            'address'     => $request->address,
            'province'    => $request->province,
            'city'        => $request->city,
            'postal_code' => $request->postal_code,
        ]);

        // 5. Hitung total belanja dan total quantity barang
        $totalBelanja = 0;
        $totalQuantity = 0;
        foreach ($cart as $item) {
            $totalBelanja += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }

        // Tentukan jenis Akad dan besaran Down Payment (DP)
        if ($totalQuantity < 12) {
            $akad = Akad::SALAM;
        } else {
            $akad = Akad::from($request->akad);
        }
        
        $dp = ($akad === Akad::ISTISHNA) ? ($totalBelanja * 0.5) : $totalBelanja;

        // 6. Buat baris Order utama di database (Variabel $order diinisialisasi secara resmi)
        $order = Orders::create([
            'user_id'     => Auth::id(),
            'total_price' => $totalBelanja,
            'status'      => OrderStatus::MENUNGGU_VALIDASI_DESAIN,
            'akad'        => $akad,
            'shipping_address' => $request->address . ', ' . $request->city . ', ' . $request->province . ', ' . $request->postal_code,
            'note'          => $request->note,
            'order_date'    => now(),
            'dp_amount'     => $dp,
            'paid_amount'   => 0,
            'refund_amount' => 0,
        ]);

        // 7. Simpan file berkas desain yang diunggah
        $filePath = $request->file('design_file')->store('designs', 'public');
        Designs::create([
            'order_id'    => $order->id,
            'file_path'   => $filePath,
            'status'      => \App\Enums\DesignStatus::PENDING,
            'uploaded_at' => now(),
        ]);

        // 8. Daftarkan item belanja ke OrdersItems & HANYA potong stok di database di sini
        foreach ($cart as $item) {
            $variantId = $item['variant_id'] ?? $item['product_variant_id'] ?? 1;

            OrdersItems::create([
                'order_id'           => $order->id,
                'product_variant_id' => $variantId,
                'quantity'   => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal'   => $item['price'] * $item['quantity'],
            ]);
        }

        // 9. Buat data tagihan Pembayaran (Payments)
        Payments::create([
            'order_id' => $order->id,
            'type' => ($akad === Akad::ISTISHNA) ? PaymentType::DP : PaymentType::FULL,
            'payment_method' => PaymentMethod::from($request->payment_method),
            'amount' => $dp,
            'status' => PaymentStatus::PENDING,
        ]);

        // 10. Buat data Pengiriman (Shippings)
        Shippings::create([
            'order_id' => $order->id,
            'courier' => $request->courier,
            'shipping_address' => $request->address . ', ' . $request->city . ', ' . $request->province . ', ' . $request->postal_code,
            'status' => 'pending',
        ]);
            
        // 11. Bersihkan seluruh session belanja setelah checkout sukses
        session()->forget('cart');
        session()->forget('direct_checkout');
        session()->forget('checkout_type');
        
        return redirect()
            ->route('checkout.index')
            ->with(
                'success_checkout',
                'Pesanan Anda berhasil dibuat! Silakan lakukan validasi desain.'
            );
    }
}