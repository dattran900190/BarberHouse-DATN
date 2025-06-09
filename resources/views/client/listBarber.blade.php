@extends('layouts.ClientLayout')

@section('title-page')
    Danh sách thợ Baber House
@endsection

@section('content')
    <main class="container">
        <div class="list-barber">
            <h2>Top thợ cắt của Barber House</h2>
            <div class="product-filters">
                <div class="filter-selects">
                    <div class="filter-group">
                        <label for="filter-category">Chi nhánh:</label>
                        <select id="filter-category">
                            <option value="">Tất cả</option>
                            <option value="cat-toc">Sáp vuốt tóc</option>
                            <option value="goi-dau">Dầu gội & dầu xả</option>
                            <option value="nhuom-toc">Tông Đơ cắt tóc</option>
                            <option value="cao-rau">Kéo cắt tóc</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filter-price">Đánh giá:</label>
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
            <div class="main-list-barber">
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>
                <div class="top-barber">
                    <div class="img-top-barber">
                        <a href="">
                            <img src="https://kenh14cdn.com/2020/6/30/img0096-1592366363868430058761-1593507888983990295582.jpeg"
                                alt="" />
                        </a>
                        <button href="" class="btn">Đặt lịch ngay</button>
                    </div>
                    <h5><a href="">Tên ...........</a></h5>
                    <p><a href="">Địa chỉ .............</a></p>
                </div>

            </div>
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
