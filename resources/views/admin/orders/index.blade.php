@extends('layouts.AdminLayout')

@section('title', 'Quản lý Đơn hàng')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span>&times;</span></button>
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
        <h3 class="fw-bold mb-3">Quản lý đặt hàng</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý đơn hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/orders') }}">Đơn hàng</a>
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
                       placeholder="Tìm theo mã đơn hoặc tên người nhận..."
                       value="{{ request('search') }}">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <button type="submit"
                        class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent"
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
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                    <span class="visually-hidden">New alerts</span>
                                </span>
                            </span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'processing' ? 'active' : '' }}" id="processing-tab" data-toggle="tab"
                        href="#processing" role="tab">Đang xử lý</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'shipping' ? 'active' : '' }}" id="shipping-tab" data-toggle="tab"
                        href="#shipping" role="tab">Đang giao hàng</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'completed' ? 'active' : '' }}" id="completed-tab" data-toggle="tab"
                        href="#completed" role="tab">Hoàn thành</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'cancelled' ? 'active' : '' }}" id="cancelled-tab" data-toggle="tab"
                        href="#cancelled" role="tab">Đã hủy</a>
                </li>
            </ul>

            <div class="tab-content" id="orderTabsContent">
                <!-- Tab Chờ xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'pending' ? 'show active' : '' }}" id="pending" role="tabpanel">
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
                                @if ($pendingOrders->count())
                                    @foreach ($pendingOrders as $index => $order)
                                        <tr>
                                            <td>{{ $pendingOrders->firstItem() + $index }}</td>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $order->name }}</td>
                                            <td>{{ $order->phone }}</td>
                                            <td>{{ Str::limit($order->address, 30) }}</td>
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                                            <td >
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @if(isset($paymentMap[$order->payment_status]))
                                                    <span class="badge {{ $paymentMap[$order->payment_status]['class'] }}">
                                                        {{ $paymentMap[$order->payment_status]['text'] }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Không rõ</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-warning">Chờ xác nhận</span></td>
                                            
                                            @if ($currentRole == 'admin')
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenuOrder{{ $order->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                            
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenuOrder{{ $order->id }}">
                                            
                                                        {{-- Xem --}}
                                                        <li>
                                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="dropdown-item">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a>
                                                        </li>
                                            
                                                        {{-- Xác nhận --}}
                                                        <li>
                                                            <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST"
                                                                onsubmit="return confirm('Bạn có chắc muốn xác nhận đơn này không?');">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item text-success">
                                                                    <i class="fas fa-check-circle me-2"></i> Xác nhận
                                                                </button>
                                                            </form>
                                                        </li>
                                            
                                                        <li><hr class="dropdown-divider"></li>
                                            
                                                        {{-- Hủy --}}
                                                        <li>
                                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                                                                onsubmit="return confirm('Bạn có chắc muốn hủy đơn này không?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-times-circle me-2"></i> Hủy
                                                                </button>
                                                            </form>
                                                        </li>
                                            
                                                    </ul>
                                                </div>
                                            </td>
                                            
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có đơn hàng chờ xác nhận.</td>
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
                <div class="tab-pane fade {{ $activeTab == 'processing' ? 'show active' : '' }}" id="processing" role="tabpanel">
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
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                                            <td >
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-primary">Đang xử lý</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có đơn hàng đang xử lý.</td>
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
                <div class="tab-pane fade {{ $activeTab == 'shipping' ? 'show active' : '' }}" id="shipping" role="tabpanel">
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
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                                            <td >
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-info">Đang giao hàng</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có đơn hàng đang giao.</td>
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
                <div class="tab-pane fade {{ $activeTab == 'completed' ? 'show active' : '' }}" id="completed" role="tabpanel">
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
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                                            <td >
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-success">Hoàn thành</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có đơn hàng hoàn thành.</td>
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
                <div class="tab-pane fade {{ $activeTab == 'cancelled' ? 'show active' : '' }}" id="cancelled" role="tabpanel">
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
                                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                                            <td >
                                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                            <td><span class="badge bg-danger">Đã hủy</span></td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có đơn hàng đã hủy.</td>
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

        // Thêm ánh xạ phương thức thanh toán sang tiếng Việt
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
                const paymentInfo = paymentMap[data.payment_status] || { class: 'bg-secondary', text: data.payment_status };
                // Sử dụng tiếng Việt cho phương thức thanh toán
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
                            <form action="/admin/orders/${data.order_id || ''}/confirm" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn xác nhận đơn này không?');">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                <button type="submit" class="dropdown-item text-success">
                                    <i class="fas fa-check-circle me-2"></i> Xác nhận
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="/admin/orders/${data.order_id || ''}" method="POST"
                                onsubmit="return confirm('Bạn có chắc muốn hủy đơn này không?');">
                                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-times-circle me-2"></i> Hủy
                                </button>
                            </form>
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
                        <td >${paymentMethodText}</td>
                        <td>${data.created_at || ''}</td>
                        <td><span class="badge ${paymentInfo.class}">${paymentInfo.text}</span></td>
                        <td><span class="badge bg-warning">Chờ xác nhận</span></td>
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
            // Tìm dòng có mã đơn hàng tương ứng
            const rows = document.querySelectorAll('#pending tbody tr');
            rows.forEach(row => {
                const codeCell = row.children[1]; // cột mã đơn
                if (codeCell && codeCell.textContent.trim() === data.order_code) {
                    // Cập nhật badge trạng thái thanh toán
                    const paymentCell = row.children[8]; // cột trạng thái thanh toán
                    if (paymentCell) {
                        let badgeClass = 'bg-secondary', badgeText = data.payment_status;
                        if (data.payment_status === 'paid') { badgeClass = 'bg-success'; badgeText = 'Đã thanh toán'; }
                        else if (data.payment_status === 'unpaid') { badgeClass = 'bg-warning'; badgeText = 'Chưa thanh toán'; }
                        else if (data.payment_status === 'failed') { badgeClass = 'bg-danger'; badgeText = 'Thanh toán thất bại'; }
                        else if (data.payment_status === 'refunded') { badgeClass = 'bg-secondary'; badgeText = 'Đã hoàn tiền'; }
                        paymentCell.innerHTML = `<span class="badge ${badgeClass}">${badgeText}</span>`;
                    }
                }
            });
        });
    </script>
@endsection
