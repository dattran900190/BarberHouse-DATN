@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết sản phẩm - {{ $product->name }}
@endsection

@section('content')
    <main class="container">
        <section class="h-100 h-custom">
           <div class="mainDetailPro d-flex flex-wrap gap-4">
    @php $variant = $product->variants->first(); @endphp

    {{-- Hình ảnh sản phẩm --}}
<div class="detailPro-left" style="flex: 1; min-width: 300px;">
    <div class="image-top mb-3">
        <img id="mainImage" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
            style="max-width: 100%;">
    </div>

    {{-- Album ảnh sản phẩm --}}
   @php
    // Nếu gallery có dữ liệu thì lấy từ DB, nếu không thì chỉ lấy ảnh đại diện
    $gallery = $product->gallery ? json_decode($product->gallery) : [$product->image];
@endphp
    @if (count($gallery))
        <div class="album-wrapper d-flex align-items-center">
            <button class="prev-btn btn btn-light">❮</button>
            <div class="image-bottom overflow-hidden" style="flex: 1;">
                <div class="image-track d-flex gap-2" style="transition: all 0.3s ease;">
                    @foreach ($gallery as $img)
                        <img src="{{ asset('storage/' . $img) }}"
                             onclick="document.getElementById('mainImage').src = '{{ asset('storage/' . $img) }}'"
                             style="width: 80px; cursor: pointer;" alt="Gallery">
                    @endforeach
                </div>
            </div>
            <button class="next-btn btn btn-light">❯</button>
        </div>
    @endif
</div>

    {{-- Thông tin sản phẩm --}}
    <div class="detailPro-right" style="flex: 1; min-width: 300px;">
        <h3>{{ $product->name }}</h3>
        <h5 class="text-danger fw-bold">Giá: {{ number_format($product->price) }} đ</h5>
        <p>{{ $product->description }}</p>

        @php $variant = $product->variants->first(); @endphp
        @if ($variant)
            <form action="{{ route('cart.add') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                <label for="quantity" class="me-2">Số lượng:</label>
                <input type="number" name="quantity" id="quantity"
                    class="form-control-sm d-inline-block w-auto" value="1" min="1" />
                <button type="submit" class="btn btn-dark ms-3">🛒 Thêm vào giỏ hàng</button>
            </form>
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
    <div class="d-flex justify-content-center flex-wrap gap-4">
        @forelse ($relatedProducts as $item)
            <div class="card text-center" style="width: 200px;">
                <a href="{{ route('client.product.detail', $item->id) }}" class="text-decoration-none text-dark">
                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                        style="height: 200px; object-fit: cover;" alt="{{ $item->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->name }}</h5>
                        <p class="card-text text-danger fw-bold">{{ number_format($item->price) }} đ</p>
                          <button type="submit" class="btn-add-to-cart" title="Thêm vào giỏ hàng">
                                        🛒
                                    </button>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-center">Không có sản phẩm liên quan.</p>
        @endforelse
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
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.querySelector('.image-track');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        let scrollIndex = 0;
        const imageWidth = 160; // 150px ảnh + 10px margin

        if (track && prevBtn && nextBtn) {
            prevBtn.addEventListener('click', () => {
                if (scrollIndex > 0) {
                    scrollIndex--;
                    track.style.transform = `translateX(-${scrollIndex * imageWidth}px)`;
                }
            });

            nextBtn.addEventListener('click', () => {
                const totalImages = track.querySelectorAll('img').length;
                if (scrollIndex < totalImages - 4) {
                    scrollIndex++;
                    track.style.transform = `translateX(-${scrollIndex * imageWidth}px)`;
                }
            });
        }
    });
</script>
@endsection
