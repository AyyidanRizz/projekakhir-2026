@extends('front.layouts.master')

@section('content')
<div class="untree_co-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 mb-5 mb-md-0">
                <div class="bg-light p-5 text-center rounded">
                    <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="{{ $product->name }}">
                </div>
            </div>
            
            <div class="col-md-6">
                <h2 class="h3 text-black mb-3 fw-bold font-weight-bold">{{ $product->name }}</h2>
                
                <p class="lead text-black mb-3 fw-bold font-weight-bold">
                    <span id="display-price">Rp {{ number_format($product->base_price * 1000, 0, ',', '.') }}</span>
                </p>
                
                <div class="mb-4">
                    <p>{{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}</p>
                </div>

                <hr>

                <form id="product-form" action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-black font-weight-bold" for="variant_select">Pilih Varian (Ukuran & Bahan):</label>
                            <select class="form-control" id="variant_select" name="variant_id" required>
                                <option value="" data-price="{{ $product->base_price * 1000 }}">-- Pilih Ukuran & Bahan --</option>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price * 1000 }}" data-stock="{{ $variant->stock }}">
                                        {{ $variant->size ?? 'All Size' }} - {{ $variant->material ?? 'Bahan Standar' }} 
                                        (Stok: {{ $variant->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <p class="text-muted" id="stock-info">Silakan pilih varian untuk melihat ketersediaan stok.</p>

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

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" id="add-to-cart-btn" class="btn btn-black btn-lg py-3 px-4">Add To Cart</button>
                        <button type="button" id="checkout-direct-btn" class="btn btn-primary btn-lg py-3 px-4" style="background-color: #3b5d50; border-color: #3b5d50;">Checkout Langsung</button>
                    </div>

                    <script>
                        // Memperbaiki logika pembelokan: ubah tipe ke button agar tidak langsung submit acak
                        document.getElementById('checkout-direct-btn').addEventListener('click', function(e) {
                            let form = document.getElementById('product-form');
                            // Ubah action ke buynow
                            form.action = "{{ route('cart.buynow', $product->id) }}";
                            // Kirim form
                            form.submit();
                            
                            // Kembalikan action semula ke cart.add setelah 0.5 detik agar jika user klik BACK, form sudah normal
                            setTimeout(() => {
                                form.action = "{{ route('cart.add', $product->id) }}";
                            }, 500);
                        });

                        // JavaScript interaktif tombol Tambah/Kurang Quantity
                        let qtyInput = document.getElementById('quantity_input');
                        let maxStock = 999;

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
            maxStock = parseInt(stock);
            qtyInput.value = 1;
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

    // PERBAIKAN UTAMA: Memaksa halaman reload bersih jika diakses dari tombol back/forward browser
    window.addEventListener("pageshow", function (event) {
        var historyTraversal = event.persisted || 
                               (typeof window.performance != "undefined" && 
                                window.performance.navigation.type === 2);
        if (historyTraversal) {
            window.location.reload();
        }
    });
</script>
@endsection