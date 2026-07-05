<?php

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
/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', function () {
    return view('front.index');
});

Route::get('/', function () {
    return view('front.index');
});

// Halaman Shop Utama
Route::get('/shop', [ProductsController::class, 'index'])
    ->name('front.shop');

// Halaman Detail Produk (Tambahkan ini)
Route::get('/shop/{id}', [ProductsController::class, 'show'])
    ->name('front.shop.detail');

Route::get('/about', function () {
    return view('front.about');
});

Route::get('/services', function () {
    return view('front.services');
});

Route::get('/cart', function () {
    return view('front.cart');
});

Route::get('/checkout', function () {
    return view('front.checkout');
});

// Proteksi Halaman: Hanya User yang sudah Login
Route::middleware(['auth'])->group(function () {
    Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
    Route::get('/checkout', function () { 
        return view('front.checkout'); 
    })->name('front.checkout'); // <-- SEKARANG SUDAH DIBERI NAMA
});
/*
Route::get('/', function () { return view('front.index'); })->name('home');
Route::get('/shop', function () { return view('front.shop'); });
Route::get('/about', function () { return view('front.about'); });
Route::get('/services', function () { return view('front.services'); });
Route::get('/cart', function () { return view('front.cart'); });
*/

// Route Autentikasi (Login & Keluar)
Route::get('/masuk', [AuthController::class, 'showAuthPage'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
// Tambahkan ini di routes/web.php (di luar atau di dalam middleware auth sesuai kebutuhan webmu)
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

// Proteksi Halaman: Hanya User yang sudah Login yang bisa masuk ke sini
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', function () { 
        return view('front.checkout'); 
    });
});