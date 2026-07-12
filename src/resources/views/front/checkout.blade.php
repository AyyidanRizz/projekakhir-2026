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
                              <h1 style="font-size: 2.5rem;">Checkout</h1>
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

        <div class="untree_co-section">
            <div class="container">

			@if(session('success_checkout'))
				<div style="background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; padding: 20px; border-radius: 5px; margin-bottom: 25px; text-align: center;">
					<h4 style="margin-top: 0; font-weight: bold;">🎉 Checkout Berhasil!</h4>
					<p>{{ session('success_checkout') }}</p>
                    <a href="{{ url('/') }}" class="btn btn-black btn-sm mt-2">Kembali Belanja</a>
				</div>
			@endif

            {{-- MODIFIKASI TAMPILAN: Form hanya muncul jika keranjang tidak kosong --}}
            @if(!empty($cart))

            <!-- Pembuka Form -->
            <form action="{{ route('checkout.store') }}"
                method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="row">
                  <div class="col-md-6 mb-5 mb-md-0">
                    <h2 class="h3 mb-3 text-black">
                        Informasi Pemesan
                    </h2>
                    <div class="p-4 border bg-white rounded">
                        {{-- Nama --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Nama
                            </label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                value="{{ old('name', Auth::user()->name) }}"
                                readonly>
                        </div>
                        {{-- Email --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Email
                            </label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="{{ old('email', Auth::user()->email) }}"
                                readonly>
                        </div>
                        {{-- Nomor HP --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Nomor HP
                            </label>
                            <input
                                type="text"
                                name="phone"
                                class="form-control"
                                value="{{ old('phone', Auth::user()->phone) }}"
                                required>
                        </div>
                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Alamat Lengkap
                            </label>
                            <textarea
                                name="address"
                                rows="4"
                                class="form-control"
                                required>{{ old('address', Auth::user()->address) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Provinsi
                                </label>
                                <input
                                    type="text"
                                    name="province"
                                    class="form-control"
                                    value="{{ old('province', Auth::user()->province) }}"
                                    required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    Kota
                                </label>
                                <input
                                    type="text"
                                    name="city"
                                    class="form-control"
                                    value="{{ old('city', Auth::user()->city) }}"
                                    required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">
                                Kode Pos
                            </label>
                            <input
                                type="text"
                                name="postal_code"
                                class="form-control"
                                value="{{ old('postal_code', Auth::user()->postal_code) }}"
                                required>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">
                                Ekspedisi
                            </label>
                            <select 
                                name="courier"
                                class="form-control"
                                required>
                                <option value="">
                                    Pilih Ekspedisi
                                </option>
                                @foreach($couriers as $courier)
                                    <option value="{{ $courier->value }}">
                                        {{ $courier->getLabel() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">
                                Catatan Pesanan
                            </label>
                            <textarea
                                name="note"
                                rows="4"
                                class="form-control">{{ old('note') }}</textarea>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">
                                Upload Desain Kustom
                            </label>
                            <input
                                type="file"
                                name="design_file"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required>
                            <small class="text-muted">
                                Format yang didukung:
                                JPG, JPEG, PNG, PDF.
                            </small>
                            @error('design_file')
                                <div class="text-danger mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                  </div>
                  <div class="col-md-6">

                    <div class="row mb-5">
                      <div class="col-md-12">
                        <h2 class="h3 mb-3 text-black">Your Order</h2>
                        <div class="p-3 p-lg-5 border bg-white">
                          <table class="table site-block-order-table mb-5">
                            <thead>
                              <th>Product</th>
                              <th>Total</th>
                            </thead>
                            <tbody>
                              {{-- Loop data produk --}}
                              @foreach($cart as $id => $item)
                              <tr>
                                <td>{{ $item['name'] }} <strong class="mx-2">x</strong> {{ $item['quantity'] }}</td>
                                <td>Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                              </tr>
                              @endforeach
                              <tr>
                                <td>
                                    Total Quantity
                                </td>
                                <td>
                                    {{ $totalQuantity }} pcs
                                </td>
                            </tr>
                            @if($totalQuantity < 12)
                                <tr>
                                    <td>Akad</td>
                                    <td>
                                        Salam
                                        <input type="hidden" name="akad" value="salam">
                                    </td>
                                </tr>
                            @else
                              <tr>
                                  <td>Jenis Akad</td>
                                  <td>
                                      {{-- Pembungkus utama menggunakan Flexbox agar berjejer ke samping dengan jarak (gap-2) --}}
                                      <div class="d-flex gap-2 align-items-center">
                                          
                                          {{-- Opsi Istishna --}}
                                          <div class="form-check p-0 m-0">
                                              <input 
                                                  class="akad-radio d-none" 
                                                  type="radio" 
                                                  name="akad" 
                                                  value="istishna" 
                                                  id="akadIstishna" 
                                                  checked>
                                              {{-- Kamu bisa ubah nilai 10px di bawah ini untuk mengatur kelengkungan ujung tombol --}}
                                              <label class="akad-box text-center m-0" for="akadIstishna" style="border-radius: 25px; display: inline-block; cursor: pointer;">
                                                  Istishna
                                              </label>
                                          </div>

                                          {{-- Opsi Salam --}}
                                          <div class="form-check p-0 m-0">
                                              <input 
                                                  class="akad-radio d-none" 
                                                  type="radio" 
                                                  name="akad" 
                                                  value="salam" 
                                                  id="akadSalam">
                                              {{-- Kamu bisa ubah nilai 10px di bawah ini untuk mengatur kelengkungan ujung tombol --}}
                                              <label class="akad-box text-center m-0" for="akadSalam" style="border-radius: 25px; display: inline-block; cursor: pointer;">
                                                  Salam
                                              </label>
                                          </div>

                                      </div>
                                  </td>
                              </tr>
                            @endif
                              {{-- Menampilkan Subtotal --}}
                                <tr>
                                    <td class="text-black font-weight-bold"><strong>Cart Subtotal</strong></td>
                                    <td class="text-black">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</td>
                                </tr>
                              {{-- Menampilkan Total Akhir --}}
                                @if($totalQuantity >= 12)
                                <tbody id="paymentDetail">
                                <tr>
                                    <td>
                                        <strong>DP</strong>
                                    </td>
                                    <td>
                                        Rp 
                                        <span id="dpAmount">
                                            {{ number_format($totalBelanja * 0.5,0,',','.') }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>Pelunasan</strong>
                                    </td>
                                    <td>
                                        Rp 
                                        <span id="remainingAmount">
                                            {{ number_format($totalBelanja * 0.5,0,',','.') }}
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                                @endif
                          </table>
                        <h5 class="mb-3">
                            Metode Pembayaran
                        </h5>
                        <div class="border p-3 mb-3">
                            <div class="form-check mb-3">
                                <input 
                                    class="form-check-input payment-radio"
                                    type="radio"
                                    name="payment_method"
                                    value="virtual_account"
                                    id="virtualAccount"
                                    checked>
                                <label 
                                    class="form-check-label"
                                    for="virtualAccount">
                                    Virtual Account
                                </label>
                            </div>
                            <div 
                                id="vaDetail"
                                class="mt-3">
                                <p class="mb-1">
                                    Nomor Virtual Account:
                                </p>
                                <div class="input-group">
                                    <input 
                                        type="text"
                                        class="form-control"
                                        id="vaNumber"
                                        value="88081234567890"
                                        readonly>
                                    <button 
                                        type="button"
                                        class="btn btn-dark"
                                        onclick="copyVA()">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="border p-3 mb-5">
                            <div class="form-check">
                                <input 
                                    class="form-check-input payment-radio"
                                    type="radio"
                                    name="payment_method"
                                    value="qris"
                                    id="qris">
                                <label 
                                    class="form-check-label"
                                    for="qris">
                                    QRIS
                                </label>
                            </div>
                            <div 
                                id="qrisDetail"
                                class="text-center mt-3"
                                style="display:none">
                                <p>
                                    Scan QR Code untuk pembayaran
                                </p>
                                <img 
                                    src="{{ asset('front/images/foto/photo-profile.jpeg') }}"
                                    width="200"
                                    alt="QRIS">
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-black btn-lg py-3 btn-block" type="submit">Place Order</button>
                        </div>

                        </div>
                      </div>
                    </div>

                  </div>
                </div>

              </form> <!-- Penutup Form -->
            @endif

            </div>
          </div>

          

<script>
document.addEventListener('DOMContentLoaded', function(){
    const total = {{ $totalBelanja }};
    const akadRadio = document.querySelectorAll('.akad-radio');
    const dp = document.getElementById('dpAmount');
    const remaining = document.getElementById('remainingAmount');
    const paymentDetail = document.getElementById('paymentDetail');
    function updateAkad(){
        const selected = document.querySelector(
            'input[name="akad"]:checked'
        );
        if(!selected) return;
        if(selected.value === 'istishna'){
            let dpValue = total * 0.5;
            if(paymentDetail){
                paymentDetail.style.display = "table-row-group";
            }
            dp.innerHTML = dpValue.toLocaleString('id-ID');
            remaining.innerHTML = dpValue.toLocaleString('id-ID');
        }else{
            if(paymentDetail){
                paymentDetail.style.display = "none";
            }
        }
    }
    akadRadio.forEach(function(radio){
        radio.addEventListener('change', updateAkad);
    });
    updateAkad();
    const paymentRadio = document.querySelectorAll('.payment-radio');
    const vaDetail = document.getElementById('vaDetail');
    const qrisDetail = document.getElementById('qrisDetail');
    paymentRadio.forEach(function(payment){
        payment.addEventListener('change', function(){
            if(this.value === 'virtual_account'){
                vaDetail.style.display = "block";
                qrisDetail.style.display = "none";
            }
            if(this.value === 'qris'){
                vaDetail.style.display = "none";
                qrisDetail.style.display = "block";
            }
        });
    });
});
function copyVA(){
    let va = document.getElementById('vaNumber');
    navigator.clipboard.writeText(va.value);
    alert("Nomor Virtual Account berhasil disalin");
}
</script>
@endsection