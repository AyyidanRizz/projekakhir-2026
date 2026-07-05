@extends('front.layouts.master')

@section('content')
        <!-- Start Hero Section -->
            <div class="hero">
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-lg-5">
                            <div class="intro-excerpt">
                                <h1>Shop</h1>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            
                        </div>
                    </div>
                </div>
            </div>
        <!-- End Hero Section -->

        <div class="untree_co-section product-section before-footer-section">
            <div class="container">
                <div class="row">
					<!-- Loop Data Produk dari Controller -->
					@foreach($products as $product)
					<div class="col-12 col-md-4 col-lg-3 mb-5">
						<!-- Link mengarah ke rute detail dengan ID produk -->
						<a class="product-item" href="{{ url('/shop/' . $product->id) }}">
							<img src="{{ asset('storage/' . $product->image) }}" class="img-fluid product-thumbnail" alt="{{ $product->name }}">
							<h3 class="product-title">{{ $product->name }}</h3>
							
							<!-- Menampilkan Harga Dasar -->
							<strong class="product-price text-dark d-block mt-2">
								Rp {{ number_format($product->base_price * 1000, 0, ',', '.') }}
							</strong>

							<span class="icon-cross">
								<img src="{{ asset('front/images/cross.svg') }}" class="img-fluid">
							</span>
						</a>
					</div>
					@endforeach
					<!-- End Loop -->
                </div>
            </div>
        </div>
@endsection