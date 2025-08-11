<nav id="mainNav" class="navbar fixed-top">
    <div class="container-fluid">

        <div class="d-flex justify-content-center align-items-center w-100">

            {{-- LEFT --}}
            <ul class="navbar-nav d-flex flex-row flex-nowrap me-4 desktop-menu">
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">TRANG CHỦ</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('san-pham*') ? 'active' : '' }}" href="{{ url('san-pham') }}">SẢN PHẨM</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('bai-viet*') ? 'active' : '' }}" href="{{ url('bai-viet') }}">BÀI VIẾT</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('tho-cat*') ? 'active' : '' }}" href="{{ url('tho-cat') }}">THỢ CẮT</a>
                </li>
            </ul>

            {{-- LOGO Ở GIỮA --}}
            <a class="navbar-brand mx-4" href="{{ url('/') }}">
                <img src="{{ asset('storage/' . ($imageSettings['white_logo'] ?? 'default-images/white_logo.png')) }}"
                    height="60" alt="Logo">
            </a>

            {{-- RIGHT --}}
            <ul class="navbar-nav d-flex flex-row flex-nowrap ms-4 desktop-menu">
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('dat-lich*') ? 'active' : '' }}" href="{{ route('dat-lich') }}">ĐẶT LỊCH</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link {{ request()->is('chi-nhanh*') ? 'active' : '' }}" href="{{ url('chi-nhanh') }}">CHI NHÁNH</a>
                </li>
                <li class="nav-item mx-2 position-relative">
                    <a class="nav-link-right {{ request()->is('gio-hang*') ? 'active' : '' }}" href="{{ url('gio-hang') }}">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span id="cartCount"
                            class="position-absolute top-25 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.7rem;">
                            {{ session('cart_count', 0) }}
                        </span>
                    </a>
                </li>
                <li class="nav-item mx-2 position-relative">
                    <a class="nav-link-right" href="#" id="notification-bell" data-bs-toggle="dropdown"
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

                {{-- SOCIAL LINKS --}}
                @foreach ($social_links as $key => $url)
                    @if (str_contains($key, 'tiktok'))
                        @continue
                    @endif
                    <li class="nav-item mx-2">
                        <a class="nav-link-right" href="{{ $url }}" target="_blank">
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

                {{-- USER ACCOUNT DROPDOWN --}}
                <li class="nav-item dropdown mx-2">
                    <a class="nav-link-right" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa-solid fa-user"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end desktop-user-dropdown" aria-labelledby="accountDropdown">
                        @guest
                            <li><a class="dropdown-item" href="{{ route('login') }}">
                                <i class="fa-solid fa-sign-in-alt me-2"></i>Đăng nhập</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('register') }}">
                                <i class="fa-solid fa-user-plus me-2"></i>Đăng ký</a>
                            </li>
                        @else
                            <li>
                                <div class="dropdown-header user-info">
                                    <div class="user-avatar">
                                        <h6><i class="fa-solid fa-user"></i>  {{ Auth::user()->name }}</h6>
                                    </div>
                                    <div class="user-details">
                                       
                                        <small>{{ Auth::user()->email }}</small>
                                    </div>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="{{ url('/profile') }}">
                                <i class="fa-solid fa-user-cog me-2"></i>Quản lý tài khoản</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('client.detailWallet') }}">
                                <i class="fa-solid fa-wallet me-2"></i>Hoàn tiền</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('client.redeem') }}">
                                <i class="fa-solid fa-gift me-2"></i>Đổi mã giảm giá</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('client.orderHistory') }}">
                                <i class="fa-solid fa-shopping-bag me-2"></i>Lịch sử đặt hàng</a>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('client.appointmentHistory') }}">
                                <i class="fa-solid fa-calendar me-2"></i>Lịch sử đặt lịch</a>
                            </li>

                            {{-- ADMIN LINKS --}}
                            @if (Auth::user()->role === 'admin_branch')
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fa-solid fa-cogs me-2"></i>Quản lý chi nhánh</a>
                                </li>
                            @endif

                            @if (Auth::user()->role != 'user')
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fa-solid fa-user-shield me-2"></i>Trang quản trị</a>
                                </li>
                            @endif

                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item logout-btn" type="submit">
                                        <i class="fa-solid fa-sign-out-alt me-2"></i>Đăng xuất
                                    </button>
                                </form>
                            </li>
                        @endguest
                    </ul>
                </li>

                <li class="nav-item mx-2">
                    <a class="nav-link-right" id="search-icon" href="#">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                </li>
            </ul>

            {{-- MOBILE MENU BUTTONS --}}
            <div class="mobile-menu-buttons d-lg-none">
                <button class="mobile-nav-btn" id="mobileMenuBtn" title="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <button class="mobile-nav-btn" id="mobileUserToggle" title="Tài khoản">
                    <i class="fa-solid fa-user"></i>
                </button>
                
                {{-- MOBILE USER DROPDOWN --}}
                <div class="mobile-user-dropdown" id="mobileUserDropdown">
                    <div class="mobile-user-header">
                        <div class="mobile-user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="mobile-user-info">
                            <h6>{{ Auth::user()->name ?? ''}}</h6> <br>
                            <small>{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                    
                    <div class="mobile-user-menu">
                        @guest
                            <a href="{{ route('login') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-sign-in-alt"></i>
                                Đăng nhập
                            </a>
                            <a href="{{ route('register') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-user-plus"></i>
                                Đăng ký
                            </a>
                        @else
                            <a href="{{ url('/profile') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-user-cog"></i>
                                Quản lý tài khoản
                            </a>
                            <a href="{{ route('client.detailWallet') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-wallet"></i>
                                Hoàn tiền
                            </a>
                            <a href="{{ route('client.redeem') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-gift"></i>
                                Đổi mã giảm giá
                            </a>
                            <a href="{{ route('client.orderHistory') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-shopping-bag"></i>
                                Lịch sử đặt hàng
                            </a>
                            <a href="{{ route('client.appointmentHistory') }}" class="mobile-menu-item">
                                <i class="fa-solid fa-calendar"></i>
                                Lịch sử đặt lịch
                            </a>
                            
                            @if (Auth::user()->role === 'admin_branch')
                                <a href="{{ route('dashboard') }}" class="mobile-menu-item">
                                    <i class="fa-solid fa-cogs"></i>
                                    Quản lý chi nhánh
                                </a>
                            @endif
                            
                            @if (Auth::user()->role != 'user')
                                <a href="{{ route('dashboard') }}" class="mobile-menu-item">
                                    <i class="fa-solid fa-user-shield"></i>
                                    Trang quản trị
                                </a>
                            @endif
                            
                            <div class="mobile-menu-divider"></div>
                            
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="mobile-menu-item mobile-logout-btn">
                                    <i class="fa-solid fa-sign-out-alt"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        @endguest
                    </div>
                </div>
            </div>

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

{{-- MOBILE MENU OVERLAY --}}
<div class="mobile-menu-overlay" id="mobileMenuOverlay">
    <div class="mobile-menu-sidebar">
        <div class="mobile-menu-header">
            <button class="mobile-menu-close" id="mobileMenuClose">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        
        <div class="mobile-search-section">
            <div class="search-container">
                <input type="text" placeholder="Tìm kiếm sản phẩm" class="mobile-search-input">
                <button class="mobile-search-btn">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
        
        <div class="mobile-menu-body">
            <ul class="mobile-nav-list">
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                        TRANG CHỦ
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('san-pham*') ? 'active' : '' }}" href="{{ url('san-pham') }}">
                        SẢN PHẨM
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('bai-viet*') ? 'active' : '' }}" href="{{ url('bai-viet') }}">
                        BÀI VIẾT
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('tho-cat*') ? 'active' : '' }}" href="{{ url('tho-cat') }}">
                        THỢ CẮT
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('dat-lich*') ? 'active' : '' }}" href="{{ route('dat-lich') }}">
                        ĐẶT LỊCH
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('chi-nhanh*') ? 'active' : '' }}" href="{{ url('chi-nhanh') }}">
                        CHI NHÁNH
                    </a>
                </li>
                <li class="mobile-nav-item">
                    <a class="mobile-nav-link {{ request()->is('gio-hang*') ? 'active' : '' }}" href="{{ url('gio-hang') }}">
                        GIỎ HÀNG
                        <span class="mobile-cart-badge">{{ session('cart_count', 0) }}</span>
                    </a>
                </li>
                <li class="mobile-nav-item position-relative">
                    <a class="mobile-nav-link" href="#" id="mobileNotificationBell" data-bs-toggle="dropdown"
                        aria-expanded="false">
                       THÔNG BÁO
                        <span id="mobile-notification-count"
                            class="mobile-cart-badge"
                            style="font-size: 0.7rem;">
                            0
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end mobile-notification-dropdown"
                        aria-labelledby="mobileNotificationBell">
                        <li>
                            <h6 class="dropdown-header">Thông báo</h6>
                        </li>
                        <li id="mobile-notification-list" class="notification-list">
                            <p class="text-center text-muted">Chưa có thông báo</p>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item text-center" href="#" onclick="resetMobileNotificationCount()">Xóa
                                tất cả thông báo</a></li>
                    </ul>
                </li>
            </ul>

            {{-- USER ACCOUNT SECTION --}}
            <div class="mobile-user-section">
                @guest
                    <div class="mobile-auth-buttons">
                        <a href="{{ route('login') }}" class="btn btn-outline-dark btn-sm me-2">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn btn-dark btn-sm">Đăng ký</a>
                    </div>
                @else
                    <div class="mobile-user-info">
                        <div class="user-avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h6>{{ Auth::user()->name }}</h6> 
                            <small>{{ Auth::user()->email }}</small>
                        </div>
                    </div>
                    <ul class="mobile-user-menu">
                        <li><a href="{{ url('/profile') }}"><i class="fa-solid fa-user-cog"></i> Quản lý tài khoản</a></li>
                        <li><a href="{{ route('client.detailWallet') }}"><i class="fa-solid fa-wallet"></i> Hoàn tiền</a></li>
                        <li><a href="{{ route('client.redeem') }}"><i class="fa-solid fa-gift"></i> Đổi mã giảm giá</a></li>
                        <li><a href="{{ route('client.orderHistory') }}"><i class="fa-solid fa-shopping-bag"></i> Lịch sử đặt hàng</a></li>
                        <li><a href="{{ route('client.appointmentHistory') }}"><i class="fa-solid fa-calendar"></i> Lịch sử đặt lịch</a></li>
                        
                        @if (Auth::user()->role === 'admin_branch')
                            <li><a href="{{ route('dashboard') }}"><i class="fa-solid fa-cogs"></i> Quản lý chi nhánh</a></li>
                        @endif
                        
                        @if (Auth::user()->role != 'user')
                            <li><a href="{{ route('dashboard') }}"><i class="fa-solid fa-user-shield"></i> Trang quản trị</a></li>
                        @endif
                        
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="mobile-logout-btn">
                                    <i class="fa-solid fa-sign-out-alt"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                @endguest
            </div>

            {{-- SOCIAL MEDIA ICONS --}}
            <div class="mobile-social-section">
                <h6>Kết nối với chúng tôi</h6>
                <div class="social-icons">
                    @foreach ($social_links as $key => $url)
                        @if (str_contains($key, 'tiktok'))
                            @continue
                        @endif
                        <a href="{{ $url }}" target="_blank" class="social-icon">
                            @if (str_contains($key, 'facebook'))
                                <i class="fa-brands fa-facebook-f"></i>
                            @elseif (str_contains($key, 'instagram'))
                                <i class="fa-brands fa-instagram"></i>
                            @elseif (str_contains($key, 'youtube'))
                                <i class="fa-brands fa-youtube"></i>
                            @else
                                <i class="fa-solid fa-link"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
