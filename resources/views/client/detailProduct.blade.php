@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Chi tiết sản phẩm Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-100 h-custom">
            <div class="mainDetailPro">
                <div class="detailPro-left">
                    <div class="image-top">
                        <img src="https://4rau.vn/upload/sanpham/download7547_250x250.jpeg" alt="">
                    </div>
                    <div class="album-wrapper">
                        <button class="prev-btn">❮</button>
                        <div class="image-bottom">
                            <div class="image-track">
                                <img src="https://4rau.vn/upload/sanpham/download7547_250x250.jpeg" alt="">
                                <img src="https://4rau.vn/upload/sanpham/download7547_250x250.jpeg" alt="">
                                <img src="https://4rau.vn/upload/sanpham/download7547_250x250.jpeg" alt="">
                                <img src="https://4rau.vn/upload/sanpham/download7547_250x250.jpeg" alt="">
                                <img src="https://4rau.vn/upload/sanpham/download7547_250x250.jpeg" alt="">
                            </div>
                        </div>
                        <button class="next-btn">❯</button>
                    </div>
                </div>

                <div class="detailPro-right">
                    <h3>BROSH CLAY FIBER POMADE (mini)</h3>
                    <h5>Giá: 295.000 vnđ</h5>
                    <p>Một loại pomade mới kết hợp giữa 2 dòng best seller
                        nhất của BROSH tại Việt Nam, cho ra đời phiên bản lai
                        giống không quá cứng,
                        nhưng không hề làm tóc bẹp mềm nhanh chóng!</p>
                    <p>
                        Số lượng: <input type="number" class=" form-control-sm mx-2 quantity-input" id="quantity-input"
                            value="1" min="1" />
                    </p>

                    <a href="" class="btn btn-dark">Thêm vào giỏ hàng</a>
                </div>
            </div>

            <div class="information-product">
                <h4>Thông tin chi tiết</h4>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                    Nostrum beatae expedita quasi harum suscipit, voluptatum ad modi at,
                    mollitia sapiente pariatur illum cumque iusto nobis. Excepturi, cum. Illum, quasi ipsa.</p>
            </div>

            <div class="orther-product">
                <h2>Sản phẩm khác</h2>
                <div class="products">
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 1</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 2</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 3</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 4</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 5</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 6</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 7</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img
                                    src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg"
                                    alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 8</a></h4>
                        <p><a href="">Giá sản phẩm</a></p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <style>
        #mainNav {
            background-color: #000;
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
@endsection
