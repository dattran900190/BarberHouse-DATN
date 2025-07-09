@extends('layouts.AdminLayout')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
        // Thứ tự trạng thái (cancelled không tính vào thứ tự vì nó có thể chọn bất cứ lúc nào)
        $statusOrder = ['pending', 'processing', 'shipping', 'completed'];

        // Vị trí trạng thái hiện tại trong mảng $statusOrder
        $currentIndex = array_search($order->status, $statusOrder);
    @endphp

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 flex-grow-1 text-center">Chi tiết đơn hàng: {{ $order->order_code }}</h3>
            <a href="{{ route('admin.orders.index', ['page' => request('page', 1)]) }}"
                class="btn btn-secondary btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-arrow-left"></i>
                <span class="btn-text ms-2"> Quay lại danh sách</span>
            </a>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
                <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'Khách vãng lai' }}</p>
                <p><strong>Tên người nhận:</strong> {{ $order->name }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->address }}</p>
                <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
                <p><strong>Phương thức thanh toán:</strong>
                    {{ $paymentMethodMap[$order->payment_method] ?? ucfirst($order->payment_method) }}</p>
                <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Phương thức giao hàng:</strong>
                    {{ $shippingMap[$order->shipping_method] ?? ucfirst($order->shipping_method) }}</p>
                <p><strong>Trạng thái thanh toán:</strong>
                    {{ $paymentMap[$order->payment_status] ?? ucfirst($order->payment_status) }}</p>

                <p><strong>Ghi chú:</strong> {{ $order->note ?: '-' }}</p>
                <p>
                    <strong>Trạng thái:</strong>
                    <span
                        class="badge bg-{{ $order->status == 'pending'
                            ? 'warning'
                            : ($order->status == 'processing'
                                ? 'primary'
                                : ($order->status == 'shipping'
                                    ? 'info'
                                    : ($order->status == 'completed'
                                        ? 'success'
                                        : 'danger'))) }}">
                        {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                    </span>
                </p>

                <p><strong>Tổng tiền:</strong> {{ number_format($order->total_money, 0, ',', '.') }} đ</p>
            </div>

            <h4>Sản phẩm trong đơn hàng:</h4>
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Dung lượng</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Tổng</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->productVariant->product->name ?? '-' }}</td>
                            <td>{{ $item->productVariant->volume_id ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price_at_time, 0, ',', '.') }} đ</td>
                            <td>{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="mt-4 w-50">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="form-group">
                    <label for="status">Cập nhật trạng thái đơn hàng:</label>
                    <select name="status" id="status" class="form-control"
                        {{ in_array($order->status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                        @foreach ($statusMap as $key => $label)
                            @php
                                $statusIndex = array_search($key, $statusOrder);
                                // Disable các trạng thái trước trạng thái hiện tại (trong $statusOrder)
                                $disabled =
                                    $statusIndex !== false && $currentIndex !== false && $statusIndex < $currentIndex;

                                // Nếu trạng thái hiện tại khác 'pending' thì disable luôn lựa chọn hủy đơn
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
                    <button type="submit" class="btn btn-primary mt-3">Cập nhật trạng thái</button>
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

        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection
