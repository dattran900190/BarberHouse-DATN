@extends('layouts.ClientLayout')

@section('title-page')
    Trang chủ Baber House
@endsection

@section('slider')
    <section class="hero-slider">
        @foreach ($banners as $index => $banner)
            <div class="slide {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/' . $banner->image_url) }}" alt="{{ $banner->title }}" />
            </div>
        @endforeach

        <!-- optional prev/next buttons -->
        <button class="prev">‹</button>
        <button class="next">›</button>
    </section>
@endsection


@section('content')
    <main class="container">
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: '{{ session('success') }}',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        // timer: 3000,
                        timerProgressBar: true
                    });
                });
            </script>
        @endif

        <section id="mainPost">
            <h2>Tin tức nổi bật</h2>
            <div class="posts-wrapper">
                <button class="prev-posts">‹</button>
                <div class="posts">
                    @foreach ($featuredPosts as $post)
                        <div class="post">
                            <div class="image-container">
                                <a href="{{ route('client.detailPost', $post->slug) }}">
                                    <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}">
                                </a>
                            </div>
                            <h4><a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title }}</a></h4>
                            <p><a
                                    href="{{ route('client.detailPost', $post->slug) }}">{{ Str::limit(strip_tags($post->short_description), 50) }}</a>
                            </p>
                        </div>
                    @endforeach
                </div>
                <button class="next-posts">›</button>
            </div>

            <div class="posts-nomal">
                @foreach ($normalPosts as $post)
                    <div class="post-nomal">
                        <div class="image-nomal">
                            <a href="{{ route('client.detailPost', $post->slug) }}">
                                <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}">
                            </a>
                        </div>
                        <h4><a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title }}</a></h4>
                        <p><a
                                href="{{ route('client.detailPost', $post->slug) }}">{{ Str::limit(strip_tags($post->short_description), 50) }}</a>
                        </p>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('client.posts') }}" style="text-decoration: none"><button id="loadMore"
                    class="btn-xem-them">Xem thêm</button></a>
        </section>

        <section id="product">
            <div class="product-wrapper">
                <h2>Sản phẩm Baber House</h2>
                <div class="products">
                    @foreach ($products as $product)
                        <div class="product">
                            <div class="image-product">
                                <a href="{{ route('client.product.detail', $product->id) }}">
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" />
                                </a>
                            </div>
                            <h4>
                                <a href="{{ route('client.product.detail', $product->id) }}" class="product-link">
                                    {{ $product->name }}
                                </a>
                            </h4>
                            <p>{{ number_format($product->price) }} đ</p>

                            @php $variant = $product->variants->first(); @endphp

                            @if ($variant)
                                <div class="button-group">
                                    <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn-outline-cart" title="Thêm vào giỏ hàng">
                                            <i class="fas fa-cart-plus"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('cart.buyNow') }}" method="POST" class="buy-now-form">
                                        @csrf
                                        <input type="hidden" name="product_variant_id" value="{{ $variant->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        @guest
                                            <button type="button" class="btn-outline-buy">Mua ngay</button>
                                        @else
                                            <button type="submit" class="btn-outline-buy">Mua ngay</button>
                                        @endguest
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <a href="{{ route('client.product') }}" style="text-decoration: none"><button id="loadMore"
                        class="btn-xem-them">Xem thêm</button></a>
            </div>
        </section>

        <section id="price">
            <h2>Bảng giá</h2>
            <img src="{{ asset('storage/' . ($imageSettings['bang_gia'] ?? 'default-images/no-banggia.png')) }}"
                alt="Bảng giá" />
        </section>

        <section id="instagram">
            <h2>Instagram</h2>
            <div class="images">
                @forelse ($customerImages as $image)
                    <div class="image-item">
                        <img src="{{ asset('storage/' . $image->image) }}" loading="lazy" alt="Ảnh khách hàng">
                    </div>
                @empty
                    <p class="text-muted">Chưa có ảnh khách hàng nào.</p>
                @endforelse
            </div>
        </section>

    </main>
@endsection
@section('css')
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
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            timer: 1500,
                            showConfirmButton: false
                        });
                        if (res.cart_count !== undefined) {
                            $('#cartCount').text(res.cart_count);
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
                return false;
            });
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
