@extends('layouts.ClientLayout')

@section('title-page')
    Trang chủ Baber House
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
        <!-- optional prev/next buttons -->
        <button class="prev">‹</button>
        <button class="next">›</button>
    </section>
@endsection

@section('content')
    <main class="container">
        <section id="mainPost">
            <h2>Tin tức nổi bật</h2>
            <div class="posts-wrapper">
                <button class="prev-posts">‹</button>
                <div class="posts">
                    @foreach ($posts as $post)
                        <div class="post">
                            <div class="image-container">
                                <a href="{{ route('client.detailPost', $post->slug) }}">
                                    <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}">
                                </a>
                            </div>
                            <h4><a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title }}</a></h4>
                            <p><a
                                    href="{{ route('client.detailPost', $post->slug) }}">{{ Str::limit(strip_tags($post->content), 50) }}</a>
                            </p>
                        </div>
                    @endforeach
                </div>
                <button class="next-posts">›</button>
            </div>

            <div class="posts-nomal">
                @foreach ($posts as $post)
                    <div class="post-nomal">
                        <div class="image-nomal">
                            <a href="{{ route('client.detailPost', $post->slug) }}">
                                <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}">
                            </a>
                        </div>
                        <h4><a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title }}</a></h4>
                        <p><a
                                href="{{ route('client.detailPost', $post->slug) }}">{{ Str::limit(strip_tags($post->content), 50) }}</a>
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
                                <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <input type="hidden" name="product_variant_id"
                                        value="{{ $product->default_variant_id ?? $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-to-cart icon-button" title="Thêm vào giỏ hàng">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </form>
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
            <img src="{{ asset('images/bang_gia.png') }}" alt="" />
        </section>

        <section id="instagram">
            <h2>Instagram</h2>
            <div class="images">
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
                <div class="image-item"><img src="/img/post2.png" alt="" /></div>
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
