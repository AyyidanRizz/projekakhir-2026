@extends('front.layouts.master')

@section('content')
        {{-- Menambahkan inline style padding untuk mengontrol tinggi hero section --}}
            <div class="hero" style="padding: 30px 0 !important;"> 
                <div class="container">
                    <div class="row justify-content-between">
                        <div class="col-lg-5">
                            <div class="intro-excerpt">
                                {{-- Mengurangi ukuran font h1 jika dirasa teksnya terlalu besar setelah hero diperkecil --}}
                                <h1 style="font-size: 2.5rem;">Produk Sablon & Bordir</span></h1>
                                <p class="mb-3">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.</p>
                                <p class="mb-0">
                                </p>
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

        <!-- Start Product Section -->
        <div class="product-section">
            <div class="container">
                <div class="row">

                    <!-- Start Column 1 -->
                    <div class="col-md-12 col-lg-3 mb-5 mb-lg-0">
                        <h2 class="mb-4 section-title">Crafted with excellent material.</h2>
                        <p class="mb-4">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. </p>
                        <p><a href="{{ route('front.shop') }}" class="btn">
                            Explore
                        </a></p>
                    </div> 
                    <!-- End Column 1 -->
                    @foreach($products as $product)
                    <div class="col-12 col-md-4 col-lg-3 mb-5 mb-md-0">
                        <a class="product-item"
                        href="{{ route('front.shop.detail', $product->slug) }}">
                            <img
                                src="{{ $product->image ? asset('storage/'.$product->image) : asset('front/images/product-1.png') }}"
                                class="img-fluid product-thumbnail"
                                alt="{{ $product->name }}">
                            <h3 class="product-title">
                                {{ $product->name }}
                            </h3>
                            <strong class="product-price">
                                Rp {{ number_format($product->base_price * 1000,0,',','.') }}
                            </strong>
                            <span class="icon-cross">
                                <img src="{{ asset('front/images/cross.svg') }}" class="img-fluid">
                            </span>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- End Product Section -->
        
        <!-- Start We Help Section -->
        <div class="we-help-section">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-7 mb-5 mb-lg-0">
                        <div class="imgs-grid">
                            <div class="grid grid-1"><img src="{{ asset('front/images/foto/sablon1-orang(1).jpg') }}" alt="Untree.co"></div>
                            <div class="grid grid-2"><img src="{{ asset('front/images/foto/bordir9-alat(1).jpg') }}" alt="Untree.co"></div>
                            <div class="grid grid-3"><img src="{{ asset('front/images/foto/bordir5-baju(1).jpg') }}" alt="Untree.co"></div>
                        </div>
                    </div>
                    <div class="col-lg-5 ps-lg-5">
                        <h2 class="section-title mb-4">We Help You Make Modern Interior Design</h2>
                        <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada</p>

                        <ul class="list-unstyled custom-list my-4">
                            <li>Donec vitae odio quis nisl dapibus malesuada</li>
                            <li>Donec vitae odio quis nisl dapibus malesuada</li>
                            <li>Donec vitae odio quis nisl dapibus malesuada</li>
                            <li>Donec vitae odio quis nisl dapibus malesuada</li>
                        </ul>
                        <p><a href="#" class="btn">Explore</a></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End We Help Section -->

        <!-- Start Popular Product -->
        <div class="popular-product">
            <div class="container">
                <div class="row">

                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="product-item-sm d-flex">
                            <div class="thumbnail">
                                <img src="{{ asset('front/images/foto/produk3-baju.png') }}" alt="Image" class="img-fluid">
                            </div>
                            <div class="pt-3">
                                <h3>Kaos Crew Neck</h3>
                                <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio </p>
                                <p><a href="#">Read More</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="product-item-sm d-flex">
                            <div class="thumbnail">
                                <img src="{{ asset('front/images/foto/produk2-hoodie.png') }}" alt="Image" class="img-fluid">
                            </div>
                            <div class="pt-3">
                                <h3>Hoodie</h3>
                                <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio </p>
                                <p><a href="#">Read More</a></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="product-item-sm d-flex">
                            <div class="thumbnail">
                                <img src="{{ asset('front/images/foto/produk1-topi.png') }}" alt="Image" class="img-fluid">
                            </div>
                            <div class="pt-3">
                                <h3>Topi</h3>
                                <p>Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio </p>
                                <p><a href="#">Read More</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Popular Product -->

        <!-- Start Testimonial Slider -->
        <div class="testimonial-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 mx-auto text-center">
                        <h2 class="section-title">Testimonials</h2>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="testimonial-slider-wrap text-center">

                            <div id="testimonial-nav">
                                <span class="prev" data-controls="prev"><span class="fa fa-chevron-left"></span></span>
                                <span class="next" data-controls="next"><span class="fa fa-chevron-right"></span></span>
                            </div>

                            <div class="testimonial-slider">
                                
                                <div class="item">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8 mx-auto">

                                            <div class="testimonial-block text-center">
                                                <blockquote class="mb-5">
                                                    <p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
                                                </blockquote>

                                                <div class="author-info">
                                                    <div class="author-pic">
                                                        <img src="{{ asset('front/images/person-1.png') }}" alt="Maria Jones" class="img-fluid">
                                                    </div>
                                                    <h3 class="font-weight-bold">Maria Jones</h3>
                                                    <span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div> 
                                <!-- END item -->

                                <div class="item">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8 mx-auto">

                                            <div class="testimonial-block text-center">
                                                <blockquote class="mb-5">
                                                    <p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
                                                </blockquote>

                                                <div class="author-info">
                                                    <div class="author-pic">
                                                        <img src="{{ asset('front/images/person-1.png') }}" alt="Maria Jones" class="img-fluid">
                                                    </div>
                                                    <h3 class="font-weight-bold">Maria Jones</h3>
                                                    <span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div> 
                                <!-- END item -->

                                <div class="item">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8 mx-auto">

                                            <div class="testimonial-block text-center">
                                                <blockquote class="mb-5">
                                                    <p>&ldquo;Donec facilisis quam ut purus rutrum lobortis. Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Integer convallis volutpat dui quis scelerisque.&rdquo;</p>
                                                </blockquote>

                                                <div class="author-info">
                                                    <div class="author-pic">
                                                        <img src="{{ asset('front/images/person-1.png') }}" alt="Maria Jones" class="img-fluid">
                                                    </div>
                                                    <h3 class="font-weight-bold">Maria Jones</h3>
                                                    <span class="position d-block mb-3">CEO, Co-Founder, XYZ Inc.</span>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div> 
                                <!-- END item -->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Testimonial Slider -->
@endsection