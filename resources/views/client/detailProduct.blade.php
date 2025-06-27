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
                <div class="detailPro-left">
                    <div class="image-top mb-3">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            style="max-width: 100%;">
                    </div>

                    {{-- Nếu bạn có nhiều ảnh thì dùng album --}}
                    @if (!empty($product->gallery))
                        <div class="album-wrapper">
                            <button class="prev-btn">❮</button>
                            <div class="image-bottom overflow-hidden">
                                <div class="image-track d-flex gap-2" style="transition: all 0.3s ease;">
                                    @foreach (json_decode($product->gallery) as $img)
                                        <img src="{{ asset('storage/' . $img) }}" style="width: 150px;" alt="Gallery Image">
                                    @endforeach
                                </div>
                            </div>
                            <button class="next-btn">❯</button>
                        </div>
                    @endif
                </div>

                {{-- Thông tin sản phẩm --}}
                <div class="detailPro-right">
                    <h3>{{ $product->name }}</h3>
                    <h5 class="text-danger fw-bold">Giá: {{ number_format($product->price) }} đ</h5>
                    <p>{{ $product->description }}</p>

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

            {{-- Thông tin chi tiết --}}
            <div class="information-product mt-5">
                <h4>Thông tin chi tiết</h4>
                <p>{{ $product->details ?? 'Đang cập nhật...' }}</p>
            </div>

            {{-- Sản phẩm liên quan --}}
            <div class="orther-product mt-5">
                <h2 class="mb-4">Sản phẩm khác</h2>
                <div class="products row">
                    @forelse ($relatedProducts as $item)
                        <div class="col-6 col-md-3 mb-4">
                            <div class="card h-100 text-center">
                                <a href="{{ route('client.product.detail', $item->id) }}"
                                    class="text-decoration-none text-dark">
                                    <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top"
                                        style="height: 200px; object-fit: cover;" alt="{{ $item->name }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $item->name }}</h5>
                                        <p class="card-text text-danger fw-bold">{{ number_format($item->price) }} đ</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @empty
                        <p>Không có sản phẩm liên quan.</p>
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
        const track = document.querySelector('.image-track');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');
        let scrollIndex = 0;
        const imageWidth = 160; // 150px ảnh + 10px margin

        if (track) {
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
    </script>
@endsection
