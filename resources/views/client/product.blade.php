@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Sản phẩm Baber House
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
      <section id="product">
        <div class="product-wrapper">
          <h2>Sản phẩm </h2>

          <div class="product-filters">
            <h3>Lọc sản phẩm:</h3>
            <div class="filter-selects">
              <div class="filter-group">
                <label for="filter-category">Loại sản phẩm:</label>
                <select id="filter-category">
                  <option value="">Tất cả</option>
                  <option value="cat-toc">Sáp vuốt tóc</option>
                  <option value="goi-dau">Dầu gội & dầu xả</option>
                  <option value="nhuom-toc">Tông Đơ cắt tóc</option>
                  <option value="cao-rau">Kéo cắt tóc</option>
                </select>
              </div>
              <div class="filter-group">
                <label for="filter-price">Khoảng giá:</label>
                <select id="filter-price">
                  <option value="">Tất cả</option>
                  <option value="0-100">Dưới 100k</option>
                  <option value="100-200">100k–200k</option>
                  <option value="200-500">200k–500k</option>
                  <option value="500-9999">Trên 500k</option>
                </select>
              </div>
            </div>
          </div>
          
          <!-- Ví dụ sản phẩm có data-attributes để lọc -->
          <div class="products-list">
            <div class="product" data-category="cat-toc" data-price="150">
              <!-- ... -->
            </div>
            <div class="product" data-category="goi-dau" data-price="80">
              <!-- ... -->
            </div>
            <!-- thêm .product khác ... -->
          </div>

          <div class="products">
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 1</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 2</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 3</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 4</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 5</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 6</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 7</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 8</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
          </div>
          <div class="products">
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" /></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 1</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 2</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 3</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 4</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 5</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 6</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 7</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
            <div class="product">
              <div class="image-product">
                <a href="{{ asset('chi-tiet-san-pham') }}"><img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt=""/></a>
              </div>
              <h4><a href="{{ asset('chi-tiet-san-pham') }}">Sản phẩm 8</a></h4>
              <p><a href="">Giá sản phẩm</a></p>
            </div>
          </div>

          <!-- HTML: Pagination -->
<nav class="pagination" aria-label="Page navigation">
  <button class="page-btn prev" disabled>‹ Prev</button>
  <ul class="page-list">
    <li><button class="page-number active">1</button></li>
    <li><button class="page-number">2</button></li>
    <li><button class="page-number">3</button></li>
    <li><button class="page-number">4</button></li>
    <li><span class="ellipsis">…</span></li>
    <li><button class="page-number">10</button></li>
  </ul>
  <button class="page-btn next">Next ›</button>
</nav>

        </div>
      </section>
    </main>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection
