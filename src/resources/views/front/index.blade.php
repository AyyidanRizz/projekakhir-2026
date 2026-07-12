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
                                <p class="mb-3">Wujudkan Produk Custom Impianmu dengan Proses yang Transparan dan Sesuai Syariah.</p>
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
                        <h2 class="mb-4 section-title">Wujudkan Produk Impianmu.</h2>
                        <p class="mb-4">Pesan berbagai produk custom dengan mudah. Mulai dari upload desain hingga produksi,
                        semua diproses secara transparan dan sesuai prinsip syariah.</p>
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
        <div class="we-help-section pt-0 mt-0">
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
                        <h2 class="section-title mb-4">We Help You To Make Your Own Design</h2>
                        <p>Kami melayani sablon dan bordir custom untuk kebutuhan organisasi hingga pribadi. 
                        Tak hanya mudah, kami memastikan setiap pesanan Anda diproses dengan prinsip kebaikan:</p>

                        <ul class="list-unstyled custom-list my-4">
                            <li>Transaksi dengan aturan syariah yang jelasan diawal</li>
                            <li>Produk berkualitas dengan menggunakan bahan pilihan</li>
                            <li>Custom sesuai ide desain dan spesifikasi kesukaanmu</li>
                            <li>Setiap tahapan produksi akan diinfokan secara transparan</li>
                        </ul>
                        <p><a href="{{ route('front.shop') }}" class="btn">
                            Explore
                        </a></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- End We Help Section -->

        <!-- Start Popular Product -->
        <div class="popular-product">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 mx-auto text-center">
                        <h2 class="section-title">Produk Pilihan</h2>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4 mb-4 mb-lg-0">
                        <div class="product-item-sm d-flex">
                            <div class="thumbnail">
                                <img src="{{ asset('front/images/foto/produk3-baju.png') }}" alt="Image" class="img-fluid">
                            </div>
                            <div class="pt-3">
                                <h3>Kaos Crew Neck</h3>
                                <p>Kenyamanan maksimal dengan sentuhan desain minimalis yang bermakna.</p>
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
                                <p>Sahabat terbaik untuk cuaca dingin, dirancang dengan bahan premium yang super lembut.</p>
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
                                <p>Sentuhan akhir yang sempurna untuk melindungi harimu.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Popular Product -->

        <!-- Start Testimonial Slider -->
        <div class="testimonial-section pt-0 mt-0">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 mx-auto text-center">
                        <h2 class="section-title">Testimoni</h2>
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
                                                    <p>&ldquo;Proses pemesanan sangat mudah dan jelas. Saya bisa mengirim desain sendiri, memilih ukuran, hingga mengetahui status pesanan tanpa harus datang langsung. Hasil sablonnya juga sesuai dengan desain yang diberikan.&rdquo;</p>
                                                </blockquote>

                                                <div class="author-info">
                                                    <div class="author-pic">
                                                        <img src="{{ asset('front/images/person-1.png') }}" alt="Maria Jones" class="img-fluid">
                                                    </div>
                                                    <h3 class="font-weight-bold">Ahmad Fauzan</h3>
                                                    <span class="position d-block mb-3">Ketua Organisasi Kampus</span>
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
                                                    <p>&ldquo;Saya sangat terbantu dengan adanya sistem ini. Pemilihan akad dan rincian pembayaran dijelaskan dengan transparan, sehingga proses pemesanan dalam jumlah banyak terasa lebih aman dan nyaman.&rdquo;</p>
                                                </blockquote>

                                                <div class="author-info">
                                                    <div class="author-pic">
                                                        <img src="{{ asset('front/images/person-1.png') }}" alt="Maria Jones" class="img-fluid">
                                                    </div>
                                                    <h3 class="font-weight-bold">Rani Maharani</h3>
                                                    <span class="position d-block mb-3">Pemilik Usaha</span>
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
                                                    <p>&ldquo;Produk yang saya pesan memiliki kualitas yang bagus dan hasil bordirnya rapi. Komunikasi selama proses produksi juga mudah karena setiap tahap pesanan dapat dipantau.&rdquo;</p>
                                                </blockquote>

                                                <div class="author-info">
                                                    <div class="author-pic">
                                                        <img src="{{ asset('front/images/person-1.png') }}" alt="Maria Jones" class="img-fluid">
                                                    </div>
                                                    <h3 class="font-weight-bold">Dimas Pratame</h3>
                                                    <span class="position d-block mb-3">Anggota Komunitas</span>
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