<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products; 
use App\Models\ProductsVariants; // Pastikan nama model varian kamu sesuai

class CartController extends Controller
{
    public function index()
    {
        session()->forget('checkout_type');
        session()->forget('direct_checkout');

        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        return view('front.cart', compact('cart', 'subtotal'));
    }

    // 1. TAMBAH KE KERANJANG & POTONG STOK DI DATABASE
public function add(Request $request, $id)
    {
        $product = Products::findOrFail($id);
        
        $request->validate([
            'variant_id' => 'required',
            'quantity' => 'required|integer|min:1'
        ]);

        $qtyRequested = intval($request->quantity);
        $variant = $product->variants()->where('id', $request->variant_id)->firstOrFail();
        
        // Tetap cek apakah permintaan awal masuk akal dengan stok yang ada
        if ($variant->stock < $qtyRequested) {
            return redirect()->back()->with('error', 'Stok tidak cukup. Hanya tersisa ' . $variant->stock . ' item.');
        }

        $cartKey = $id . '_' . $request->variant_id;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            if ($variant->stock < ($cart[$cartKey]['quantity'] + $qtyRequested)) {
                return redirect()->back()->with('error', 'Gagal menambah. Jumlah di keranjang melebihi stok tersedia.');
            }
            $cart[$cartKey]['quantity'] += $qtyRequested;
        } else {
            $cart[$cartKey] = [
                "product_id" => $id,
                "variant_id" => $request->variant_id,
                "name" => $product->name,
                "variant_name" => ($variant->size ?? 'All Size') . ' - ' . ($variant->material ?? 'Bahan'),
                "quantity" => $qtyRequested,
                "price" => $variant->price * 1000,
                "image" => $product->image
            ];
        }

        // === BARIS DECREMENT DI SINI SUDAH DIHAPUS ===

        session()->put('cart', $cart);

        session()->forget('checkout_type');
        session()->forget('direct_checkout'); 

        return redirect()->route('cart.index')->with('success', 'Produk berhasil dimasukkan ke keranjang!');
    }

    // 2. HAPUS DARI KERANJANG (TANPA MENGEMBALIKAN STOK DATABASE)
    public function remove($key)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$key])) {
            // === BARIS INCREMENT DI SINI SUDAH DIHAPUS ===
            unset($cart[$key]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }

    // 3. BELI INSTANT (DIRECT CHECKOUT) & POTONG STOK
    public function buyNow(Request $request, $id){
        $product = Products::findOrFail($id);
        $request->validate([
            'variant_id' => 'required',
            'quantity'   => 'required|integer|min:1'
        ]);
        $qtyRequested = (int) $request->quantity;
        $variant = $product->variants()
            ->where('id', $request->variant_id)
            ->firstOrFail();
        if ($variant->stock < $qtyRequested) {
            return redirect()->back()
                ->with('error', 'Stok tidak cukup untuk pembelian langsung.');
        }
        $directCheckout = [
            "product_id"   => $id,
            "variant_id"   => $variant->id,
            "name"         => $product->name,
            "variant_name" => ($variant->size ?? 'All Size') . ' - ' . ($variant->material ?? 'Bahan'),
            "quantity"     => $qtyRequested,
            "price"        => $variant->price * 1000,
            "image"        => $product->image,
        ];
        session()->put('direct_checkout', [
            $id . '_' . $variant->id => $directCheckout
        ]);

        session()->put('checkout_type', 'direct');
        return redirect()->route('checkout.index');
    }

    // 4. MEMPERBARUI QUANTITY DI HALAMAN CART (OPSIONAL - MENYESUAIKAN PERUBAHAN ANGKA)
// 4. MEMPERBARUI QUANTITY DI HALAMAN CART (TANPA UPDATE STOK DATABASE)
    public function update(Request $request)
    {
        if ($request->cart_keys && $request->quantities) {
            $cart = session()->get('cart', []);
            
            foreach ($request->cart_keys as $index => $key) {
                if (isset($cart[$key])) {
                    $newQty = intval($request->quantities[$index]);
                    
                    if ($newQty <= 0) {
                        $this->remove($key);
                        continue;
                    }

                    $variant = ProductsVariants::find($cart[$key]['variant_id']);
                    if ($variant) {
                        // Validasi apakah jumlah baru melebihi stok total di database
                        if ($variant->stock < $newQty) {
                            return redirect()->back()->with('error', 'Waduh, stok tidak mencukupi! Sisa stok yang tersedia saat ini hanya ' . $variant->stock . ' pcs.');
                        }
                        // === BARIS DECREMENT DAN INCREMENT DI SINI SUDAH DIHAPUS ===
                    }

                    $cart[$key]['quantity'] = $newQty;
                }
            }
            session()->put('cart', $cart);
            session()->put('checkout_type', 'cart');
            return redirect()->back()->with('success', 'Keranjang berhasil diperbarui!');
        }
        return redirect()->back();
    }
}