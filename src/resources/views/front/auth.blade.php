@extends('front.layouts.master')

@section('content')
<div class="untree_co-section before-footer-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="h3 mb-3 text-black text-center">Login ke Akun Anda</h2>
                <p class="text-center mb-4">Silakan masuk menggunakan akun pelanggan yang telah terdaftar.</p>
                
                <div class="p-3 p-lg-5 border bg-white rounded shadow-sm">
                    <!-- Tampilkan Pesan Error jika Login Gagal -->
                    @if ($errors->has('email'))
                        <div class="alert alert-danger py-2">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        <div class="form-group row mb-3">
                            <div class="col-md-12">
                                <label for="email" class="text-black">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required autofocus>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <div class="col-md-12">
                                <label for="password" class="text-black">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-black btn-lg py-3 btn-block w-100">Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection