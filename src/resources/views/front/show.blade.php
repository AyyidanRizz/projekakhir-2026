@extends('front.layouts.master')

@section('content')
<div class="hero">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1>{{ $product->name }}</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="untree_co-section">
    <div class="container">
        <div class="row">
            <!-- Sisi Kiri: Gambar Produk -->
            <div class="col-md-6 mb-5 mb-md-0">
                <div class="bg-light p-5 text-center rounded">
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->name }}">
                </div>
            </div>
            
            <!-- Sisi Kanan: Informasi & Pilihan Varian -->
            <div class="col-md-6">
                <h2 class="h3 text-black mb-3 fw-bold font-weight-bold">{{ $product->name }}</h2>
                
                <!-- PERBAIKAN 1: Harga dasar dikali 1000 agar menjadi Rupiah asli -->
                <p class="lead text-black mb-3 fw-bold font-weight-bold">
                    <span id="display-price">Rp {{ number_format($product->base_price * 1000, 0, ',', '.') }}</span>
                </p>
                
                <div class="mb-4">
                    <p>{{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}</p>
                </div>

                <hr>

                <!-- Form Action diarahkan ke route cart.add -->
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <!-- Menyimpan ID produk utama secara hidden -->
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-black font-weight-bold" for="variant_select">Pilih Varian (Ukuran & Bahan):</label>
                            <select class="form-control" id="variant_select" name="variant_id" required>
                                <!-- data-price dikali 1000 untuk fallback default -->
                                <option value="" data-price="{{ $product->base_price * 1000 }}">-- Pilih Ukuran & Bahan --</option>
                                @foreach($product->variants as $variant)
                                    <!-- PERBAIKAN 2: data-price varian juga dikali 1000 -->
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price * 1000 }}" data-stock="{{ $variant->stock }}">
                                        {{ $variant->size ?? 'All Size' }} - {{ $variant->material ?? 'Bahan Standar' }} 
                                        (Stok: {{ $variant->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Informasi Stok Real-time -->
                    <p class="text-muted" id="stock-info">Silakan pilih varian untuk melihat ketersediaan stok.</p>

                    <!-- FITUR BARU: Input Jumlah Barang (Quantity) -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="text-black font-weight-bold" for="quantity_input">Jumlah Barang:</label>
                            <div class="input-group" style="width: 140px;">
                                <button class="btn btn-outline-black btn-sm py-2 px-3" type="button" id="btn-minus">&minus;</button>
                                <input type="number" class="form-control text-center py-2" id="quantity_input" name="quantity" value="1" min="1" required readonly style="background-color: #fff;">
                                <button class="btn btn-outline-black btn-sm py-2 px-3" type="button" id="btn-plus">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-black btn-lg py-3 px-4">Add To Cart</button>
                        <button type="submit" id="checkout-direct-btn" class="btn btn-primary btn-lg py-3 px-4" style="background-color: #3b5d50; border-color: #3b5d50;">Checkout Langsung</button>
                    </div>

                    <script>
                        // Logika pembelokan action form saat tombol Checkout Langsung ditekan
                        document.getElementById('checkout-direct-btn').addEventListener('click', function(e) {
                            let form = this.closest('form');
                            form.action = "{{ route('cart.buynow', $product->id) }}";
                        });

                        // JavaScript interaktif tombol Tambah/Kurang Quantity
                        let qtyInput = document.getElementById('quantity_input');
                        let maxStock = 999; // Default max stock sebelum varian dipilih

                        document.getElementById('btn-plus').addEventListener('click', function() {
                            let currentVal = parseInt(qtyInput.value);
                            if (currentVal < maxStock) {
                                qtyInput.value = currentVal + 1;
                            }
                        });

                        document.getElementById('btn-minus').addEventListener('click', function() {
                            let currentVal = parseInt(qtyInput.value);
                            if (currentVal > 1) {
                                qtyInput.value = currentVal - 1;
                            }
                        });
                    </script>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- Struktur Pop-up Toast -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 80px;">
    <div id="cartToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000" style="background-color: #3b5d50;">
        <div class="d-flex">
            <div class="toast-body">
                <span id="toast-message">🛒 Produk berhasil ditambahkan!</span>
                <a href="{{ route('cart.index') }}" class="text-white ms-2 fw-bold text-decoration-underline">Lihat Keranjang</a>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- JavaScript Interaktif Menghitung Harga Varian -->
<script>
    document.getElementById('variant_select').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let price = selectedOption.getAttribute('data-price');
        let stock = selectedOption.getAttribute('data-stock');
        
        if (price) {
            let formattedPrice = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(price);

            document.getElementById('display-price').innerText = formattedPrice;
        }

        let stockInfo = document.getElementById('stock-info');
        let qtyInput = document.getElementById('quantity_input');

        if (stock) {
            maxStock = parseInt(stock); // Set batas maksimum input qty sesuai stok database
            qtyInput.value = 1; // Reset ke 1 saat ganti varian
            stockInfo.innerHTML = `Stok tersisa: <strong>${stock}</strong> item.`;
        } else {
            maxStock = 999;
            stockInfo.innerText = "Silakan pilih varian untuk melihat ketersediaan stok.";
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        @if(session('success'))
            let toastEl = document.getElementById('cartToast');
            document.getElementById('toast-message').innerHTML = "🛒 {{ session('success') }}";
            toastEl.style.backgroundColor = "#2c483d";
            let toast = new bootstrap.Toast(toastEl);
            toast.show();
        @endif

        @if(session('error'))
            let toastEl = document.getElementById('cartToast');
            document.getElementById('toast-message').innerHTML = "⚠️ {{ session('error') }}";
            toastEl.style.backgroundColor = "#dc3545";
            let toast = new bootstrap.Toast(toastEl);
            toast.show();
        @endif
    });
</script>
@endsection