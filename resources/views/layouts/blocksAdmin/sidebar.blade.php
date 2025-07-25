<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header d-flex justify-content-between align-items-center" data-background-color="dark">
            <div class="nav-toggle d-flex">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>

            <a href="index.html" class="logo text-center mx-auto">
                <img src="{{ asset('storage/' . ($imageSettings['white_logo'] ?? 'default-images/white_logo.png')) }}" alt="navbar brand" class="navbar-brand"
                    height="70" />
            </a>

            <div class="topbar-toggler">
                <button class="btn btn-toggle more">
                    <i class="gg-more-vertical-alt"></i>
                </button>
            </div>
        </div>

        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item active">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Components</h4>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/appointments') }}">
                        <i class="fas fa-calendar-check"></i>
                        <p>Danh sách đặt lịch</p>
                        <span id="pending-appointment-count" class="badge badge-danger"
                            style="{{ $pendingCount > 0 ? '' : 'display: none;' }}">
                            {{ $pendingCount }}
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ asset('admin/orders') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Danh sách Đơn hàng</p>

                        <span id="pending-order-count" class="badge badge-danger"
                              style="{{ $pendingOrderCount > 0 ? '' : 'display: none;' }}">
                            {{ $pendingOrderCount }}
                        </span>

                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('refunds.index') }}">
                        <i class="icon-docs"></i> {{-- Biểu tượng phù hợp với hoàn tiền --}}
                        <p>Danh sách Hoàn tiền</p>
                        <span class="badge badge-count-refunds ms-2 bg-danger" id="sidebar-pending-refund-count"
                            style="{{ $pendingRefundCount > 0 ? '' : 'display: none;' }}">
                            {{ $pendingRefundCount }}
                        </span>
                    </a>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#base">
                        <i class="fas fa-cut"></i>
                        <p>Quản lý đặt lịch</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="base">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ asset('admin/services') }}">
                                    <span class="sub-item">Dịch vụ</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/checkins') }}">
                                    <span class="sub-item">Checkin</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/point_histories') }}">
                                    <span class="sub-item">Quản lý lịch sử điểm</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/user_redeemed_vouchers') }}">
                                    <span class="sub-item">Lịch sử đổi voucher</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/promotions') }}">
                                    <span class="sub-item">Mã giảm giá</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/reviews') }}">
                                    <span class="sub-item">Bình luận</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#sidebarLayouts">
                        <i class="fas fa-shopping-bag"></i>
                        <p>Quản lý đặt hàng</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="sidebarLayouts">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ asset('admin/products') }}">
                                    <span class="sub-item">Sản phẩm</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/volumes') }}">
                                    <span class="sub-item">Dung tích</span>
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ asset('admin/refunds') }}">
                                    <span class="sub-item">Hoàn tiền</span>
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ asset('admin/product_categories') }}">
                                    <span class="sub-item">Danh mục</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#forms">
                        <i class="fas fa-building"></i>
                        <p>Quản lý chi nhánh</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="forms">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ asset('admin/branches') }}">
                                    <span class="sub-item">Chi nhánh</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/barbers') }}">
                                    <span class="sub-item">Thợ cắt</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/barber_schedules') }}">
                                    <span class="sub-item">Lịch trình thợ</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#tables">
                        <i class="fas fa-cogs"></i>
                        <p>Quản lý chung</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="tables">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ asset('admin/posts') }}">
                                    <span class="sub-item">Bài viết</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/users') }}">
                                    <span class="sub-item">Người dùng</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/banners') }}">
                                    <span class="sub-item">Banner</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ asset('admin/customer-images') }}">
                                    <span class="sub-item">Ảnh khách hàng</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin/settings') }}">
                        <i class="fas fa-desktop"></i>
                        <p>Cài đặt</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
