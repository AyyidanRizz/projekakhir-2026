<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\ProductsVariants;

class CartController extends Controller
{
    /**
     * Menambahkan produk dan variannya ke dalam keranjang (Session)
     */
    public function add(Request $request)
    {
        // 1. Validasi input yang dikirim dari form
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:products_variants,id',
        ]);

        // 2. Ambil data produk dan varian secara detail dari database
        $product = Products::findOrFail($request->product_id);
        $variant = ProductsVariants::findOrFail($request->variant_id);

        // 3. Cek apakah stok varian tersebut masih tersedia
        if ($variant->stock <= 0) {
            return redirect()->back()->with('error', 'Maaf, stok untuk varian ini sudah habis!');
        }

        // 4. Ambil data keranjang yang saat ini ada di Session (jika belum ada, buat array kosong)
        $cart = session()->get('cart', []);

        // Membuat unique ID untuk item di keranjang (gabungan ID produk dan ID varian)
        $cartItemId = $product->id . '-' . $variant->id;

        // 5. Logika: Jika barang dengan varian yang sama SUDAH ADA di keranjang, tambah jumlahnya (quantity)
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity']++;
        } else {
            // Jika BELUM ADA, masukkan data baru ke dalam array keranjang
            $cart[$cartItemId] = [
                "product_id" => $product->id,
                "variant_id" => $variant->id,
                "name"       => $product->name,
                "size"       => $variant->size,
                "material"   => $variant->material,
                "quantity"   => 1,
                "price"      => $variant->price, // Menggunakan harga final milik varian
                "image"      => $product->image
            ];
        }

        // 6. Simpan kembali array keranjang yang terbaru ke dalam Session
        session()->put('cart', $cart);

        // 7. Kembalikan user ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang belanja!');
    }
}