@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết sản phẩm - {{ $product->name }}
@endsection
<!-- Lightbox2 CDN -->
<link href="https://cdn.jsdelivr.net/npm/lightbox2@2/dist/css/lightbox.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2/dist/js/lightbox-plus-jquery.min.js"></script>

@section('content')
    <main class="container">
        <section class="h-100 h-custom">
            <div class="mainDetailPro d-flex flex-wrap gap-4">
                @php $variants = $product->variants; @endphp
                @php $variant = $variants->first(); @endphp
                @php $variantStocks = $variants->pluck('stock', 'id'); @endphp

                {{-- Hình ảnh sản phẩm --}}
                <div class="detailPro-left" style="flex: 1; min-width: 300px;">
                    <div class="image-top mb-3">
                        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            style="width: 100%; max-width: 500px; height:500px; display: block; margin: 0 auto; object-fit: cover;">
                    </div>

                    @php
                        $gallery =
                            $product->images && $product->images->count()
                                ? $product->images->pluck('image_url')->toArray()
                                : [$product->image];
                    @endphp
                    @if (count($gallery))
                        <div class="album-wrapper d-flex align-items-center">
                            <button class="prev-btn">❮</button>
                            <div class="image-bottom overflow-hidden" style="flex: 1;">
                                <div class="image-track" style="transition: all 0.3s ease;">
                                    @foreach ($gallery as $img)
                                        <a href="{{ asset('storage/' . $img) }}" data-lightbox="gallery">
                                            <img src="{{ asset('storage/' . $img) }}" style="width: 150px; cursor: zoom-in;"
                                                alt="Gallery">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            <button class="next-btn">❯</button>
                        </div>
                    @endif
                </div>

                {{-- Thông tin sản phẩm --}}
                <div class="detailPro-right" style="flex: 1; min-width: 300px;">
                    <h3>{{ $product->name }}</h3>
                    <h5 class="text-danger fw-bold">Giá: {{ number_format($product->price) }} VNĐ</h5>
                    <p>{{ $product->description }}</p>
                    @php
                        $variants = $product->variants;
                    @endphp
                    @if ($variants->count())
                        @if ($variants->where('stock', '>', 0)->count())
                            @auth
                                @if (in_array(auth()->user()->role, ['admin', 'admin_branch']))
                                    <div class="row g-2 align-items-center">
                                        <div class="col-auto">
                                            <label for="variant_id">Thể tích:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select id="variant_id" class="form-select form-select-sm">
                                                @foreach ($variants as $variant)
                                                    <option value="{{ $variant->id }}">
                                                        {{ $variant->volume->name ?? 'Không rõ' }}{{ $variant->volume && $variant->volume->unit ? ' ' . $variant->volume->unit : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <label for="quantity">Số lượng:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="number" id="quantity" class="form-control form-control-sm"
                                                value="1" min="1" />
                                        </div>
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-dark icon-button" title="Thêm vào giỏ hàng"
                                                onclick="showAdminError()">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <span id="stockDisplay">Tồn kho: {{ $product->variants->first()->stock }}</span>
                                    <div class="mt-3">
                                        <button type="button"
                                            class="btn btn-dark d-flex align-items-center justify-content-center gap-2"
                                            onclick="showAdminError()">
                                            <span>Mua ngay</span>
                                        </button>
                                    </div>
                                @else
                                    <form action="{{ route('cart.add') }}" method="POST" class="mt-3" id="addToCartForm">
                                        @csrf
                                        <div class="row g-2 align-items-center">
                                            <div class="col-auto">
                                                <label for="variant_id">Thể tích:</label>
                                            </div>
                                            <div class="col-auto">
                                                <select name="product_variant_id" id="variant_id"
                                                    class="form-select form-select-sm">
                                                    @foreach ($variants as $variant)
                                                        <option value="{{ $variant->id }}">
                                                            {{ $variant->volume->name ?? 'Không rõ' }}{{ $variant->volume && $variant->volume->unit ? ' ' . $variant->volume->unit : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-auto">
                                                <label for="quantity">Số lượng:</label>
                                            </div>
                                            <div class="col-2">
                                                <input type="number" name="quantity" id="quantity"
                                                    class="form-control form-control-sm" value="1" min="1" />
                                            </div>
                                            <div class="col-auto">
                                                <button type="submit" class="btn btn-dark icon-button"
                                                    title="Thêm vào giỏ hàng">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <span id="stockDisplay">Tồn kho: {{ $product->variants->first()->stock }}</span>
                                    <div class="mt-3">
                                        <form action="{{ route('cart.buyNow') }}" method="POST" id="buyNowForm">
                                            @csrf
                                            <input type="hidden" name="product_variant_id" id="buy_now_variant_id"
                                                value="{{ $product->variants->first()->id }}">
                                            <input type="hidden" name="quantity" id="buy_now_quantity" value="1">
                                            <button type="submit"
                                                class="btn btn-dark d-flex align-items-center justify-content-center gap-2">
                                                <span>Mua ngay</span>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @else
                                <form action="{{ route('cart.add') }}" method="POST" class="mt-3" id="addToCartForm">
                                    @csrf
                                    <div class="row g-2 align-items-center">
                                        <div class="col-auto">
                                            <label for="variant_id">Thể tích:</label>
                                        </div>
                                        <div class="col-auto">
                                            <select name="product_variant_id" id="variant_id"
                                                class="form-select form-select-sm">
                                                @foreach ($variants as $variant)
                                                    <option value="{{ $variant->id }}">
                                                        {{ $variant->volume->name ?? 'Không rõ' }}{{ $variant->volume && $variant->volume->unit ? ' ' . $variant->volume->unit : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <label for="quantity">Số lượng:</label>
                                        </div>
                                        <div class="col-2">
                                            <input type="number" name="quantity" id="quantity"
                                                class="form-control form-control-sm" value="1" min="1" />
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-dark icon-button"
                                                title="Thêm vào giỏ hàng">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <span id="stockDisplay">Tồn kho: {{ $product->variants->first()->stock }}</span>
                                <div class="mt-3">
                                    <button type="button"
                                        class="btn btn-dark d-flex align-items-center justify-content-center gap-2"
                                        onclick="showLoginRequired()">
                                        <span>Mua ngay</span>
                                    </button>
                                </div>
                            @endauth

                            {{-- Hiển thị giá sau khi chọn variant --}}
                            <div class="mt-2">
                                <span id="variantPrice" class="fw-bold text-danger"></span>
                            </div>
                        @else
                            <span style="color: rgb(232, 184, 12); font-weight: bold;">Hết hàng</span>
                        @endif
                    @else
                        <p class="text-danger">Sản phẩm hiện chưa có phiên bản để bán.</p>
                    @endif
                </div>
            </div>

            {{-- ✅ Thông tin chi tiết nằm dưới full-width --}}
            <div class="information-product mt-5 w-100">
                <h4>Thông tin chi tiết</h4>
                <p>{{ $product->long_description ?? 'Đang cập nhật...' }}</p>
            </div>
            {{-- Sản phẩm liên quan --}}
            <div class="orther-product mt-5">
                <h2 class="mb-4 text-center">Sản phẩm khác</h2>
                <div class="product-wrapper">
                    <div class="products">
                        @forelse ($relatedProducts as $item)
                            <div class="product" style="border: 1px solid #d2d2d2">
                                <div class="image-product">
                                    <a href="{{ route('client.product.detail', $item->id) }}">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" />
                                    </a>
                                </div>
                                <h4>
                                    <a href="{{ route('client.product.detail', $item->id) }}" class="product-link"
                                        style="color: #000 !important">
                                        {{ $item->name }}
                                    </a>
                                </h4>
                                <p>{{ number_format($item->price) }} VNĐ</p>
                                @php $itemVariant = $item->variants->first(); @endphp
                                @if ($itemVariant)
                                    <div class="button-group">
                                        @auth
                                            @if (in_array(auth()->user()->role, ['admin', 'admin_branch']))
                                                <button type="button" class="btn-outline-cart" title="Thêm vào giỏ hàng"
                                                    onclick="showAdminError()">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                                <button type="button" class="btn-outline-buy" onclick="showAdminError()">Mua
                                                    ngay</button>
                                            @else
                                                <form action="{{ route('cart.add') }}" method="POST"
                                                    class="add-to-cart-form">
                                                    @csrf
                                                    <input type="hidden" name="product_variant_id"
                                                        value="{{ $itemVariant->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn-outline-cart"
                                                        title="Thêm vào giỏ hàng">
                                                        <i class="fas fa-cart-plus"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('cart.buyNow') }}" method="POST"
                                                    class="buy-now-form">
                                                    @csrf
                                                    <input type="hidden" name="product_variant_id"
                                                        value="{{ $itemVariant->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn-outline-buy">Mua ngay</button>
                                                </form>
                                            @endif
                                        @else
                                            <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                                @csrf
                                                <input type="hidden" name="product_variant_id"
                                                    value="{{ $itemVariant->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn-outline-cart" title="Thêm vào giỏ hàng">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('cart.buyNow') }}" method="POST" class="buy-now-form">
                                                @csrf
                                                <input type="hidden" name="product_variant_id"
                                                    value="{{ $itemVariant->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="button" class="btn-outline-buy"
                                                    onclick="showLoginRequired()">Mua ngay</button>
                                            </form>
                                        @endauth
                                    </div>
                                @else
                                    <p class="text-danger">Không có phiên bản để mua</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-center">Không có sản phẩm liên quan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Custom style và script cho album --}}
    <style>
        .prev-btn,
        .next-btn {
            width: 50px;
            height: 50px;
            background: rgba(232, 230, 225, 0.6);
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: absolute;
            top: 50%;
            z-index: 10;
            border-radius: 50%;
        }

        .prev-btn {
            left: 10px;
            color: #000
        }

        .next-btn {
            right: 10px;
            color: #000
        }

        #mainNav {
            background-color: #000;
        }

        .image-track {
            transition: transform 0.3s ease;
        }

        .button-group {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
            flex-wrap: nowrap;
            /* Giữ các nút trên cùng một dòng */
        }

        .btn {
            flex: 1;
            min-width: 0;
            padding: 8px 15px;
            white-space: nowrap;
            /* Ngăn chữ bị ngắt dòng */
        }

        /* Đảm bảo nút Mua ngay luôn full width và nhất quán */
        .btn.w-100 {
            width: 100% !important;
            margin-top: 10px;
        }

        /* Style nhất quán cho nút Mua ngay trong chi tiết sản phẩm */
        .detailPro-right .btn.w-100 {
            padding: 12px 24px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .detailPro-right .btn.btn-dark.w-100 {
            background-color: #000;
            border: 2px solid #000;
            color: #fff;
        }

        .detailPro-right .btn.btn-dark.w-100:hover {
            background-color: #333;
            border-color: #333;
        }

        .detailPro-right .btn.btn-danger.w-100 {
            background-color: #dc3545;
            border: 2px solid #dc3545;
            color: #fff;
        }

        .detailPro-right .btn.btn-danger.w-100:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        /* Style cho nút Mua ngay không full width (user đã đăng nhập) */
        .detailPro-right .btn:not(.w-100) {
            padding: 10px 20px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 14px;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .detailPro-right .btn.btn-dark:not(.w-100) {
            background-color: #000;
            border: 2px solid #000;
            color: #fff;
        }

        .detailPro-right .btn.btn-dark:not(.w-100):hover {
            background-color: #333;
            border-color: #333;
        }

        .detailPro-right .btn.btn-danger:not(.w-100) {
            background-color: #dc3545;
            border: 2px solid #dc3545;
            color: #fff;
        }

        .detailPro-right .btn.btn-danger:not(.w-100):hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        /* Mobile-first responsive styles */
        @media (max-width: 768px) {
            .mainDetailPro {
                flex-direction: column !important;
                gap: 2rem !important;
            }

            .detailPro-left,
            .detailPro-right {
                flex: none !important;
                min-width: 100% !important;
                width: 100% !important;
            }

            .detailPro-left {
                order: 1;
            }

            .detailPro-right {
                order: 2;
            }

            /* Responsive form layout */
            #addToCartForm .row {
                flex-direction: column;
                gap: 1rem;
            }

            #addToCartForm .col-auto,
            #addToCartForm .col-2 {
                width: 100% !important;
                max-width: 100% !important;
            }

            #addToCartForm label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
            }

            #addToCartForm select,
            #addToCartForm input {
                width: 100% !important;
                max-width: 100% !important;
            }

            /* Button styling for mobile */
            .button-group {
                flex-wrap: wrap;
                /* Cho phép ngắt dòng khi cần trên mobile */
            }

            .btn {
                width: auto;
                /* Tự động điều chỉnh chiều rộng */
                margin-bottom: 0.5rem;
                padding: 10px 15px;
                font-size: 1rem;
            }

            /* Đảm bảo nút Mua ngay luôn full width trên mobile */
            .btn.w-100 {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            /* Style nhất quán cho nút Mua ngay trên mobile */
            .detailPro-right .btn.w-100 {
                padding: 10px 20px;
                font-size: 13px;
            }

            /* Style cho nút Mua ngay không full width trên mobile */
            .detailPro-right .btn:not(.w-100) {
                padding: 8px 16px;
                font-size: 13px;
            }

            /* Gallery navigation for mobile */
            .album-wrapper {
                gap: 0.5rem;
            }

            .prev-btn,
            .next-btn {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 2px solid #ddd;
                background: white;
                font-size: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .prev-btn:hover,
            .next-btn:hover {
                background: #f8f9fa;
                border-color: #007bff;
            }

            .image-bottom img {
                width: 120px !important;
                height: 120px;
                object-fit: cover;
                border-radius: 8px;
                margin-right: 0.5rem;
            }

            /* Related products responsive */
            .orther-product .products {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
                gap: 1rem !important;
            }

            .product {
                margin-bottom: 1rem;
            }

            .product h4 {
                font-size: 1rem;
                line-height: 1.3;
            }

            .button-group {
                margin-top: auto;
                padding: 12px;
                justify-content: center;
                gap: 8px;
                background: #fff;
            }

            .btn-outline-cart,
            .btn-outline-buy {
                width: auto !important;
                text-align: center;
            }
        }

        /* Tablet styles */
        @media (min-width: 769px) and (max-width: 1024px) {
            .mainDetailPro {
                gap: 2rem !important;
            }

            .detailPro-left,
            .detailPro-right {
                min-width: 45% !important;
            }

            #addToCartForm .row {
                gap: 1rem;
            }

            .btn {
                padding: 0.6rem 1rem;
            }
        }

        /* Small mobile devices */
        @media (max-width: 480px) {
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .image-bottom img {
                width: 100px !important;
                height: 100px;
            }

            .prev-btn,
            .next-btn {
                width: 35px;
                height: 35px;
                font-size: 16px;
                background: #007bff;
            }

            h3 {
                font-size: 1.3rem;
            }

            h5 {
                font-size: 1.1rem;
            }

            .orther-product h2 {
                font-size: 1.5rem;
            }

            .button-group {
                flex-direction: row;
                /* Giữ ngang trên màn hình nhỏ */
                gap: 5px;
                /* Giảm khoảng cách trên màn hình nhỏ */
            }

            .btn {
                padding: 8px 10px;
                font-size: 0.9rem;
            }

            /* Đảm bảo nút Mua ngay luôn full width trên màn hình nhỏ */
            .btn.w-100 {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }

            /* Style nhất quán cho nút Mua ngay trên màn hình nhỏ */
            .detailPro-right .btn.w-100 {
                padding: 8px 16px;
                font-size: 12px;
            }

            /* Style cho nút Mua ngay không full width trên màn hình nhỏ */
            .detailPro-right .btn:not(.w-100) {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        /* Landscape mobile */
        @media (max-width: 768px) and (orientation: landscape) {
            .mainDetailPro {
                flex-direction: row !important;
                gap: 1.5rem !important;
            }

            .detailPro-left,
            .detailPro-right {
                min-width: 48% !important;
            }

            .button-group {
                flex-wrap: nowrap;
            }
        }
    </style>
    <script>
        const track = document.querySelector('.image-track');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');

        let scrollIndex = 0;
        const maxVisible = 4; // số ảnh hiển thị cùng lúc
        const imageWidth = 160; // bao gồm cả margin phải (150px + 10px)

        prevBtn.addEventListener('click', () => {
            if (scrollIndex > 0) {
                scrollIndex--;
                track.style.transform = `translateX(-${scrollIndex * imageWidth}px)`;
            }
        });

        nextBtn.addEventListener('click', () => {
            const totalImages = track.querySelectorAll('img').length;
            if (scrollIndex < totalImages - maxVisible) {
                scrollIndex++;
                track.style.transform = `translateX(-${scrollIndex * imageWidth}px)`;
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khi chọn variant, cập nhật cho "Mua ngay"
            const variantSelect = document.getElementById('variant_id');
            const buyNowVariantId = document.getElementById('buy_now_variant_id');
            if (variantSelect && buyNowVariantId) {
                variantSelect.addEventListener('change', function() {
                    buyNowVariantId.value = this.value;
                });
            }
            // Khi chọn số lượng, cập nhật cho "Mua ngay"
            const quantityInput = document.getElementById('quantity');
            const buyNowQuantity = document.getElementById('buy_now_quantity');
            if (quantityInput && buyNowQuantity) {
                quantityInput.addEventListener('input', function() {
                    buyNowQuantity.value = this.value;
                });
            }

            // Thay đổi ảnh đại diện khi chọn biến thể
            const mainImage = document.getElementById('mainImage');
            const variantImages = @json($variantImages ?? []);
            if (variantSelect && mainImage) {
                variantSelect.addEventListener('change', function() {
                    const selectedId = this.value;
                    const imgUrl = variantImages[selectedId];
                    if (imgUrl) {
                        mainImage.src = imgUrl; // Thay đổi ảnh đại diện thành ảnh biến thể
                    } else {
                        mainImage.src = "{{ Storage::url($product->image) }}"; // Giữ ảnh đại diện gốc
                    }
                });
            }
        });
        $(function() {
            $('.btn-buy-now[type="button"]').on('click', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Bạn chưa đăng nhập!',
                    text: 'Vui lòng đăng nhập để sử dụng chức năng "Mua ngay".',
                    showConfirmButton: true,
                    confirmButtonText: 'Đăng nhập'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('login') }}";
                    }
                });
            });
        });
    </script>
    <script>
        $(function() {
            $('#addToCartForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
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
                        if (response.cart_count !== undefined) {
                            $('#cartCount').text(response.cart_count);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra, vui lòng thử lại!';
                        if (xhr.status === 400 && xhr.responseJSON) {
                            const res = xhr.responseJSON;
                            if (res.reached_max) {
                                message = res.message ||
                                'Bạn đã thêm tối đa số lượng sản phẩm.';
                            } else if (res.message) {
                                message = res.message;
                            }
                        }

                        Swal.fire({
                            icon: 'warning',
                            title: 'Cảnh báo!',
                            text: message,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                        });
                    }
                });
            });
        });
        $(function() {
            $('.add-to-cart-form').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
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
                        if (response.cart_count !== undefined) {
                            $('#cartCount').text(response.cart_count);
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra, vui lòng thử lại!';
                        if (xhr.status === 400 && xhr.responseJSON) {
                            const res = xhr.responseJSON;
                            if (res.reached_max) {
                                message = res.message ||
                                'Bạn đã thêm tối đa số lượng sản phẩm.';
                            } else if (res.message) {
                                message = res.message;
                            }
                        }
                        Swal.fire({
                            icon: 'warning',
                            title: 'Cảnh báo!',
                            text: message,
                        });
                    }
                });
            });
        });
    </script>
    <script>
        $(function() {
            // Lấy dữ liệu tồn kho từ PHP
            const variantStocks = @json($variantStocks);

            // Function cập nhật tồn kho
            function updateStockDisplay() {
                const variantSelect = document.getElementById('variant_id');
                const stockDisplay = document.getElementById('stockDisplay');

                if (variantSelect && stockDisplay) {
                    const selectedVariantId = variantSelect.value;
                    const stock = variantStocks[selectedVariantId] !== undefined ? parseInt(variantStocks[
                        selectedVariantId]) : 0;

                    // Cập nhật hiển thị tồn kho
                    stockDisplay.textContent = `Tồn kho: ${stock}`;

                    // Cập nhật max value cho input quantity
                    const quantityInput = document.getElementById('quantity');
                    if (quantityInput) {
                        quantityInput.max = stock;
                        if (parseInt(quantityInput.value) > stock) {
                            quantityInput.value = stock;
                        }
                    }

                    // Cập nhật buy now form
                    const buyNowVariantId = document.getElementById('buy_now_variant_id');
                    if (buyNowVariantId) {
                        buyNowVariantId.value = selectedVariantId;
                    }
                }
            }

            // Cập nhật tồn kho khi chọn variant
            $('#variant_id').on('change', function() {
                updateStockDisplay();
            });

            // Cập nhật tồn kho khi thay đổi số lượng
            $('#quantity').on('input', function() {
                const variantSelect = document.getElementById('variant_id');
                const selectedVariantId = variantSelect.value;
                const stock = variantStocks[selectedVariantId] !== undefined ? parseInt(variantStocks[
                    selectedVariantId]) : 0;
                const quantity = parseInt(this.value) || 0;

                if (quantity > stock) {
                    this.value = stock;
                }
            });

            // Validate tồn kho khi ấn nút Mua ngay
            const buyNowForm = document.getElementById('buyNowForm');
            if (buyNowForm) {
                buyNowForm.addEventListener('submit', function(e) {
                    const variantSelect = document.getElementById('variant_id');
                    const quantityInput = document.getElementById('quantity');
                    const selectedVariantId = variantSelect ? variantSelect.value : buyNowForm
                        .querySelector('#buy_now_variant_id').value;
                    const stock = variantStocks[selectedVariantId] !== undefined ? parseInt(variantStocks[
                        selectedVariantId]) : 0;
                    const quantity = quantityInput ? parseInt(quantityInput.value) : parseInt(buyNowForm
                        .querySelector('#buy_now_quantity').value);
                    if (quantity > stock) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Vượt quá tồn kho',
                            text: `Chỉ còn ${stock} sản phẩm trong kho.`,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                        });
                        return false;
                    }
                });
            }
        });
    </script>

    <script>
        function showAdminError() {
            Swal.fire({
                icon: 'warning',
                title: 'Cảnh báo!',
                text: 'Bạn không có quyền thực hiện hành động này',
                confirmButtonText: 'Đóng',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            });
        }

        function showLoginRequired() {
            Swal.fire({
                icon: 'warning',
                title: 'Bạn chưa đăng nhập!',
                text: 'Vui lòng đăng nhập để sử dụng chức năng "Mua ngay".',
                showConfirmButton: true,
                confirmButtonText: 'Đăng nhập',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}";
                }
            });
        }
    </script>
@endsection
