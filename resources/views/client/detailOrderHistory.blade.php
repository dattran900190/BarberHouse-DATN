@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đơn hàng
@endsection

@section('content')
@php
        $statusMap = [
            'pending' => 'Chờ xử lý',
            'processing' => 'Đang xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
    @endphp
    <main style="padding: 10%">
        <div class="container mt-5">
            <div class="card order-detail shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center border-0">
                    <h3 class="mb-0 fw-bold">Chi tiết đơn hàng</h3>
                    <div>
                        <strong>Trạng thái:</strong>
                        <span class="status-label status-{{ $order->status }}">
                            {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                        </span>

                    </div>

                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
                            <p><strong>Ngày đặt hàng:</strong> {{ $order->created_at->format('d/m/Y') }}</p>

                        </div>

                    </div>
                    <h5 class="fw-bold">Danh sách sản phẩm</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $statusMap = [
                                    'pending' => 'Chờ xử lý',
                                    'processing' => 'Đang xử lý',
                                    'shipping' => 'Đang giao hàng',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                ];
                            @endphp
                            @foreach ($order->items as $item)
                                @php
                                    $variant = $item->productVariant;
                                    $product = $variant->product ?? null;
                                @endphp
                                <tr>
                                    <td><img src="{{ $product?->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                            alt="Hình ảnh" width="80"></td>
                                    <td>{{ $product?->name ?? 'Sản phẩm không tồn tại' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price_at_time, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ number_format($item->total_price, 0, ',', '.') }} VNĐ</td>
                                </tr>
                            @endforeach

                    </table>
                    <h5 class="fw-bold mt-4">Thông tin giao hàng</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Địa chỉ giao hàng:</strong> {{ $order->address }}</p>
                            <p><strong>Phương thức giao hàng:</strong>
                                {{ $order->shipping_fee == 25000 ? 'Giao hàng tiêu chuẩn' : 'Giao hàng nhanh' }}
                            </p>

                        </div>
                        <div class="col-md-6">
                            <p><strong>Ngày dự kiến giao hàng:</strong> 06/05/2025</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold">Tổng tiền: {{ number_format($order->total_money, 0, ',', '.') }} VNĐ</h5>

                    <div>
                        <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST"
                            style="display:inline-block;"
                            onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">Hủy đơn hàng</button>
                        </form>

                        <a href="{{ route('client.orderHistory') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
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
@endsection

@section('card-footer')
@endsection

@section('scripts')
@endsection
