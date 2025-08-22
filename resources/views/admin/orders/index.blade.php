@extends('layouts.AdminLayout')

@section('title', 'Quản lý Đơn hàng')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>×</span></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>×</span></button>
        </div>
    @endif

    @php
        $currentRole = Auth::user()->role;
        $paymentMethodMap = [
            'cash' => 'Thanh toán khi nhận hàng',
            'vnpay' => 'Thanh toán qua VNPAY',
        ];
        $statusMap = [
            'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
            'processing' => ['class' => 'primary', 'text' => 'Đang xử lý'],
            'shipping' => ['class' => 'info', 'text' => 'Đang giao hàng'],
            'completed' => ['class' => 'success', 'text' => 'Hoàn thành'],
            'cancelled' => ['class' => 'danger', 'text' => 'Đã hủy'],
        ];
        $paymentMap = [
            'unpaid' => ['class' => 'bg-warning', 'text' => 'Chưa thanh toán'],
            'paid' => ['class' => 'bg-success', 'text' => 'Đã thanh toán'],
            'failed' => ['class' => 'bg-danger', 'text' => 'Thanh toán thất bại'],
            'refunded' => ['class' => 'bg-secondary', 'text' => 'Đã hoàn tiền'],
        ];
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Quản lý đặt hàng</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/orders') }}">Danh sách đơn hàng</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách đơn hàng</div>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="mb-3" style="position: relative;">
                <input type="text" name="search" class="form-control pe-5"
                    placeholder="Tìm theo mã đơn hoặc tên người nhận..." value="{{ request('search') }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <button type="submit" class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent"
                    style="padding-right: 15px;">
                    <i class="fa fa-search"></i>
                </button>
            </form>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'pending' ? 'active' : '' }}" id="pending-tab" data-toggle="tab"
                        href="#pending" role="tab">Chờ xác nhận
                        @if (!empty($pendingOrderCount) && $pendingOrderCount > 0)
                            <span class="position-relative">
                                <span
                                    class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                    <span class="visually-hidden">New alerts</span>
                                </span>
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'processing' ? 'active' : '' }}" id="processing-tab"
                        data-toggle="tab" href="#processing" role="tab">Đang xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'shipping' ? 'active' : '' }}" id="shipping-tab" data-toggle="tab"
                        href="#shipping" role="tab">Đang giao hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'completed' ? 'active' : '' }}" id="completed-tab"
                        data-toggle="tab" href="#completed" role="tab">Hoàn thành</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'cancelled' ? 'active' : '' }}" id="cancelled-tab"
                        data-toggle="tab" href="#cancelled" role="tab">Đã hủy</a>
                </li>
            </ul>

            <div class="tab-content" id="orderTabsContent">
                <!-- Tab Chờ xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'pending' ? 'show active' : '' }}" id="pending"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Người nhận</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Trạng thái</th>
                                    <th>Trạng thái thanh toán</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($pendingOrders->count())
                                    @foreach ($pendingOrders as $index => $order)
                                        <tr>
                                            <td>{{ $pendingOrders->firstItem() + $index }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ Str::limit($order->address, 30) }}</td>
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-warning">Chờ xác nhận</span></td>
                                            <td class="text-center">
                                                @if (isset($paymentMap[$order->payment_status]))
                                                    <span class="badge {{ $paymentMap[$order->payment_status]['class'] }}">
                                                        {{ $paymentMap[$order->payment_status]['text'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Không rõ</span>
                                                @endif
                                            </td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenuOrder{{ $order->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenuOrder{{ $order->id }}">
                                                            <li>
                                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fas fa-eye me-2"></i> Xem
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                    class="dropdown-item text-success confirm-btn"
                                                                    data-id="{{ $order->id }}">
                                                                    <i class="fas fa-check-circle me-2"></i> Xác nhận
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                    class="dropdown-item text-danger destroy-btn"
                                                                    data-id="{{ $order->id }}">
                                                                    <i class="fas fa-times-circle me-2"></i> Hủy
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có đơn hàng chờ xác nhận.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pendingOrders->appends(['search' => request('search'), 'tab' => 'pending'])->links() }}
                    </div>
                </div>

                <!-- Tab Đang xử lý -->
                <div class="tab-pane fade {{ $activeTab == 'processing' ? 'show active' : '' }}" id="processing"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Người nhận</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                 
                                    <th>Ngày đặt hàng</th>
                                       <th>Trạng thái thanh toán</th>
                                    <th>Trạng thái</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($processingOrders->count())
                                    @foreach ($processingOrders as $index => $order)
                                        <tr>
                                            <td>{{ $processingOrders->firstItem() + $index }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ Str::limit($order->address, 30) }}</td>
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                         
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if (isset($paymentMap[$order->payment_status]))
                                                    <span class="badge {{ $paymentMap[$order->payment_status]['class'] }}">
                                                        {{ $paymentMap[$order->payment_status]['text'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Không rõ</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-primary">Đang xử lý</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenuOrder{{ $order->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenuOrder{{ $order->id }}">
                                                            <li>
                                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fas fa-eye me-2"></i> Xem
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                    class="dropdown-item text-success ship-btn"
                                                                    data-id="{{ $order->id }}">
                                                                    <i class="fas fa-truck me-2"></i> Chuyển sang giao hàng
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có đơn hàng đang xử lý.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $processingOrders->appends(['search' => request('search'), 'tab' => 'processing'])->links() }}
                    </div>
                </div>

                <!-- Tab Đang giao hàng -->
                <div class="tab-pane fade {{ $activeTab == 'shipping' ? 'show active' : '' }}" id="shipping"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Người nhận</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th>Trạng thái</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($shippingOrders->count())
                                    @foreach ($shippingOrders as $index => $order)
                                        <tr>
                                            <td>{{ $shippingOrders->firstItem() + $index }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ Str::limit($order->address, 30) }}</td>
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if (isset($paymentMap[$order->payment_status]))
                                                    <span class="badge {{ $paymentMap[$order->payment_status]['class'] }}">
                                                        {{ $paymentMap[$order->payment_status]['text'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Không rõ</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-info">Đang giao hàng</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenuOrder{{ $order->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenuOrder{{ $order->id }}">
                                                            <li>
                                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fas fa-eye me-2"></i> Xem
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                    class="dropdown-item text-success complete-btn"
                                                                    data-id="{{ $order->id }}">
                                                                    <i class="fas fa-check-circle me-2"></i> Hoàn thành
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có đơn hàng đang giao.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $shippingOrders->appends(['search' => request('search'), 'tab' => 'shipping'])->links() }}
                    </div>
                </div>

                <!-- Tab Hoàn thành -->
                <div class="tab-pane fade {{ $activeTab == 'completed' ? 'show active' : '' }}" id="completed"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Người nhận</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th>Trạng thái</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($completedOrders->count())
                                    @foreach ($completedOrders as $index => $order)
                                        <tr>
                                            <td>{{ $completedOrders->firstItem() + $index }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ Str::limit($order->address, 30) }}</td>
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if (isset($paymentMap[$order->payment_status]))
                                                    <span class="badge {{ $paymentMap[$order->payment_status]['class'] }}">
                                                        {{ $paymentMap[$order->payment_status]['text'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Không rõ</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-success">Hoàn thành</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenuOrder{{ $order->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenuOrder{{ $order->id }}">
                                                            <li>
                                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fas fa-eye me-2"></i> Xem
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có đơn hàng hoàn thành.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $completedOrders->appends(['search' => request('search'), 'tab' => 'completed'])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hủy -->
                <div class="tab-pane fade {{ $activeTab == 'cancelled' ? 'show active' : '' }}" id="cancelled"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Người nhận</th>
                                    <th>SĐT</th>
                                    <th>Địa chỉ</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Ngày đặt hàng</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th>Trạng thái</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($cancelledOrders->count())
                                    @foreach ($cancelledOrders as $index => $order)
                                        <tr>
                                            <td>{{ $cancelledOrders->firstItem() + $index }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ Str::limit($order->address, 30) }}</td>
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</td>
                                            <td>
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}
                                            </td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if (isset($paymentMap[$order->payment_status]))
                                                    <span class="badge {{ $paymentMap[$order->payment_status]['class'] }}">
                                                        {{ $paymentMap[$order->payment_status]['text'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Không rõ</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-danger">Đã hủy</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenuOrder{{ $order->id }}"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenuOrder{{ $order->id }}">
                                                            <li>
                                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                                    class="dropdown-item">
                                                                    <i class="fas fa-eye me-2"></i> Xem
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không có đơn hàng đã hủy.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $cancelledOrders->appends(['search' => request('search'), 'tab' => 'cancelled'])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Xử lý chuyển tab và giữ lại tham số tìm kiếm
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[data-toggle="tab"]');
            const searchInput = document.querySelector('input[name="search"]');
            const tabInput = document.querySelector('input[name="tab"]');

            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetTab = this.getAttribute('href').substring(1);
                    tabInput.value = targetTab;

                    // Cập nhật URL với tham số tab
                    const url = new URL(window.location);
                    url.searchParams.set('tab', targetTab);
                    if (searchInput.value) {
                        url.searchParams.set('search', searchInput.value);
                    }
                    window.history.pushState({}, '', url);

                    // Hiển thị tab được chọn
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                    });

                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('show', 'active');
                });
            });
        });
    </script>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script>
        window.currentRole = "{{ Auth::user()->role }}";
    </script>
    <script>
        const paymentMap = {
            paid: { class: 'bg-success', text: 'Đã thanh toán' },
            unpaid: { class: 'bg-warning', text: 'Chưa thanh toán' },
            failed: { class: 'bg-danger', text: 'Thanh toán thất bại' },
            refunded: { class: 'bg-secondary', text: 'Đã hoàn tiền' }
        };

        const paymentMethodMap = {
            cash: 'Thanh toán khi nhận hàng',
            vnpay: 'Thanh toán qua VNPAY'
        };

        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true
        });
        const orderChannel = pusher.subscribe('orders');
        orderChannel.bind('App\\Events\\NewOrderCreated', function(data) {
            const tableBody = document.querySelector('#pending tbody');
            if (tableBody) {
                const paymentInfo = paymentMap[data.payment_status] || {
                    class: 'bg-secondary',
                    text: data.payment_status
                };
                const paymentMethodText = paymentMethodMap[data.payment_method] || data.payment_method || '';
                let actionButtons = `
                    <li>
                        <a href="/admin/orders/${data.order_id || ''}" class="dropdown-item">
                            <i class="fas fa-eye me-2"></i> Xem
                        </a>
                    </li>
                `;
                if (window.currentRole === 'admin') {
                    actionButtons += `
                        <li>
                            <button type="button" class="dropdown-item text-success confirm-btn" data-id="${data.order_id || ''}">
                                <i class="fas fa-check-circle me-2"></i> Xác nhận
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-danger destroy-btn" data-id="${data.order_id || ''}">
                                <i class="fas fa-times-circle me-2"></i> Hủy
                            </button>
                        </li>
                    `;
                }
                const row = `
                    <tr>
                        <td>Mới</td>
                        <td>${data.order_code || ''}</td>
                        <td>${data.name || ''}</td>
                        <td>${data.phone || ''}</td>
                        <td>${data.address ? data.address.substring(0, 30) : ''}</td>
                        <td>${data.total_money ? Number(data.total_money).toLocaleString('vi-VN') + ' đ' : ''}</td>
                        <td>${paymentMethodText}</td>
                        <td>${data.created_at || ''}</td>
                          <td class="text-center"><span class="badge bg-warning">Chờ xác nhận</span></td>
                        <td class="text-center"><span class="badge ${paymentInfo.class}">${paymentInfo.text}</span></td>
                      
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button"
                                    id="actionMenuOrder${data.order_id || ''}"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenuOrder${data.order_id || ''}">
                                    ${actionButtons}
                                </ul>
                            </div>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('afterbegin', row);
            } else {
                console.warn('Không tìm thấy bảng #pending tbody!');
            }
        });

        orderChannel.bind('App\\Events\\OrderPaymentStatusUpdated', function(data) {
            const rows = document.querySelectorAll('#pending tbody tr');
            rows.forEach(row => {
                const codeCell = row.children[1];
                if (codeCell && codeCell.textContent.trim() === data.order_code) {
                    const paymentCell = row.children[8];
                    if (paymentCell) {
                        let badgeClass = 'bg-secondary',
                            badgeText = data.payment_status;
                        if (data.payment_status === 'paid') {
                            badgeClass = 'bg-success';
                            badgeText = 'Đã thanh toán';
                        } else if (data.payment_status === 'unpaid') {
                            badgeClass = 'bg-warning';
                            badgeText = 'Chưa thanh toán';
                        } else if (data.payment_status === 'failed') {
                            badgeClass = 'bg-danger';
                            badgeText = 'Thanh toán thất bại';
                        } else if (data.payment_status === 'refunded') {
                            badgeClass = 'bg-secondary';
                            badgeText = 'Đã hoàn tiền';
                        }
                        paymentCell.innerHTML = `<span class="badge ${badgeClass}">${badgeText}</span>`;
                    }
                }
            });
        });
    </script>
    <script>
        // Sử dụng event delegation để xử lý các button được thêm động
        document.addEventListener('click', function(event) {
            // Xử lý confirm button
            if (event.target.closest('.confirm-btn')) {
                event.preventDefault();
                const button = event.target.closest('.confirm-btn');
                const orderId = button.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Xác nhận đơn hàng',
                    text: 'Bạn có chắc chắn muốn xác nhận đơn hàng này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => Swal.showLoading()
                        });

                        fetch('{{ route('admin.orders.confirm', ':id') }}'.replace(':id', orderId), {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            Swal.fire({
                                title: data.success ? 'Thành công!' : 'Lỗi!',
                                text: data.message,
                                icon: data.success ? 'success' : 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            }).then(() => {
                                if (data.success) {
                                    // Nếu có activeTab trong response, chuyển đến tab đó
                                    if (data.activeTab) {
                                        const url = new URL(window.location);
                                        url.searchParams.set('tab', data.activeTab);
                                        window.location.href = url.toString();
                                    } else {
                                        location.reload();
                                    }
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.close();
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Đã có lỗi xảy ra: ' + error.message,
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        });
                    }
                });
            }

            // Xử lý destroy button
            if (event.target.closest('.destroy-btn')) {
                event.preventDefault();
                const button = event.target.closest('.destroy-btn');
                const orderId = button.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Hủy đơn hàng',
                    text: 'Bạn có chắc chắn muốn hủy đơn hàng này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => Swal.showLoading()
                        });

                        fetch('{{ route('admin.orders.destroy', ':id') }}'.replace(':id', orderId), {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            Swal.fire({
                                title: data.success ? 'Thành công!' : 'Lỗi!',
                                text: data.message,
                                icon: data.success ? 'success' : 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            }).then(() => {
                                if (data.success) {
                                    // Nếu có activeTab trong response, chuyển đến tab đó
                                    if (data.activeTab) {
                                        const url = new URL(window.location);
                                        url.searchParams.set('tab', data.activeTab);
                                        window.location.href = url.toString();
                                    } else {
                                        location.reload();
                                    }
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.close();
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Đã có lỗi xảy ra: ' + error.message,
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        });
                    }
                });
            }
        });

        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false,
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const orderId = this.getAttribute('data-id');

                    const swalOptions = {
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    };

                    if (withInput) {
                        swalOptions.input = 'textarea';
                        swalOptions.inputPlaceholder = inputPlaceholder;
                        if (inputValidator) {
                            swalOptions.inputValidator = inputValidator;
                        }
                    }

                    Swal.fire(swalOptions).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                            const body = withInput ? JSON.stringify({
                                reason: result.value || 'Không có lý do'
                            }) : undefined;

                            fetch(route.replace(':id', orderId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) {
                                            // Nếu có activeTab trong response, chuyển đến tab đó
                                            if (data.activeTab) {
                                                const url = new URL(window.location);
                                                url.searchParams.set('tab', data.activeTab);
                                                window.location.href = url.toString();
                                            } else {
                                                onSuccess();
                                            }
                                        }
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }

        // Cấu hình các hành động
        handleSwalAction({
            selector: '.confirm-btn',
            title: 'Xác nhận đơn hàng',
            text: 'Bạn có chắc chắn muốn xác nhận đơn hàng này?',
            route: '{{ route('admin.orders.confirm', ':id') }}'
        });

        handleSwalAction({
            selector: '.ship-btn',
            title: 'Chuyển sang giao hàng',
            text: 'Bạn có chắc chắn muốn chuyển đơn hàng này sang trạng thái Đang giao hàng?',
            route: '{{ route('admin.orders.ship', ':id') }}',
            method: 'PUT'
        });

        handleSwalAction({
            selector: '.complete-btn',
            title: 'Hoàn thành đơn hàng',
            text: 'Bạn có chắc chắn muốn chuyển đơn hàng này sang trạng thái Hoàn thành?',
            route: '{{ route('admin.orders.complete', ':id') }}',
            method: 'PUT'
        });

        handleSwalAction({
            selector: '.destroy-btn',
            title: 'Hủy đơn hàng',
            text: 'Bạn có chắc chắn muốn hủy đơn hàng này? Số lượng tồn kho sẽ được hoàn lại.',
            route: '{{ route('admin.orders.destroy', ':id') }}',
            method: 'DELETE'
        });
    </script>
@endsection