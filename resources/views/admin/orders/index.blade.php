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
    @endphp
    @php
        $paymentMethodMap = [
            'cash' => 'Thanh toán khi nhận hàng',
            'vnpay' => 'Thanh toán qua VNPAY',
        ];

    @endphp
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Đơn hàng</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm theo mã đơn hoặc tên người nhận..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            {{-- Đơn hàng chưa xác nhận --}}
            <h4>Đơn hàng chưa xác nhận</h4>
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
                    @forelse ($pendingOrders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ Str::limit($order->address, 30) }}</td>
                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                            <td class="text-uppercase">
                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-warning">Chờ xác nhận</span></td>
                            @if ($currentRole == 'admin')
                                <td class="text-center">
                                    <form action="{{ route('admin.orders.confirm', $order->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
                                    </form>
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="btn btn-info btn-sm">Xem</a>
                                    <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Bạn có chắc muốn hủy đơn này không?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-sm">Hủy</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Không có đơn hàng chờ xác nhận.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Đơn hàng đã xử lý --}}
            <h4>Đơn hàng đã xử lý</h4>
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
                    @forelse ($confirmedOrders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ Str::limit($order->address, 30) }}</td>
                            <td>{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                            <td class="text-uppercase">
                                {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</td>
                            <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                @php
                                    $statusMap = [
                                        'processing' => 'Đang xử lý',
                                        'shipping' => 'Đang giao hàng',
                                        'completed' => 'Hoàn thành',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                    $badgeColor = [
                                        'processing' => 'primary',
                                        'shipping' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $badgeColor[$order->status] ?? 'secondary' }}">
                                    {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </td>
                               @if ($currentRole == 'admin')
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem</a>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted">Không có đơn hàng đã xử lý.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
