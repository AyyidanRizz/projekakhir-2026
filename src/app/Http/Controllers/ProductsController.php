<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::where('is_active', true)
            ->with(['variants', 'category']);

        // Search berdasarkan nama dan deskripsi
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->get();

        $categories = \App\Models\Categories::orderBy('name')->get();

        return view('front.shop', compact('products', 'categories'));
    }

    public function show(Products $product)
    {
        $product->load('variants');

        return view('front.show', compact('product'));
    }

    public function home()
    {
        $products = Products::where('is_active', true)
            ->with('variants')
            ->latest()
            ->take(3)
            ->get();

        return view('front.index', compact('products'));
    }
}