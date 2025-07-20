@extends('layouts.AdminLayout')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Đơn hàng</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="orders">Danh sách đơn hàng</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết đơn hàng</a></li>
        </ul>
    </div>

    @php
        $paymentMethodMap = [
            'cash' => 'Thanh toán khi nhận hàng',
            'vnpay' => 'Thanh toán qua VNPAY',
            'momo' => 'Thanh toán qua Momo',
            'card' => 'Thanh toán qua thẻ tín dụng',
        ];

        $statusMap = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        $paymentMap = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'refunded' => 'Đã hoàn tiền',
        ];
        $shippingMap = [
            'standard' => 'Giao hàng tiêu chuẩn',
            'express' => 'Giao hàng nhanh',
        ];
        $statusOrder = ['pending', 'processing', 'shipping', 'completed'];
        $currentIndex = array_search($order->status, $statusOrder);
    @endphp

    <!-- Card 1: Thông tin đơn hàng -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết đơn hàng: {{ $order->order_code }}</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'Khách vãng lai' }}</p>
                    <p><strong>Tên người nhận:</strong> {{ $order->name }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                    <p><strong>Phương thức thanh toán:</strong>
                        {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</p>
                    <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Phương thức giao hàng:</strong>
                        {{ $shippingMap[$order->shipping_method] ?? ucfirst($order->shipping_method) }}</p>
                    <p><strong>Trạng thái thanh toán:</strong>
                        {{ $paymentMap[$order->payment_status] ?? ucfirst($order->payment_status) }}</p>
                    <p><strong>Ghi chú:</strong> {{ $order->note ?: '-' }}</p>
                    <p><strong>Tổng tiền:</strong> {{ number_format($order->total_money, 0, ',', '.') }} đ</p>
                    <p><strong>Trạng thái:</strong>
                        <span
                            class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'processing' ? 'primary' : ($order->status == 'shipping' ? 'info' : ($order->status == 'completed' ? 'success' : 'danger'))) }}">
                            {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Sản phẩm -->
   <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Sản phẩm trong đơn hàng</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Dung tích</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        @php
                            $product = $item->productVariant->product ?? null;
                            $variant = $item->productVariant ?? null;
                        @endphp
                        <tr>
                            <td>
                                {{ $product->name ?? '-' }}
                                @if ($product && $product->deleted_at)
                                    <span class="badge bg-danger ms-1">Đã xóa mềm</span>
                                @endif
                            </td>
                            <td>
                                {{ $item->volume_name ?? '-' }}
                                @if ($variant && $variant->deleted_at)
                                    <span class="badge bg-warning text-dark ms-1">Biến thể đã xóa mềm</span>
                                @endif
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price_at_time, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <!-- Card 3: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Cập nhật trạng thái</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="w-100">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="form-group">
                    <label for="status">Trạng thái đơn hàng:</label>
                    <select name="status" id="status" class="form-control"
                        {{ in_array($order->status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                        @foreach ($statusMap as $key => $label)
                            @php
                                $statusIndex = array_search($key, $statusOrder);
                                $disabled =
                                    $statusIndex !== false && $currentIndex !== false && $statusIndex < $currentIndex;
                                if ($key === 'cancelled' && $order->status !== 'pending') {
                                    $disabled = true;
                                }
                            @endphp
                            <option value="{{ $key }}" {{ $order->status == $key ? 'selected' : '' }}
                                {{ $disabled ? 'disabled' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if (!in_array($order->status, ['completed', 'cancelled']))
                    <div class="mt-3 d-flex align-items-center">
                        <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                            <i class="fas fa-save me-1"></i> Cập nhật
                        </button>
                        <a href="{{ route('admin.orders.index', ['page' => request('page', 1)]) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                @else
                    <div class="mt-3">
                        <a href="{{ route('admin.orders.index', ['page' => request('page', 1)]) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                @endif

            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>
@endsection
