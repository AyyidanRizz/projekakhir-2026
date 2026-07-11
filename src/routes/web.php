<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Illuminate\Support\Facades\Response;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/
Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/

// 🔥 HALAMAN UTAMA (HOMEPAGE) - SEKARANG SUDAH AKTIF
Route::get('/', [ProductsController::class, 'home'])->name('home');

// Halaman Shop Utama
Route::get('/shop', [ProductsController::class, 'index'])->name('front.shop');

// Halaman Detail Produk (Menggunakan Route Model Binding)
Route::get('/shop/{product}', [ProductsController::class, 'show'])->name('front.shop.detail');

// Halaman Statis
Route::get('/about', function () {
    return view('front.about');
});
Route::get('/services', function () {
    return view('front.services');
});


// ==================== KERAJANG (CART) ====================
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/buynow/{id}', [CartController::class, 'buyNow'])->name('cart.buynow');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::get('/cart/remove/{key}', [CartController::class, 'remove'])->name('cart.remove');


// ==================== AUTENTIKASI ====================
Route::get('/masuk', [AuthController::class, 'showAuthPage'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// ==================== PROTEKSI (AUTH ONLY) ====================
Route::middleware(['auth'])->group(function () {
    Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
    
    // Checkout diarahkan langsung ke Controller agar dinamis
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
});