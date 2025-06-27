@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử đặt hàng
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
        <div class="container">
            <div class="card order-history mt-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-0 m-3">
                    <h3 class="mb-0 fw-bold">Lịch sử đặt hàng của tôi</h3>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Lọc lịch sử
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="#">Đơn hàng đã giao</a></li>
                            <li><a class="dropdown-item" href="#">Đơn hàng đang xử lý</a></li>
                            <li><a class="dropdown-item" href="#">Đơn hàng đã hủy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form class="d-flex mb-4">
                        <input type="text" class="form-control me-2" placeholder="Tìm kiếm đơn hàng">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                    </form>
                    @foreach ($orders as $order)
                        <div class="order-item mb-3 p-3 rounded-3 shadow-sm">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <span class="fw-bold">Đơn hàng: {{ $order->order_code }}</span><br>
                                    <span class="text-dark">
                                        {{ $order->items->pluck('name')->join(', ') }}
                                    </span><br>
                                    <span class="text-muted">Tổng số lượng: {{ $order->items->sum('quantity') }}</span><br>
                                    <span class="text-muted">Tổng tiền:
                                        {{ number_format($order->total_money, 0, ',', '.') }} VNĐ</span><br>
                                    <span class="text-muted">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="col-md-2 text-center">
                                    <span class="status-label status-{{ $order->status }}">
                                        {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                                    </span>

                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="d-flex justify-content-center">
                                        <a class="btn btn-outline-primary btn-sm me-1"
                                            href="{{ route('client.detailOrderHistory', $order->id) }}">Xem chi tiết</a>
                                        @if ($order->status === 'pending')
                                            <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST"
                                                style="display:inline-block;"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Hủy đơn
                                                    hàng</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

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
