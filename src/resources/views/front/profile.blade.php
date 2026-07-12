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
                                <p class="mb-3">Wujudkan Produk Custom Impianmu dengan Proses yang Transparan dan Sesuai Syariah</p>
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

<div class="untree_co-section" style="background-color: #fafafa; padding: 60px 0;">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success mx-auto mb-4" style="max-width: 850px;">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
            @csrf
            @method('PUT')
            
            <div class="profile-card">
                
                <div class="d-flex align-items-center mb-5">
                    <div class="avatar-wrapper me-4">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ asset('storage/' . Auth::user()->avatar_url) }}" class="avatar-img" id="avatar-preview">
                        @else
                            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim(Auth::user()->email))) }}?d=mp&s=150" class="avatar-img" id="avatar-preview">
                        @endif
                        
                        <label for="avatar-input" class="avatar-edit-label">
                            <svg viewBox="0 0 24 24">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                        </label>
                        <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewImage(this)">
                    </div>
                    
                    <div>
                        <h3 class="mb-1" style="font-weight: 600; color: #2d2d2d; font-size: 1.4rem;">{{ Auth::user()->name }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                            {{ Auth::user()->email }}
                        </p>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', Auth::user()->name) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" class="form-control" value="{{ old('name', Auth::user()->name) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', Auth::user()->phone) }}" placeholder="(+98) 9123728167">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', Auth::user()->address) }}" placeholder="e.g. New York, USA">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Province</label>
                            <input type="text" name="province" class="form-control" value="{{ old('province', Auth::user()->province) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', Auth::user()->postal_code) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', Auth::user()->city) }}">
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-save-profile">
                        Save Changes
                    </button>
                </div>
                
            </div>
        </form>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>@endsection