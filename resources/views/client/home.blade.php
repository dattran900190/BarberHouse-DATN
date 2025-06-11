@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Trang chủ Baber House
@endsection

@section('slider')
     <section class="hero-slider">
      <div class="slide active">
        <img
          src="https://4rau.vn/upload/hinhanh/cover-fb-10th-collection-0744.png"
          alt="Slide 1"
        />
      </div>
      <div class="slide">
        <img
          src="https://4rau.vn/upload/hinhanh/z4459651440290_1e4a90c27fc15cc175132ecd94872e98-2870.jpg"
          alt="Slide 2"
        />
      </div>
      <div class="slide">
        <img
          src="https://4rau.vn/upload/hinhanh/z6220937549697_8ae15d51c35246081cf6bc8d60780126-1254.jpg"
          alt="Slide 3"
        />
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
                    <div class="post">
                        <div class="image-container">
                            <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 1</a></h4>
                        <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                    </div>
                    <div class="post">
                        <div class="image-container">
                            <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 2</a></h4>
                        <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                    </div>
                    <div class="post">
                        <div class="image-container">
                            <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 3</a></h4>
                        <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                    </div>
                    <div class="post">
                        <div class="image-container">
                            <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="img/Thành viên nhóm.png" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 4</a></h4>
                        <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                    </div>
                    <div class="post">
                        <div class="image-container">
                            <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="img/Thành viên nhóm.png" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 5</a></h4>
                        <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                    </div>
                </div>
                <button class="next-posts">›</button>
            </div>

            <div class="posts-nomal">
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 1</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 2</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 3</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 4</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 5</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 6</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 7</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
                <div class="post-nomal">
                    <div class="image-nomal">
                        <a href="{{ asset('chi-tiet-bai-viet') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                    </div>
                    <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề 8</a></h4>
                    <p><a href="{{ asset('chi-tiet-bai-viet') }}">Lorem ipsum dolor sit amet...</a></p>
                </div>
            </div>
            <button id="loadMore" class="btn-xem-them">Xem thêm</button>
        </section>

        <section id="product">
            <div class="product-wrapper">
                <h2>Sản phẩm Baber House</h2>
                <div class="products">
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 1</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 2</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 3</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 4</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 5</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 6</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 7</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                    <div class="product">
                        <div class="image-product">
                            <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
                        </div>
                        <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 8</a></h4>
                        <p><a href="{{ asset('chi-tiet-san-pham') }}">Giá sản phẩm</a></p>
                    </div>
                </div>
                <button id="loadMore" class="btn-xem-them">Xem thêm</button>
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

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection

