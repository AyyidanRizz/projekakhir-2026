<?php

use App\Http\Controllers\OrdersController;
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

Route::get('/shop', function () {
    return view('front.shop');
});

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

Route::middleware(['auth'])->group(function () {
    Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
});