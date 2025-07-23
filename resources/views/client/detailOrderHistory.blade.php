@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đơn hàng
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="modal-body">
            <div class="card mb-0">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-sm-flex">
                                <div class="flex-grow-1">
                                    {{-- Logo công ty --}}
                                    <img src="{{ asset('images/black_logo.png') }}" class="card-logo card-logo-dark"
                                        alt="logo tối" height="56">

                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">

                                    <h6><span class="text-muted fw-normal">Email:</span> email123@
                                    </h6>

                                    <h6 class="mb-0"><span class="text-muted fw-normal">Điện thoại:</span>
                                        {{ $order->phone }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Mã đơn hàng</p>
                                    <h5 class="fs-15 mb-0">{{ $order->order_code }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Ngày đặt</p>
                                    <h5 class="fs-15 mb-0">{{ $order->created_at->format('d/m/Y') }} <small
                                            class="text-muted">{{ $order->created_at->format('h:ia') }}</small></h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Trạng thái</p>
                                    <span class="status-label status-{{ $order->status }}">
                                        {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Tổng cộng</p>
                                    <h5 class="fs-15 mb-0">{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="row g-3">
                                <div class="col-6">
                                    <h6 class="text-muted text-uppercase fw-semibold fs-15 mb-3">Địa chỉ giao hàng</h6>
                                    <p class="text-muted mb-1">Địa chỉ: <span class="fw-medium"> {{ $order->address }}
                                        </span></p>
                                    <p class="text-muted mb-1">Điện thoại: <span class="fw-medium"> {{ $order->phone }}
                                        </span></p>
                                    <p class="text-muted mb-1">Loại giao hàng:
                                        <span class="fw-medium">
                                            {{ $order->shipping_fee == 25000 ? 'Giao hàng tiêu chuẩn' : 'Giao hàng nhanh' }}
                                        </span>
                                    </p>
                                    @php
                                        $deliveryDate = null;

                                        if (in_array($order->status, ['pending', 'processing'])) {
                                            // Dự kiến giao theo ngày đặt + thời gian giao tùy phương thức
                                            $daysToAdd = $order->shipping_fee == 25000 ? 3 : 1;
                                            $deliveryDate = $order->created_at->copy()->addDays($daysToAdd);
                                        } elseif ($order->status === 'shipping') {
                                            // Dự kiến giao là 1 ngày sau khi bắt đầu giao hàng
                                            $deliveryDate = $order->updated_at->copy()->addDay();
                                        }
                                    @endphp

                                    @if ($deliveryDate)
                                        <p class="text-muted mb-1">Ngày dự kiến giao hàng: <span
                                                class="fw-medium">{{ $deliveryDate->format('d/m/Y') }}</span></p>
                                    @elseif ($order->status === 'completed')
                                        <p>Trạng thái: Đơn hàng đã hoàn thành</p>
                                    @elseif ($order->status === 'cancelled')
                                        <p>Trạng thái: Đơn hàng đã bị hủy</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                    <thead>
                                        <tr class="table-light">
                                            <th scope="col" style="width: 50px;">#</th>
                                            <th scope="col">Hình ảnh</th>
                                            <th scope="col">Sản phẩm</th>
                                            <th scope="col">Dung tích</th>
                                            <th scope="col">Đơn giá</th>
                                            <th scope="col">Số lượng</th>
                                            <th scope="col" class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            @php
                                                $variant = $item->productVariant;
                                                $product = $variant->product ?? null;
                                            @endphp
                                            <tr>
                                                {{-- Số thứ tự --}}
                                                <th scope="row">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                                </th>
                                                {{-- Hình ảnh --}}
                                                <td>
                                                    <img src="{{ $product?->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                                        alt="Hình ảnh" width="80">
                                                </td>
                                                {{-- Tên sản phẩm và mô tả --}}
                                                <td class="text-start">
                                                    <span class="fw-medium">{{ $product?->name ?? '—' }}</span>
                                                    <p class="text-muted mb-0">{{ $variant?->description ?? '' }}</p>
                                                </td>
                                                {{-- Dung tích --}}
                                                <td>DUng tích</td>
                                                {{-- Giá và số lượng --}}
                                                <td>{{ number_format($item->price_at_time, 0, ',', '.') }} VNĐ</td>
                                                <td>{{ $item->quantity }}</td>
                                                {{-- Thành tiền --}}
                                                <td class="text-end">{{ number_format($item->total_price, 0, ',', '.') }}
                                                    VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                            <div class="border-top border-top-dashed mt-2">
                                <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto"
                                    style="width:250px">
                                    <tbody>
                                        <tr>
                                            <td>Tổng tạm tính</td>
                                            <td class="text-end">{{ number_format($item->total_price, 0, ',', '.') }} VNĐ
                                            </td>
                                        </tr>

                                        @if ($order->discount_amount)
                                            <tr>
                                                <td>Giảm giá <small
                                                        class="text-muted">({{ $order->discount_code }})</small></td>
                                                <td class="text-end">-
                                                    ${{ number_format($order->discount_amount, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>Phí vận chuyển</td>
                                            <td class="text-end">{{ number_format($order->shipping_fee, 0, ',', '.') }} VNĐ
                                            </td>
                                        </tr>
                                        <tr class="border-top border-top-dashed fs-15">
                                            <th scope="row">Tổng cộng</th>
                                            <th class="text-end">{{ number_format($order->total_money, 0, ',', '.') }} VNĐ
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Chi tiết thanh toán</h6>
                                <p class="text-muted mb-1">Phương thức thanh toán: <span class="fw-medium">Thanh toán tại
                                        nhà</span>
                                </p>

                                <p class="text-muted">Tổng cộng: <span
                                        class="fw-medium">{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</span>
                                </p>
                            </div>
                            <div class="mt-4">
                                <div class="alert alert-light">
                                    <p class="mb-0"><span class="fw-semibold">GHI CHÚ:</span>
                                        {{ $order->notes ?? 'Không có ghi chú' }}</p>
                                </div>
                            </div>
                            <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                @if ($order->status === 'pending')
                                    <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">Hủy đơn hàng</button>
                                    </form>
                                @endif
                                <a href="{{ route('client.orderHistory') }}" class="btn btn-outline-secondary btn-sm">Quay
                                    lại</a>
                            </div>
                        </div>
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
