<!-- /*
* Bootstrap 5
* Template Name: Furni
* Template Author: Untree.co
* Template URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
*/ -->
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="Untree.co">
  <link rel="shortcut icon" href="favicon.png">

  <meta name="description" content="" />
  <meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="{{ asset('front/css/bootstrap.min.css') }}" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link href="{{ asset('front/css/tiny-slider.css') }}" rel="stylesheet">
		<link href="{{ asset('front/css/style.css') }}" rel="stylesheet">
		<title>Karsa Cloth Co. </title>
	<!-- Kustom CSS untuk mengecilkan skala tampilan website agar lebih rapi -->
	</head>

	<body>

		<!-- Start Header/Navigation -->
		<nav class="custom-navbar navbar navbar navbar-expand-md navbar-dark bg-dark" arial-label="Furni navigation bar">

			<div class="container">
				<a class="navbar-brand" href="{{ url('/') }}">Karsa Cloth Co<span>.</span></a>

				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsFurni" aria-controls="navbarsFurni" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarsFurni">
					<ul class="custom-navbar-nav navbar-nav ms-auto mb-2 mb-md-0">
                        <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/') }}">Home</a>
                        </li>
                        <li class="nav-item {{ Request::is('shop') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/shop') }}">Shop</a>
                        </li>
                        <li class="nav-item {{ Request::is('about') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/about') }}">About us</a>
                        </li>
                        <li class="nav-item {{ Request::is('services') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ url('/services') }}">Services</a>
                        </li>
                    </ul>

					<ul class="custom-navbar-cta navbar-nav mb-2 mb-md-0 ms-5">
						@auth
							<!-- Jika pelanggan SUDAH login: Tampilkan nama dan dropdown menu logout -->
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle text-white d-inline-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								Hi, {{ Auth::user()->name }}
								<!-- Posisi gambar dipindah ke bawah nama dengan margin kiri (ms-2) -->
								<img
									src="{{ Auth::user()->getFilamentAvatarUrl() }}"
									width="35"
									height="35"
									class="rounded-circle ms-2"
									style="object-fit:cover;">
							</a>
							<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
								<!-- Menu Profile -->
								<li>
									<a class="dropdown-item" href="{{ route('profile.index') }}">
										Profile
									</a>
								</li>
								<!-- Garis Pembatas (Divider) -->
								<li>
									<hr class="dropdown-divider">
								</li>
								<!-- Menu Logout -->
								<li>
									<a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
										Logout
									</a>
									<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
										@csrf
									</form>
								</li>
							</ul>
						</li>
						@else
							<!-- Jika pelanggan BELUM login: Klik icon user langsung diarahkan ke halaman login -->
							<li>
								<a class="nav-link" href="{{ route('login') }}">
									<img src="{{ asset('front/images/user.svg') }}" alt="Login">
								</a>
							</li>
						@endauth

						<li>
							<a class="nav-link" href="{{ url('/cart') }}">
								<img src="{{ asset('front/images/cart.svg') }}">
							</a>
						</li>
					</ul>
				</div>
			</div>
				
		</nav>
		<!-- End Header/Navigation -->

        @yield('content')

		<!-- Start Footer Section -->
<footer class="footer-section">
            <div class="container relative">

                <div class="row g-5 mb-5">
                    <div class="col-lg-4">
                        <div class="mb-4 footer-logo-wrap"><a href="#" class="footer-logo">Karsa Cloth Co<span>.</span></a></div>
                        <p class="mb-4">Platform pemesanan sablon & bordir kustom terpercaya dengan penerapan asas-asas muamalah syariah. Bersih bahannya, transparan akadnya, berkah hasilnya.</p>

                        <ul class="list-unstyled custom-social">
						
                            <li><a href="#"><span class="fa fa-brands fa-facebook-f"></span></a></li>
                            <li><a href="#"><span class="fa fa-brands fa-twitter"></span></a></li>
                            <li><a href="#"><span class="fa fa-brands fa-instagram"></span></a></li>
                            <li><a href="#"><span class="fa fa-brands fa-linkedin"></span></a></li>
                        </ul>
                    </div>

                    <div class="col-lg-8">
                        <div class="row links-wrap">
                            <div class="col-6 col-sm-6 col-md-3">
                                <ul class="list-unstyled">
                                    <li><strong class="text-dark d-block mb-2">Menu Utama</strong></li>
                                    <li><a href="{{ url('/') }}">Home</a></li>
                                    <li><a href="{{ url('/shop') }}">Shop</a></li>
                                    <li><a href="{{ url('/services') }}">Services</a></li>
                                    <li><a href="{{ url('/about') }}">About Us</a></li>
                                </ul>
                            </div>

                            <div class="col-6 col-sm-6 col-md-3">
                                <ul class="list-unstyled">
                                    <li><strong class="text-dark d-block mb-2">Pemesanan</strong></li>
                                    <li><a href="{{ url('/cart') }}">Cart</a></li>
                                    <li><a href="{{ url('/checkout') }}">Checkout</a></li>
                                    <li><a href="{{ url('/profile') }}">Profile</a></li>
                                </ul>
                            </div>

                            <div class="col-6 col-sm-6 col-md-6">
                                <ul class="list-unstyled">
                                    <li><strong class="text-dark d-block mb-2">Kebijakan Desain Syar'i</strong></li>
                                    <li class="text-muted small" style="line-height: 1.6;">
                                        Demi menjaga keberkahan transaksi, kami mohon maaf tidak menerima pengerjaan desain yang mengandung unsur pornografi, kemaksiatan, SARA, simbol keagamaan non-Muslim, atau konten yang melanggar syariat Islam.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="border-top copyright">
                    <div class="row pt-4">
                        <div class="col-lg-6">
                            <p class="mb-2 text-center text-lg-start">Copyright &copy;<script>document.write(new Date().getFullYear());</script>. ProjekAkhir All Rights Reserved. &mdash; Designed with love by <a href="https://untree.co">Untree.co</a> Distributed By <a href="https://themewagon.com">ThemeWagon</a>
                            </p>
                        </div>
<!--
                        <div class="col-lg-6 text-center text-lg-end">
                            <ul class="list-unstyled d-inline-flex ms-auto">
                                <li class="me-4"><a href="#">Syarat &amp; Ketentuan Akad</a></li>
                                <li><a href="#">Kebijakan Privasi</a></li>
                            </ul>
                        </div>
-->
                    </div>
                </div>

            </div>
        </footer>
		<script src="{{ asset('front/js/bootstrap.bundle.min.js') }}"></script>
		<script src="{{ asset('front/js/tiny-slider.js') }}"></script>
		<script src="{{ asset('front/js/custom.js') }}"></script>
	</body>

</html>