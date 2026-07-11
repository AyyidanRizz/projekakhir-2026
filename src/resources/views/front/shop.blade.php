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
                                <h1 style="font-size: 2.5rem;">Katalog</h1>
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
        
        <div class="container mb-5">
            <div class="row">
                <div class="col-12 col-md-6 col-lg-5">
                    <form method="GET"
                        action="{{ route('front.shop') }}"
                        id="filterForm">
                        <div class="row align-items-center">
                            <div class="col-9">
                                <input
                                    type="text"
                                    class="form-control"
                                    name="search"
                                    placeholder="Cari produk..."
                                    style="border-radius: 20px !important; height: 45px;"
                                    value="{{ request('search') }}">
                            </div>
                        <div class="col-auto">
                            <button
                                type="submit"
                                class="btn btn-dark d-inline-flex align-items-center justify-content-center p-0"
                                style="border-radius: 20px !important; height: 45px; width: 45px; line-height: 0;">
                                
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 20px; height: 20px; display: block;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                                </svg>

                            </button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="container">
            {{-- Search --}}
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3">
                    <div class="border rounded p-4 bg-white">
                        <h5 class="mb-4">
                            Filter Produk
                        </h5>
                        <h6 class="mb-3">
                            Kategori
                        </h6>
                        
                        <div class="category-filters">
                            @foreach($categories as $category)
                                <div class="form-check d-flex align-items-center mb-2">
                                    <input
                                        type="checkbox"
                                        name="category"
                                        value="{{ $category->id }}"
                                        id="cat-{{ $category->id }}"
                                        class="category-checkbox mr-2 me-2" 
                                        style="margin-right: 10px; cursor: pointer;" 
                                        {{ request('category') == $category->id ? 'checked' : '' }}>
                                    <label
                                        class="mb-0 {{ request('category') == $category->id ? 'font-weight-bold text-dark' : 'text-secondary' }}"
                                        for="cat-{{ $category->id }}"
                                        style="cursor: pointer;">
                                        {{ $category->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Tombol reset jika ada filter aktif (Diposisikan di dalam div border pembungkus) --}}
                        @if(request('category'))
                            <div class="mt-3">
                                <a href="{{ route('front.shop', ['search' => request('search')]) }}" class="btn btn-sm btn-dark text-white w-100" style="border-radius: 20px !important;">
                                    Hapus Filter
                                </a>
                            </div>
                        @endif
                    </div>
                </div> {{-- Pembungkus kolom col-lg-3 ditutup pas di sini tanpa sisa tag --}}

                {{-- Produk: Sekarang otomatis sejajar di kanan sidebar --}}
                <div class="col-lg-9">
                    <div class="row">
                        @foreach($products as $product)
                            <div class="col-12 col-md-6 col-lg-4 mb-5">
                                <a class="product-item"
                                href="{{ route('front.shop.detail', $product) }}">
                                    <img
                                        src="{{ asset('storage/'.$product->image) }}"
                                        class="img-fluid product-thumbnail product-image-square"
                                        alt="{{ $product->name }}">
                                    <h3 class="product-title">
                                        {{ $product->name }}
                                    </h3>
                                    <strong class="product-price text-dark d-block mt-2">
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
        </div>
    </div> {{-- Penutup untree_co-section --}}

<script>
document.querySelectorAll('.category-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const form = document.getElementById('filterForm');
            
            const existingInput = form.querySelector('input[name="category"]');
            if(existingInput) existingInput.remove();

            if(this.checked) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'category';
                hiddenInput.value = this.value;
                form.appendChild(hiddenInput);
            }
            form.submit();
        });
    });
</script>
@endsection