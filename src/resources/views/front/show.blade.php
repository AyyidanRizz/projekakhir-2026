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

                    <!-- PERBAIKAN 3: Penataan tombol berdampingan & penambahan tombol Checkout -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-black btn-lg py-3 px-4">Add To Cart</button>
                        <a href="{{ url('/checkout') }}" class="btn btn-primary btn-lg py-3 px-4" style="background-color: #3b5d50; border-color: #3b5d50;">Checkout</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<!-- JavaScript Interaktif Menghitung Harga Varian -->
<script>
    document.getElementById('variant_select').addEventListener('change', function() {
        let selectedOption = this.options[this.selectedIndex];
        let price = selectedOption.getAttribute('data-price');
        let stock = selectedOption.getAttribute('data-stock');
        
        // Memastikan JavaScript memformat harga asli (yang sudah dikali 1000 dari blade)
        if (price) {
            let formattedPrice = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(price);

            document.getElementById('display-price').innerText = formattedPrice;
        }

        let stockInfo = document.getElementById('stock-info');
        if (stock) {
            stockInfo.innerHTML = `Stok tersisa: <strong>${stock}</strong> item.`;
        } else {
            stockInfo.innerText = "Silakan pilih varian untuk melihat ketersediaan stok.";
        }
    });
</script>
@endsection