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
                                    <img src="{{ asset('storage/' . ($imageSettings['black_logo'] ?? 'default-images/black_logo.png')) }}"
                                        class="card-logo card-logo-dark" alt="logo tối" height="56">

                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">

                                    <h6><span class="text-muted fw-normal">Email:</span> {{ $order->email ?? 'Không xác định' }}
                                    </h6>

                                    <h6 class="mb-0"><span class="text-muted fw-normal">Điện thoại:</span>
                                        {{ $order->phone ?? 'Không xác định' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Mã đơn hàng</p>
                                    <h5 class="fs-15 mb-0">{{ $order->order_code ?? 'Không xác định' }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Ngày đặt</p>
                                    <h5 class="fs-15 mb-0">{{ $order->created_at->format('d/m/Y') }} <small
                                            class="text-muted">{{ $order->created_at->format('h:ia') }}</small></h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Trạng thái</p>
                                    @if ($order->status == 'pending')
                                        <span class="status-label status-processing">Đang chờ</span>
                                    @elseif ($order->status == 'shipping')
                                        <span class="status-label status-confirmed">Đang giao hàng</span>
                                    @elseif ($order->status == 'processing')
                                        <span class="status-label status-info">Đang xử lý</span>
                                    @elseif ($order->status == 'cancelled')
                                        <span class="status-label status-cancelled">
                                            {{ $order->cancellation_type == 'no-show' ? 'Không đến' : 'Đã hủy' }}
                                        </span>
                                    @elseif ($order->status == 'completed')
                                        <span class="status-label status-completed">Đã giao hàng</span>
                                    @endif
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Tổng cộng</p>
                                    <h5 class="fs-15 mb-0">{{ number_format($order->total_money, 0, ',', '.') }} VNĐ</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4 border-top border-top-dashed d-flex justify-content-between">
                            {{-- Cột trái: Địa chỉ nhận hàng --}}
                            <div class="w-50 pe-4">
                                <h6 class="text-muted text-uppercase fw-semibold fs-15">Địa chỉ nhận hàng</h6>
                                <p class="text-muted mb-1">Họ tên: <span class="fw-medium">{{ $order->name ?? 'Không xác định' }}</span></p>
                                <p class="text-muted mb-1">Địa chỉ: <span class="fw-medium">{{ $order->address ?? 'Không xác định' }}</span></p>
                                <p class="text-muted mb-1">Điện thoại: <span class="fw-medium">{{ $order->phone ?? 'Không xác định' }}</span>
                                </p>
                            </div>

                            {{-- Cột phải: Thông tin thanh toán --}}
                            <div class="w-50 ps-4">
                                <h6 class="text-muted text-uppercase fw-semibold fs-15">Thông tin thanh toán</h6>
                                <p class="text-muted mb-1">Thanh toán:
                                    <span class="fw-medium">
                                        {{ $order->shipping_method == 'standard' ? 'Thanh toán khi nhận hàng' : 'VNPay' }}
                                    </span>
                                </p>
                                <p class="text-muted mb-1">Vận chuyển:
                                    <span class="fw-medium">
                                        {{ $order->shipping_fee == 25000 ? 'Giao hàng tiêu chuẩn' : 'Giao hàng nhanh' }}
                                    </span>
                                </p>

                                @php
                                    $deliveryDate = null;

                                    if (in_array($order->status, ['pending', 'processing'])) {
                                        $daysToAdd = $order->shipping_fee == 25000 ? 3 : 1;
                                        $deliveryDate = $order->created_at->copy()->addDays($daysToAdd);
                                    } elseif ($order->status === 'shipping') {
                                        $deliveryDate = $order->updated_at->copy()->addDay();
                                    }
                                @endphp

                                @if ($deliveryDate)
                                    <p class="text-muted mb-1">Ngày dự kiến giao hàng:
                                        <span class="fw-medium">{{ $deliveryDate->format('d/m/Y') }}</span>
                                    </p>
                                @elseif ($order->status === 'completed')
                                    <p>Trạng thái: Đơn hàng đã hoàn thành</p>
                                @elseif ($order->status === 'cancelled')
                                    <p>Trạng thái: Đơn hàng đã bị hủy</p>
                                @endif
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
                                                $subtotal =
                                                    $order->total_money -
                                                    $order->shipping_fee +
                                                    ($order->discount_amount ?? 0);
                                            @endphp
                                            <tr>
                                                {{-- Số thứ tự --}}
                                                <th scope="row">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                                </th>
                                                {{-- Hình ảnh --}}
                                                <td>
                                                    <img src="{{ $product?->image ? asset('storage/' . $product->image) : asset('images/no-image.png') ?? 'Không xác định' }}"
                                                        alt="Hình ảnh" width="80">
                                                </td>
                                                {{-- Tên sản phẩm và mô tả --}}
                                                <td class="text-start">
                                                    <span class="fw-medium">{{ $product?->name ?? 'Không xác định' }}</span>
                                                    <p class="text-muted mb-0">{{ $variant?->description ?? 'Không xác định' }}</p>
                                                </td>
                                                {{-- Dung tích --}}
                                                <td>
                                                    {{ $item->volume_name ?? 'Không xác định' }}
                                                </td>
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
                                            <td class="text-end">{{ number_format($subtotal, 0, ',', '.') }} VNĐ</td>
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

                            <div class="mt-4">
                                <div class="alert alert-light">
                                    <p class="mb-0"><span class="fw-semibold">GHI CHÚ:</span>
                                        {{ $order->note ?? 'Không có ghi chú' }}</p>
                                </div>
                            </div>
                            <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                @if ($order->status === 'pending')
                                    <button type="button" class="btn-outline-show cancel-order-btn"
                                        data-order-id="{{ $order->id }}">
                                        Hủy đơn hàng
                                    </button>

                                    <form id="cancel-form-{{ $order->id }}"
                                        action="{{ route('client.orders.cancel', $order->id) }}" method="POST"
                                        style="display: none;">
                                        @csrf
                                    </form>
                                @endif
                                <a href="{{ route('client.orderHistory') }}" class="btn-outline-show">Quay lại</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.cancel-order-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.getAttribute('data-order-id');
                    const cancelUrl = this.getAttribute('data-cancel-url');

                    Swal.fire({
                        title: 'Hủy đơn hàng',
                        text: 'Vui lòng nhập lý do hủy',
                        input: 'textarea',
                        inputPlaceholder: 'Nhập lý do hủy (tối thiểu 5 ký tự)...',
                        inputAttributes: {
                            rows: 4
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Hủy đơn',
                        cancelButtonText: 'Đóng',
                        inputValidator: (value) => {
                            if (!value) return 'Lý do hủy không được để trống!';
                            if (value.length < 5)
                                return 'Lý do hủy phải có ít nhất 5 ký tự!';
                            if (value.length > 500)
                                return 'Lý do hủy không được vượt quá 500 ký tự!';
                        },
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });

                            fetch(cancelUrl, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: JSON.stringify({
                                        cancellation_reason: result.value
                                    })
                                })
                                .then(response => {
                                    if (!response.ok) throw new Error(
                                        `HTTP error: ${response.status}`);
                                    return response.json();
                                })
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' :
                                            'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' :
                                            'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) location.reload();
                                    });
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error
                                            .message,
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
        });
    </script>
@endsection
