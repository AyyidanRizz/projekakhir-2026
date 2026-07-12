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
								<h1 style="font-size: 2.5rem;">Services</h1>
                                <p class="mb-3">Wujudkan Produk Custom Impianmu dengan Proses yang Transparan dan Sesuai Syariah.</p>
                                <p class="mb-0">
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

<!-- Start Why Choose Us Section -->
        <div class="why-choose-section">
            <div class="container">
                
                <!-- Baris Judul -->
                <div class="row mb-5">
                    <div class="col-lg-7 mx-auto text-center">
                        <h2 class="section-title">Why Choose Us</h2>
                    </div>
                </div>
                
                <!-- Baris Fitur (Horizontal) -->
                <div class="row">
                    
                    <!-- Fitur 1 -->
                    <div class="col-6 col-md-6 col-lg-3 mb-4">
                        <div class="feature">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width: 40px; height: 40px; color: black;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                </svg>
                            </div>
                            <h3>Amanah Syariah</h3>
                            <p>Kami menjamin transparansi dalam setiap akad dan spesifikasi produk. Tidak ada gharar (ketidakpastian) dalam transaksi kami.</p>
                        </div>
                    </div>

                    <!-- Fitur 2 -->
                    <div class="col-6 col-md-6 col-lg-3 mb-4">
                        <div class="feature">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width: 40px; height: 40px; color: black;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 21L14.907 18M21.177 3A2.25 2.25 0 0 0 18.02 3L2.68 18.34a2.25 2.25 0 0 0 0 3.18l.82.82a2.25 2.25 0 0 0 3.18 0L22.02 7A2.25 2.25 0 0 0 22.02 3.843l-.843-.843ZM15.75 6.75l3 3M6.75 15.75l-3 3" />
                                </svg>
                            </div>
                            <h3>Kualitas Terjamin</h3>
                            <p>Kami menggunakan bahan premium dan tinta halal serta bordir dengan presisi tinggi untuk hasil terbaik.</p>
                        </div>
                    </div>

                    <!-- Fitur 3 -->
                    <div class="col-6 col-md-6 col-lg-3 mb-4">
                        <div class="feature">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width: 40px; height: 40px; color: black;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v5.018Z" />
                                </svg>
                            </div>
                            <h3>Layanan Ramah &amp; Solutif</h3>
                            <p>Kami memberikan layanan pelanggan yang cepat dan responsif, siap membantu Anda dari awal hingga akhir.</p>
                        </div>
                    </div>

                    <!-- Fitur 4 -->
                    <div class="col-6 col-md-6 col-lg-3 mb-4">
                        <div class="feature">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" style="width: 40px; height: 40px; color: black;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                </svg>
                            </div>
                            <h3>Tanpa Riba</h3>
                            <p>Kami menjamin tidak ada sistem riba dalam proses transaksi maupun denda keterlambatan pembayaran.</p>
                        </div>
                    </div>

                </div> <!-- Akhir dari row fitur -->
            
            </div>
        </div>
<!-- End Why Choose Us Section -->		

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