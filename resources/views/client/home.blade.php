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
        @php
            $skillLevels = [
                'assistant' => 'Thử việc',
                'junior' => 'Sơ cấp',
                'senior' => 'Chuyên nghiệp',
                'master' => 'Bậc thầy',
                'expert' => 'Chuyên gia',
            ];
            // $skillLevelColors = [
            //     'assistant' => 'secondary',
            //     'junior' => 'info',
            //     'senior' => 'primary',
            //     'master' => 'success',
            //     'expert' => 'warning',
            // ];
        @endphp
        <section id="barbers" class="mt-4" style="padding-bottom: 0 ;">
            <h2 class="text-uppercase">Đội ngũ thợ cắt tóc</h2>
            <div id="barbers-list"></div>
            <div class="text-center mt-3">
                <a href="{{ route('client.listBarber') }}" class="btn-outline-cart">Xem thêm</a>
            </div>
        </section>


        <section id="mainPost">
            <h2 class="text-uppercase">Bài viết nổi bật</h2>
            <div class="posts-wrapper">
                <button class="prev-posts">‹</button>
                <div class="posts-track"> <!-- Thêm lớp track -->
                    <div class="posts">
                        @foreach ($featuredPosts as $post)
                            <div class="post">
                                <div class="image-container">
                                    <a href="{{ route('client.detailPost', $post->slug) }}">
                                        <img src="{{ Storage::url($post->image) }}" alt="{{ $post->title }}">
                                    </a>
                                </div>
                                <h4 style="font-weight: bold; text-transform: uppercase;"><a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title }}</a></h4>
                                <p>
                                    <a href="{{ route('client.detailPost', $post->slug) }}">
                                        {{ Str::limit(strip_tags($post->short_description), 50) }}
                                    </a>
                                </p>
                            </div>
                        @endforeach
                    </div>
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
                        <h4 style="font-weight: bold; text-transform: uppercase;"><a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title }}</a></h4>
                        <p><a
                                href="{{ route('client.detailPost', $post->slug) }}">{{ Str::limit(strip_tags($post->short_description), 50) }}</a>
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('client.posts') }}" class="btn-outline-cart">Xem thêm</a>
            </div>
        </section>

        <section id="product">
            <div class="product-wrapper">
                <h2 class="text-uppercase">Sản phẩm Baber House</h2>
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
                            <p>{{ number_format($product->price) }} VNĐ</p>

                            @php
                                $variant = $product->variants->where('stock', '>', 0)->first();
                            @endphp
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
                                            <button type="submit" class="btn-outline-buy">Mua ngay</button>
                                        @else
                                            <button type="submit" class="btn-outline-buy">Mua ngay</button>
                                        @endguest
                                    </form>
                                </div>
                            @else
                                <span style="color: rgb(232, 184, 12); font-weight: bold; text-align: center;">Hết
                                    hàng</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-3">
                    <a href="{{ route('client.product') }}" class="btn-outline-cart">Xem thêm</a>
                </div>
            </div>
        </section>

        <section id="price" style="padding-bottom: 0; padding-top: 90px;">
            <h2 class="text-uppercase" style="font-weight: bold;">Bảng giá</h2>
            <img src="{{ asset('storage/' . ($imageSettings['bang_gia'] ?? 'default-images/no-banggia.png')) }}"
                alt="Bảng giá" />
        </section>

        <section id="instagram">
            <h2 class="text-uppercase" style="font-weight: bold;">Ảnh khách hàng</h2>
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

    <style>
        #mainPost h2 {
            font-weight: bold;
        }

        .post-nomal {
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .post-nomal img {
            border-radius: 8px;
        }

        .post-nomal:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .posts-wrapper {
            position: relative;
            overflow: hidden;
            max-width: 1000px;
            margin: auto;
        }

        .posts-track {
            overflow: hidden;
        }

        .posts {
            display: flex;
            transition: transform 0.5s ease;
            width: 100%;
        }

        .posts .post {
            flex: 0 0 calc(100% / 3);
            /* 3 bài, trừ 2 khoảng cách */
            box-sizing: border-box;
            padding: 10px;
        }

        .posts .post .image-container {
            overflow: hidden;
            border-radius: 8px;
        }


        .posts img {
            border-radius: 8px;
        }

        .posts img:hover {
            transform: scale(1.05);
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        #barbers h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 2rem;
        }

        .barbers {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .barbers {
                grid-template-columns: 1fr;
                gap: 15px;
                margin-bottom: 30px;
            }

            .image-barber {
                height: 250px;
            }

            .image-barber img {
                height: 250px;
            }

            #barbers h2 {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .barber-info {
                padding: 12px 8px;
            }

            .barber-info h4 {
                font-size: 1.1rem;
            }

            .barber-name-link {
                font-size: 1.1rem;
            }

            .branch-link {
                font-size: 0.9rem;
            }

            .barber-info p {
                font-size: 0.9rem;
                margin-bottom: 5px;
            }

            /* Responsive cho bài viết nổi bật */
            .posts .post {
                flex: 0 0 100%;
                padding: 8px;
            }

            .posts-wrapper {
                max-width: 100%;
                margin: 0;
            }

            .posts-track {
                margin: 0 10px;
            }

            #mainPost h2 {
                font-size: 1.5rem;
                margin-bottom: 1.5rem;
            }

            /* Responsive cho bài viết thường */
            .posts-nomal {
                display: grid;
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .post-nomal {
                margin-bottom: 15px;
            }

            .post-nomal h4 {
                font-size: 1.1rem;
                margin: 8px 0;
            }

            .post-nomal p {
                font-size: 0.9rem;
                line-height: 1.4;
            }

            .image-nomal {
                height: 200px;
                overflow: hidden;
            }

            .image-nomal img {
                width: 100%;
                height: 200px;
                object-fit: cover;
            }
            .images {
                display: grid;
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .image-item {
                width: 100%;
                height: 220px;
                overflow: hidden;
                position: relative;
            }
            .image-item img {
                width: 100%;
                height: 220px;
                object-fit: cover;
                border-radius: 8px;
            }
            /* Bảng giá: ảnh to, không khung, không bo góc, không max-width */
            #price img {
                width: 100%;
                height: 500px;
                object-fit: cover;
                border-radius: 0;
                max-width: none;
                margin: 0;
                box-shadow: none;
                display: block;
            }
        }

        .barber {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }

        .barber:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .image-barber {
            width: 100%;
            height: 380px;
            overflow: hidden;
        }

        .image-barber img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .barber:hover .image-barber img {
            transform: scale(1.05);
        }

        .barber-info {
            padding: 15px 10px;
        }

        .barber-info h4 {
            margin-bottom: 8px;

        }

        .barber-profile {
            font-size: 0.9rem;
            color: #555;
        }

        .btn-xem-them {
            display: inline-block;
            padding: 10px 20px;
            background: #000;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn-xem-them:hover {
            background: #444;
        }

        /* Styling cho link tên thợ cắt */
        .barber-name-link {
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 600;
        }

        /* .barber-name-link:hover {
            color: #28a745;

            transform: scale(1.02);
        } */

        /* Styling cho link chi nhánh */
        .branch-link {
            color: #666;
            text-decoration: none;
            transition: color 0.3s ease;
            font-weight: 500;
            padding: 2px 6px;
            border-radius: 4px;
            background-color: #f8f9fa;
        }

        /* .branch-link:hover {
            color: #28a745;
            background-color: #e9ecef;
        } */

        /* Styling cho thông báo không có thợ cắt */
        #barbers-list .text-center p {
            color: #666;
            font-size: 1.1rem;
            font-style: italic;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px dashed #dee2e6;
        }
    </style>
@endsection

@section('scripts')
    @parent
    <script>
        const skillLevels = {
            'assistant': 'Thử việc',
            'junior': 'Sơ cấp',
            'senior': 'Chuyên nghiệp',
            'master': 'Bậc thầy',
            'expert': 'Chuyên gia'
        };
        function renderBarbers(barbers) {
            if (barbers.length === 0) {
                $('#barbers-list').html('<div class="text-center"><p>Hiện tại không có thợ cắt tóc nào đang làm việc.</p></div>');
                return;
            }

            let html = '<div class="barbers">';
            barbers.forEach(barber => {
                html += `<div class="barber">
                    <div class="image-barber">
                        <img src="/storage/${barber.avatar}" alt="${barber.name}">
                    </div>
                    <div class="barber-info">
                        <h4><a href="/tho-cat/${barber.id}" class="barber-name-link">${barber.name}</a></h4>
                        <p><span class="label">Kỹ năng:</span>
                            <span class="me-2 mb-2"><b>${skillLevels[barber.skill_level] ?? 'Không xác định'}</b></span>
                        </p>
                        <p><span class="label">Chi nhánh:</span>
                            ${barber.branch ? `<a href="/chi-nhanh/${barber.branch.id}" class="branch-link">${barber.branch.name}</a>` : 'N/A'}
                        </p>
                        <p><span class="label">Đánh giá:</span> ${Number(barber.rating_avg).toFixed(1)}/5
                            <i class="fa-solid fa-star" style="color: #ffd700;"></i>
                        </p>
                    </div>
                </div>`;
            });
            html += '</div>';
            $('#barbers-list').html(html);
        }
        function fetchBarbers() {
            $.get('/api/barbers', function(data) {
                renderBarbers(data);
            });
        }
        $(document).ready(function() {
            fetchBarbers();
        });
    </script>
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
    <script>
        $(function() {
            const $posts = $('.posts .post');
            const $track = $('.posts');
            let postsPerSlide = 3;
            let totalSlides = Math.ceil($posts.length / postsPerSlide);
            let currentSlide = 0;

            function updateSlide() {
                if (window.innerWidth <= 768) {
                    postsPerSlide = 1;
                    totalSlides = $posts.length;
                    const postWidth = $posts.outerWidth(true);
                    const distance = postWidth * currentSlide;
                    $track.css('transform', `translateX(-${distance}px)`);
                } else {
                    postsPerSlide = 3;
                    totalSlides = Math.ceil($posts.length / postsPerSlide);
                    const postWidth = $posts.outerWidth(true);
                    const distance = postWidth * postsPerSlide * currentSlide;
                    $track.css('transform', `translateX(-${distance}px)`);
                }
            }

            $('.next-posts').on('click', function() {
                if (currentSlide < totalSlides - 1) {
                    currentSlide++;
                    updateSlide();
                }
            });
            $('.prev-posts').on('click', function() {
                if (currentSlide > 0) {
                    currentSlide--;
                    updateSlide();
                }
            });

            // Swipe for mobile
            let startX = 0;
            let endX = 0;
            let isMobile = window.innerWidth <= 768;
            function handleSwipe() {
                const swipeThreshold = 50;
                const diff = startX - endX;
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0 && currentSlide < totalSlides - 1) {
                        currentSlide++;
                        updateSlide();
                    } else if (diff < 0 && currentSlide > 0) {
                        currentSlide--;
                        updateSlide();
                    }
                }
            }
            $('.posts-wrapper').on('touchstart', function(e) {
                if (isMobile) {
                    startX = e.originalEvent.touches[0].clientX;
                }
            });
            $('.posts-wrapper').on('touchend', function(e) {
                if (isMobile) {
                    endX = e.originalEvent.changedTouches[0].clientX;
                    handleSwipe();
                }
            });
            $(window).on('resize', function() {
                isMobile = window.innerWidth <= 768;
                updateSlide();
            });
            // Khởi tạo đúng slide khi load
            updateSlide();
        });
    </script>
    <script>
        if (window.Echo) {
            window.Echo.channel('barbers')
                .listen('.BarberUpdated', (e) => {
                    fetchBarbers();
                });
        }
    </script>



    <script>
        $(function() {
            // Slide 1 ảnh khách hàng trên mobile
            const $customerImages = $('.images .image-item');
            const $imagesTrack = $('.images');
            let currentImage = 0;
            let totalImages = $customerImages.length;
            function updateImageSlide() {
                if (window.innerWidth <= 768) {
                    const imgWidth = $customerImages.outerWidth(true);
                    const distance = imgWidth * currentImage;
                    $imagesTrack.css('transform', `translateX(-${distance}px)`);
                } else {
                    $imagesTrack.css('transform', 'none');
                }
            }
            // Swipe cho ảnh khách hàng
            let startXImg = 0;
            let endXImg = 0;
            let isMobileImg = window.innerWidth <= 768;
            function handleImageSwipe() {
                const swipeThreshold = 50;
                const diff = startXImg - endXImg;
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0 && currentImage < totalImages - 1) {
                        currentImage++;
                        updateImageSlide();
                    } else if (diff < 0 && currentImage > 0) {
                        currentImage--;
                        updateImageSlide();
                    }
                }
            }
            $('.images').on('touchstart', function(e) {
                if (isMobileImg) {
                    startXImg = e.originalEvent.touches[0].clientX;
                }
            });
            $('.images').on('touchend', function(e) {
                if (isMobileImg) {
                    endXImg = e.originalEvent.changedTouches[0].clientX;
                    handleImageSwipe();
                }
            });
            $(window).on('resize', function() {
                isMobileImg = window.innerWidth <= 768;
                updateImageSlide();
            });
            // Khởi tạo đúng slide khi load
            updateImageSlide();
        });
    </script>
@endsection
