@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Tin tức Baber House
@endsection

@section('content')
 <main class="container">
      <div class="main-posts">
        <h2>Tin tức</h2>
        <div class="posts-content">
          <div class="post-left">
            <div class="post-top">
              <div class="image-top">
                <a href="{{ asset('chi-tiet-bai-viet') }}">
                  <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                </a>
              </div>
              <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
            </div>

            <div class="post-mid">
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
              <div class="post">
                <div class="image-mid">
                  <a href="{{ asset('chi-tiet-bai-viet') }}">
                    <img src="https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                  </a>
                </div>
                <h4><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề post</a></h4>
              </div>
            </div>
          </div>

          <div class="post-right">
            <div class="post">
              <div class="image-right">
                <a href="{{ asset('chi-tiet-bai-viet') }}">
                  <img src="/https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                </a>
              </div>
              <h5><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề</a></h5>
            </div>
            <div class="post">
              <div class="image-right">
                <a href="{{ asset('chi-tiet-bai-viet') }}">
                  <img src="/https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                </a>
              </div>
              <h5><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề</a></h5>
            </div>
            <div class="post">
              <div class="image-right">
                <a href="{{ asset('chi-tiet-bai-viet') }}">
                  <img src="/https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                </a>
              </div>
              <h5><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề</a></h5>
            </div>
            <div class="post">
              <div class="image-right">
                <a href="{{ asset('chi-tiet-bai-viet') }}">
                  <img src="/https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                </a>
              </div>
              <h5><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề</a></h5>
            </div>
            <div class="post">
              <div class="image-right">
                <a href="{{ asset('chi-tiet-bai-viet') }}">
                  <img src="/https://kenh14cdn.com/2020/6/5/photo-1-15913191386161819866915.jpg" alt="" />
                </a>
              </div>
              <h5><a href="{{ asset('chi-tiet-bai-viet') }}">Tiêu đề</a></h5>
            </div>
          </div>
        </div>
      </div>
    </main>
    <style>
      #mainNav {
        background-color: #000;
      }
    </style>
    <script>
      const icon = document.getElementById("search-icon");
        const overlay = document.getElementById("search-overlay");
        const closeBtn = document.querySelector(".close-btn");
        if (icon && overlay) {
            icon.addEventListener("click", e => {
                e.preventDefault();
                overlay.style.display = "flex";
            });
            // đóng
            closeBtn?.addEventListener("click", () => overlay.style.display = "none");
            overlay.addEventListener("click", e => {
                if (!e.target.closest(".search-content")) overlay.style.display = "none";
            });
            document.addEventListener("keydown", e => {
                if (e.key === "Escape") overlay.style.display = "none";
            });
        }
    </script>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection

<script>
  const nav = document.getElementById("mainNav");

    window.addEventListener("scroll", () => {
      if (window.scrollY = 100) {
        nav.classList.add("scrolled");
      } else {
        nav.classList.remove("scrolled");
      }
    });
</script>
