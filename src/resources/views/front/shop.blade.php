@extends('front.layouts.master')

@section('content')

<!-- Start Hero Section -->
<div class="hero" style="padding: 30px 0 !important;">
    <div class="container">
        <div class="row justify-content-between">
            <div class="col-lg-5">
                <div class="intro-excerpt">
                    <h1 style="font-size: 2.5rem;">Katalog</h1>
                    <p class="mb-3">
                        Donec vitae odio quis nisl dapibus malesuada. Nullam ac aliquet velit.
                        Aliquam vulputate velit imperdiet dolor tempor tristique.
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

<!-- Search -->
<div class="container" style="margin-top: 50px; margin-bottom: 50px;">
    <div class="row">
        <div class="col-12 col-md-6 col-lg-5">
            <form method="GET" action="{{ route('front.shop') }}" id="filterForm">
                <div class="row align-items-center">
                    <div class="col-9">
                        <input
                            type="text"
                            class="form-control"
                            name="search"
                            placeholder="Cari produk..."
                            value="{{ request('search') }}"
                            style="border-radius:20px!important;height:45px;">
                    </div>

                    <div class="col-auto">
                        <button
                            type="submit"
                            class="btn btn-dark d-inline-flex align-items-center justify-content-center p-0"
                            style="border-radius:20px!important;height:45px;width:45px;">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                style="width:20px;height:20px;">
                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Product Section -->
<div class="product-section pt-0">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-5">
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
                                    id="cat-{{ $category->id }}"
                                    class="category-checkbox me-2"
                                    value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'checked' : '' }}>
                                <label
                                    for="cat-{{ $category->id }}"
                                    class="mb-0 {{ request('category') == $category->id ? 'fw-bold text-dark' : 'text-secondary' }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @if(request('category'))
                        <div class="mt-3">
                            <a
                                href="{{ route('front.shop',['search'=>request('search')]) }}"
                                class="btn btn-dark btn-sm w-100"
                                style="border-radius:20px;">
                                Hapus Filter
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <!-- Produk -->
            <div class="col-lg-9">
                <div class="row">
                    @forelse($products as $product)
                        <div class="col-12 col-md-6 col-lg-4 mb-5">
                            <a
                                class="product-item"
                                href="{{ route('front.shop.detail', $product) }}">
                                <img
                                    src="{{ $product->image ? asset('storage/'.$product->image) : asset('front/images/product-1.png') }}"
                                    class="img-fluid product-thumbnail"
                                    alt="{{ $product->name }}">
                                <h3 class="product-title">
                                    {{ $product->name }}
                                </h3>
                                <strong class="product-price">
                                    Rp {{ number_format($product->base_price * 1000,0,',','.') }}
                                </strong>
                                <span class="icon-cross">
                                    <img
                                        src="{{ asset('front/images/cross.svg') }}"
                                        class="img-fluid">
                                </span>
                            </a>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <h5>Produk tidak ditemukan.</h5>
                        </div>

                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.category-checkbox').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        const form = document.getElementById('filterForm');
        const oldInput = form.querySelector('input[name="category"]');
        if (oldInput) {
            oldInput.remove();
        }
        if (this.checked) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'category';
            input.value = this.value;
            form.appendChild(input);
        }
        form.submit();
    });
});
</script>
@endsection