@extends('layouts.ClientLayout')

@section('title-page')
    Sản phẩm Baber House
@endsection

@section('slider')
    <section class="hero-slider">
        <div class="slide active">
            <img src="https://4rau.vn/upload/hinhanh/cover-fb-10th-collection-0744.png" alt="Slide 1" />
        </div>
        <div class="slide">
            <img src="https://4rau.vn/upload/hinhanh/z4459651440290_1e4a90c27fc15cc175132ecd94872e98-2870.jpg"
                alt="Slide 2" />
        </div>
        <div class="slide">
            <img src="https://4rau.vn/upload/hinhanh/z6220937549697_8ae15d51c35246081cf6bc8d60780126-1254.jpg"
                alt="Slide 3" />
        </div>
        <button class="prev">‹</button>
        <button class="next">›</button>
    </section>
@endsection

@section('content')
    <main class="container-fluid px-5">
        <section id="product">
            <div class="product-wrapper w-100">
                <h2 class="mb-4">Sản phẩm</h2>

                {{-- FORM LỌC --}}
                <form method="GET" action="{{ route('client.product') }}" class="row g-3 mb-5">
                    {{-- Danh mục --}}
                    <div class="col-md-3">
                        <label for="filter-category" class="form-label">Loại sản phẩm</label>
                        <select id="filter-category" name="category" onchange="this.form.submit()" class="form-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach ($globalCategories as $cate)
                                <option value="{{ $cate->id }}"
                                    {{ request('category') == $cate->id ? 'selected' : '' }}>
                                    {{ $cate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Giá --}}
                    <div class="col-md-3">
                        <label for="filter-price" class="form-label">Khoảng giá</label>
                        <select id="filter-price" name="price_range" onchange="this.form.submit()" class="form-select">
                            <option value="">Tất cả giá</option>
                            <option value="0-100" {{ request('price_range') == '0-100' ? 'selected' : '' }}>Dưới 100k
                            </option>
                            <option value="100-200" {{ request('price_range') == '100-200' ? 'selected' : '' }}>100k–200k
                            </option>
                            <option value="200-500" {{ request('price_range') == '200-500' ? 'selected' : '' }}>200k–500k
                            </option>
                            <option value="500-9999" {{ request('price_range') == '500-9999' ? 'selected' : '' }}>Trên 500k
                            </option>
                        </select>
                    </div>
                </form>

                {{-- DANH SÁCH SẢN PHẨM --}}
                <div class="row">

                    @forelse ($products as $product)
                        <div class="col-6 col-md-3 mb-4">
                            <div class="card h-100 text-center">
                                <a href="{{ route('client.product.detail', $product->id) }}"
                                    class="text-decoration-none text-dark">

                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                        class="card-img-top" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $product->name }}</h5>
                                        <p class="card-text text-danger fw-bold">{{ number_format($product->price) }} đ</p>

                                    </div>
                                </a>
                                <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="product_variant_id"
                                        value="{{ $product->default_variant_id ?? $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-to-cart icon-button" title="Thêm vào giỏ hàng">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-center">Không có sản phẩm nào.</p>
                    @endforelse
                </div>

                {{-- PHÂN TRANG --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        </section>
    </main>
@endsection


@section('scripts')
    <script>
        $(function() {
            $('.add-to-cart-form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                $.ajax({
                    url: "{{ route('cart.add') }}",
                    method: "POST",
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Đã thêm vào giỏ hàng!',
                            timer: 1500,
                            customClass: {
                                popup: 'custom-swal-popup' // CSS
                            },
                            showConfirmButton: false
                        });
                        if (res.cart_count !== undefined) {
                            $('#cartCount').text(res.cart_count);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra, vui lòng thử lại!'
                        });
                    }
                });
                return false;
            });
        });
    </script>
@endsection
