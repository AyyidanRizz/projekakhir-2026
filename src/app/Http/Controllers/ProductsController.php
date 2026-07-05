<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;

class ProductsController extends Controller
{
    public function index()
    {
        // Mengambil semua produk dari database
        $products = Products::where('is_active', true)->with('variants')->get();
            return view('front.shop', compact('products'));
    }
    public function show($id)
    {
        $product = Products::with('variants')->findOrFail($id);
            return view('front.show', compact('product'));
    }
}
