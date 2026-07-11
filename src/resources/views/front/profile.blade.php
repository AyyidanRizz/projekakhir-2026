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
								<h1 style="font-size: 2.5rem;">Profile</h1>
								<p class="mb-3">Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit. Aliquam vulputate velit imperdiet dolor tempor tristique.</p>
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
		<!-- End Hero Section -->

<div class="untree_co-section">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-4 border rounded bg-white">
                
                <!-- BAGIAN FOTO PROFIL (DI PALING ATAS FORM) -->
                <div class="text-center mb-4">
                    @if(Auth::user()->avatar_url)
                        <img
                            src="{{ asset('storage/' . Auth::user()->avatar_url) }}"
                            class="rounded-circle mb-3"
                            width="150"
                            height="150"
                            style="object-fit:cover;">
                    @else
                        <img
                            src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(Auth::user()->email))) }}?d=mp&s=150"
                            class="rounded-circle mb-3">
                    @endif

                    <div class="mx-auto" style="max-width: 300px;">
                        <input
                            type="file"
                            name="avatar"
                            class="form-control">
                    </div>
                </div>
                <!-- END BAGIAN FOTO PROFIL -->

                <div class="mb-3">
                    <label>Nama</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', Auth::user()->name) }}">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input
                        type="email"
                        class="form-control"
                        value="{{ Auth::user()->email }}"
                        readonly>
                </div>
                <div class="mb-3">
                    <label>No HP</label>
                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="{{ old('phone', Auth::user()->phone) }}">
                </div>
                <div class="mb-3">
                    <label>Alamat</label>
                    <textarea
                        name="address"
                        class="form-control">{{ old('address', Auth::user()->address) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label>Provinsi</label>
                        <input
                            type="text"
                            name="province"
                            class="form-control"
                            value="{{ old('province', Auth::user()->province) }}">
                    </div>
                    <div class="col-md-6">
                        <label>Kota</label>
                        <input
                            type="text"
                            name="city"
                            class="form-control"
                            value="{{ old('city', Auth::user()->city) }}">
                    </div>
                </div>
                <div class="mt-3">
                    <label>Kode Pos</label>
                    <input
                        type="text"
                        name="postal_code"
                        class="form-control"
                        value="{{ old('postal_code', Auth::user()->postal_code) }}">
                </div>
                <button class="btn btn-black mt-4">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection