<nav id="mainNav" class="navbar fixed-top">
    <div class="container-fluid">

        <div class="d-flex justify-content-center align-items-center w-100">

            {{-- LEFT --}}
            <ul class="navbar-nav d-flex flex-row flex-nowrap me-4">
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Trang chủ</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('san-pham*') ? 'active' : '' }}" href="{{ url('san-pham') }}">Sản phẩm</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('bai-viet*') ? 'active' : '' }}" href="{{ url('bai-viet') }}">Bài viết</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('tho-cat*') ? 'active' : '' }}" href="{{ url('tho-cat') }}">Thợ cắt</a>
                </li>
            </ul>

            {{-- LOGO Ở GIỮA --}}
            <a class="navbar-brand mx-4" href="{{ url('/') }}">
                <img src="{{ asset('storage/' . ($imageSettings['white_logo'] ?? 'default-images/white_logo.png')) }}"
                    height="60" alt="Logo">
            </a>

            {{-- RIGHT --}}
            <ul class="navbar-nav d-flex flex-row flex-nowrap ms-4">
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('dat-lich*') ? 'active' : '' }}" href="{{ route('dat-lich') }}">Đặt lịch</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('chi-nhanh*') ? 'active' : '' }}" href="{{ url('chi-nhanh') }}">Chi nhánh</a>
                </li>
                <li class="nav-item mx-2 position-relative">
                    <a class="nav-link {{ request()->is('gio-hang*') ? 'active' : '' }}" href="{{ url('gio-hang') }}">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cartCount"
                            class="position-absolute top-25 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.7rem;">
                            {{ session('cart_count', 0) }}
                        </span>
                    </a>
                </li>
                <li class="nav-item mx-2 position-relative">
                    <a class="nav-link" href="#" id="notification-bell" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-bell"></i>
                        <span id="notification-count"
                            class="position-absolute top-25 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.7rem;">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end notification-dropdown"
                        aria-labelledby="notification-bell">
                        <li>
                            <h6 class="dropdown-header">Thông báo</h6>
                        </li>
                        <li id="notification-list" class="notification-list">
                            <p class="text-center text-muted">Chưa có thông báo</p>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center" href="#" onclick="resetNotificationCount()">Xóa
                                tất cả thông báo</a></li>
                    </ul>
                </li>


                {{-- ICON ADMIN CHO BRANCH ADMIN --}}
                @foreach ($social_links as $key => $url)
                    @if (str_contains($key, 'tiktok'))
                        @continue
                    @endif
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="{{ $url }}" target="_blank">
                            @if (str_contains($key, 'facebook'))
                                <i class="fa-brands fa-facebook"></i>
                            @elseif (str_contains($key, 'instagram'))
                                <i class="fa-brands fa-instagram"></i>
                            @elseif (str_contains($key, 'youtube'))
                                <i class="fa-brands fa-youtube"></i>
                            @else
                                <i class="fa-solid fa-link"></i>
                            @endif
                        </a>
                    </li>
                @endforeach

                <li class="nav-item dropdown mx-2">
                    <a class="nav-link" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                        @guest
                            <li><a class="dropdown-item" href="{{ route('login') }}">Đăng nhập</a></li>
                            <li><a class="dropdown-item" href="{{ route('register') }}">Đăng ký</a></li>
                        @else
                            <li>
                                <h6 class="dropdown-header">{{ Auth::user()->name }}</h6>
                            </li>
                            <li><a class="dropdown-item" href="{{ url('/profile') }}">Quản lý tài khoản</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.detailWallet') }}">Hoàn tiền</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.redeem') }}">Đổi mã giảm giá</a></li>
                            <li><a class="dropdown-item" href="{{ route('client.orderHistory') }}">Lịch sử đặt hàng</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory') }}">Lịch sử đặt
                                    lịch</a></li>

                            {{-- THÊM LINK ADMIN VÀO DROPDOWN CHO BRANCH ADMIN --}}
                            @if (Auth::user()->role === 'admin_branch')
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="fa-solid fa-cogs me-2"></i>Quản lý chi nhánh
                                    </a></li>
                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Đăng xuất</button>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </li>
                <li class="nav-item mx-2"><a class="nav-link" id="search-icon" href="#"><i
                            class="fa-solid fa-magnifying-glass"></i></a>
                </li>
                @auth
                    @if (Auth::user()->role != 'user')
                        <li class="nav-item mx-2">
                            <a class="nav-link" href="{{ route('dashboard') }}" onclick="window.location.reload();"
                                title="Đến trang quản trị">
                                <i class="fa-solid fa-user-shield"></i>
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>


            <!-- Search Overlay -->
            <div id="search-overlay" style="display: none;">
                <div class="search-content">
                    <form method="GET" action="{{ route('client.product') }}" class="d-flex align-items-center">
                        <input type="text" name="search" id="search-input" placeholder="Tìm kiếm sản phẩm..."
                            class="form-control me-2" />
                        <button type="submit" class="btn-outline-buy">Tìm</button>
                    </form>
                    <button class="close-btn btn btn-sm btn-danger mt-2">❌</button>
                </div>
            </div>

        </div>

    </div>
</nav>
