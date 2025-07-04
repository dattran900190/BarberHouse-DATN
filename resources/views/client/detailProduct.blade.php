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
                @php $variant = $product->variants->first(); @endphp

                {{-- Hình ảnh sản phẩm --}}
                <div class="detailPro-left" style="flex: 1; min-width: 300px;">
                    <div class="image-top mb-3">
                        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto;">

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
                    <h5 class="text-danger fw-bold">Giá: {{ number_format($product->price) }} đ</h5>
                    <p>{{ $product->description }}</p>
                    @php
                        $variants = $product->variants;
                    @endphp
                    @if ($variants->count())
                        <form action="{{ route('cart.add') }}" method="POST" class="mt-3" id="variantForm">
                            @csrf
                            <label for="variant_id" class="me-2">Chọn thể tích:</label>
                            <select name="product_variant_id" id="variant_id" class="form-select d-inline-block w-auto">
                                @foreach ($variants as $variant)
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                        data-volume="{{ $variant->volume->name ?? '' }}"
                                        data-unit="{{ $variant->volume->unit ?? '' }}">
                                        {{ $variant->volume->name ?? 'Không rõ' }}{{ $variant->volume && $variant->volume->unit ? ' ' . $variant->volume->unit : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="quantity" class="me-2 ms-3">Số lượng:</label>
                            <input type="number" name="quantity" id="quantity"
                                class="form-control-sm d-inline-block w-auto" value="1" min="1" />

                            <button type="submit" class="btn btn-dark ms-3" class="icon-button" style="margin-top: 20px"><i class="fa-solid fa-cart-plus"></i></button>
                        </form>

                        <form action="{{ route('cart.buyNow') }}" method="POST" class="d-inline" id="buyNowForm"
                            style="margin-left:10px;">
                            @csrf
                            <input type="hidden" name="product_variant_id" id="buy_now_variant_id"
                                value="{{ $product->variants->first()->id }}">
                            <input type="hidden" name="quantity" id="buy_now_quantity" value="1">
                            @guest
                                <button type="button" class="btn btn-success btn-buy-now" style="margin-top: 20px">Mua
                                    ngay</button>
                            @else
                                <button type="submit" class="btn btn-success btn-buy-now" style="margin-top: 20px">Mua
                                    ngay</button>
                            @endguest
                        </form>



                        <div class="mt-2">
                            <span id="variantPrice" class="fw-bold text-danger"></span>
                        </div>
                    @else
                        <p class="text-danger">Sản phẩm hiện chưa có phiên bản để bán.</p>
                    @endif
                </div>

            </div>


            {{-- ✅ Thông tin chi tiết nằm dưới full-width --}}
            <div class="information-product mt-5 w-100">
                <h4>Thông tin chi tiết</h4>
                <p>{{ $product->details ?? 'Đang cập nhật...' }}</p>
            </div>
            {{-- Sản phẩm liên quan --}}
            <div class="orther-product mt-5">
                <h2 class="mb-4 text-center">Sản phẩm khác</h2>
                <div class="container">
                    <div class="row justify-content-center">
                        @forelse ($relatedProducts as $item)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-4">
                                <div class="card text-center h-100">
                                    <a href="{{ route('client.product.detail', $item->id) }}"
                                        class="text-decoration-none text-dark">
                                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                                            style="height: 150px; object-fit: cover;" alt="{{ $item->name }}">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $item->name }}</h6>
                                            <p class="card-text text-danger fw-bold">{{ number_format($item->price) }} đ
                                            </p>
                                            <button type="submit" class="btn-add-to-cart icon-button"
                                                title="Thêm vào giỏ hàng"><i class="fa-solid fa-cart-plus"></i></button>
                                        </div>
                                    </a>
                                    <div class="action-buttons mt-auto mb-3 d-flex justify-content-center gap-2">
                                        @php $variant = $item->variants->first(); @endphp
                                        @if ($variant)
                                            <form action="{{ route('cart.add') }}" method="POST" class="m-0 p-0">
                                                @csrf
                                                <input type="hidden" name="product_variant_id"
                                                    value="{{ $variant->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-dark ms-3"
                                                    style="margin-top: 20px">🛒</button>
                                            </form>
                                            <form action="{{ route('cart.buyNow') }}" method="POST"
                                                class="m-0 p-0 buy-now-form-related">
                                                @csrf
                                                <input type="hidden" name="product_variant_id"
                                                    value="{{ $variant->id }}">
                                                <input type="hidden" name="quantity" value="1">
                                                @guest
                                                    <button type="button" class="btn btn-success btn-buy-now"
                                                        style="margin-top: 20px">Mua</button>
                                                @else
                                                    <button type="submit" class="btn btn-success btn-buy-now"
                                                        style="margin-top: 20px">Mua</button>
                                                @endguest
                                            </form>
                                        @endif
                                    </div>
                                </div>
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
        #mainNav {
            background-color: #000;
        }

        .image-track {
            transition: transform 0.3s ease;
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

@endsection
