@extends('front.layouts.master')

@section('content')
    <!-- Start Hero Section -->
{{-- Menambahkan inline style padding untuk mengontrol tinggi hero section --}}
    <div class="hero" style="padding: 30px 0 !important;"> 
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-5">
                    <div class="intro-excerpt">
                        {{-- Mengurangi ukuran font h1 jika dirasa teksnya terlalu besar setelah hero diperkecil --}}
                        <h1 style="font-size: 2.5rem;">Keranjang</h1>
                        <p class="mb-3">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.</p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="hero-img-wrap">
                        &nbsp;
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Hero Section -->

    <div class="untree_co-section before-footer-section">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(count($cart) > 0)
            <div class="row mb-5">
                <!-- FORM DIUBAH KE ROUTE UPDATE CART -->
                <form class="col-md-12" action="{{ route('cart.update') }}" method="POST">
                    @csrf
                    <div class="site-blocks-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="product-thumbnail">Image</th>
                                    <th class="product-name">Product</th>
                                    <th class="product-price">Price</th>
                                    <th class="product-quantity">Quantity</th>
                                    <th class="product-total">Total</th>
                                    <th class="product-remove">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $key => $item)
                                <tr>
                                    <td class="product-thumbnail">
                                        <img src="{{ asset('storage/' . $item['image']) }}" alt="Image" class="img-fluid" style="max-width: 100px;">
                                    </td>
                                    <td class="product-name">
                                        <h2 class="h5 text-black">{{ $item['name'] }}</h2>
                                        <small class="text-muted">Varian: {{ $item['variant_name'] }}</small>
                                    </td>
                                    <td>Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td>
                                        <!-- Hidden input untuk tracking session key -->
                                        <input type="hidden" name="cart_keys[]" value="{{ $key }}">
                                        
                                        <div class="input-group mb-3 d-flex align-items-center quantity-container" style="max-width: 120px;">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-black decrease" type="button">&minus;</button>
                                            </div>
                                            <input type="text" name="quantities[]" class="form-control text-center quantity-amount" value="{{ $item['quantity'] }}" placeholder="" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-black increase" type="button">&plus;</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                                    <td><a href="{{ route('cart.remove', $key) }}" class="btn btn-black btn-sm">X</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-5">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <!-- Men-trigger submit form update diatas -->
                            <button type="submit" class="btn btn-black btn-sm btn-block">Update Cart</button>
                        </div>
                </form> <!-- Penutup Form Update di luar row tombol agar mencakup tabel -->
                        <div class="col-md-6">
                            <a href="{{ url('/') }}" class="btn btn-outline-black btn-sm btn-block text-center d-block">Continue Shopping</a>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <label class="text-black h4" for="coupon">Coupon</label>
                            <p>Enter your coupon code if you have one.</p>
                        </div>
                        <div class="col-md-8 mb-3 mb-md-0">
                            <input type="text" class="form-control py-3" id="coupon" placeholder="Coupon Code">
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-black">Apply Coupon</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 pl-5">
                    <div class="row justify-content-end">
                        <div class="col-md-7">
                            <div class="row">
                                <div class="col-md-12 text-right border-bottom mb-5">
                                    <h3 class="text-black h4 text-uppercase">Cart Totals</h3>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <span class="text-black">Subtotal</span>
                                </div>
                                <div class="col-md-6 text-right">
                                    <strong class="text-black">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-6">
                                    <span class="text-black">Total</span>
                                </div>
                                <div class="col-md-6 text-right">
                                    <strong class="text-black">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ url('/checkout') }}" class="btn btn-black btn-lg py-3 btn-block text-center d-block">Proceed To Checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-5">
                <h3>Keranjang belanja kamu masih kosong.</h3>
                <a href="{{ url('/') }}" class="btn btn-black mt-3">Mulai Belanja</a>
            </div>
            @endif
        </div>
    </div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Ambil elemen form update keranjang
        const cartForm = document.querySelector('form[action="{{ route('cart.update') }}"]');

        if (cartForm) {
            // Deteksi klik pada tombol + (increase) dan - (decrease) bawaan template
            document.querySelectorAll('.increase, .decrease').forEach(button => {
                button.addEventListener('click', function () {
                    // Beri sedikit delay (100ms) agar script bawaan template 
                    // selesai mengubah angka di input terlebih dahulu
                    setTimeout(() => {
                        cartForm.submit();
                    }, 100);
                });
            });
        }
    });
</script>
    <!-- Script Tombol Kuantitas Interaktif
    <script>
        document.querySelectorAll('.increase').forEach(button => {
            button.addEventListener('click', function() {
                let input = this.closest('.quantity-container').querySelector('.quantity-amount');
                input.value = parseInt(input.value) + 1;
            });
        });

        document.querySelectorAll('.decrease').forEach(button => {
            button.addEventListener('click', function() {
                let input = this.closest('.quantity-container').querySelector('.quantity-amount');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                }
            });
        });
    </script>
    -->
@endsection