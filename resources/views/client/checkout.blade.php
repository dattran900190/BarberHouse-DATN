@extends('layouts.ClientLayout')

@section('title-page')
    Thanh toán Baber House
@endsection

@section('content')
    <main class="container py-4 mb-3 pt-10" style="margin-top: 70px;">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <form method="POST" action="{{ route('cart.checkout.process') }}" id="checkoutForm">
                    @if (isset($buyNow) && $buyNow)
                        <input type="hidden" name="buy_now" value="1">
                    @endif
                    @csrf
                    <div class="row" style="margin-top: 70px;">
                        {{-- Cột trái - Thông tin đặt hàng --}}
                        <div class="col-12 col-lg-8 mb-4">
                            {{-- Thông tin người dùng --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Thông tin nhận hàng</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="name" class="form-label fw-bold">Tên người nhận <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ old('name', $userInfo['name']) }}"
                                                placeholder="Nhập tên người nhận">
                                            @error('name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <label for="phone" class="form-label fw-bold">Số điện thoại <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone', $userInfo['phone']) }}"
                                                placeholder="Nhập số điện thoại">
                                            @error('phone')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Địa chỉ Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $userInfo['email']) }}" readonly required>
                                        @error('email')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="address" class="form-label fw-bold">Địa chỉ <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ old('address', $userInfo['address']) }}"
                                            placeholder="Nhập địa chỉ giao hàng">
                                        @error('address')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="note" class="form-label fw-bold">Ghi chú</label>
                                        <textarea name="note" class="form-control" id="note" rows="3"
                                            placeholder="Ghi chú cho đơn hàng (không bắt buộc)">{{ old('note') }}</textarea>
                                        @error('note')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Vận chuyển --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0"><i class="fa-solid fa-truck me-2"></i>Vận chuyển</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $shippingOptions = [
                                            ['label' => 'Tiêu chuẩn (2-4 ngày)', 'value' => 'standard', 'fee' => 25000],
                                            ['label' => 'Giao nhanh (1 ngày)', 'value' => 'express', 'fee' => 100000],
                                        ];
                                        $shippingFee = old('shipping_fee', 25000);
                                    @endphp
                                    <input type="hidden" id="shipping_fee_input" name="shipping_fee"
                                        value="{{ $shippingFee }}">
                                    <input type="hidden" name="delivery_method" value="standard">

                                    @foreach ($shippingOptions as $option)
                                        <div
                                            class="form-check d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                            <div class="form-check d-flex align-items-center">
                                                <input class="form-check-input me-3" type="radio" name="delivery_method"
                                                    id="delivery_{{ $option['value'] }}" value="{{ $option['value'] }}"
                                                    data-fee="{{ $option['fee'] }}" {{ $loop->first ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold"
                                                    for="delivery_{{ $option['value'] }}">
                                                    {{ $option['label'] }}
                                                </label>
                                            </div>
                                            <span
                                                class="fw-bold text-dark fs-6">{{ number_format($option['fee'], 0, ',', '.') }}
                                                VNĐ</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Thanh toán --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fa-solid fa-credit-card me-2"></i>Thanh toán</h5>
                                </div>
                                <div class="card-body">
                                    <div
                                        class="form-check d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-3" value="1" type="radio"
                                                name="phuong_thuc_thanh_toan_id" id="paymentMethodCOD" checked required>
                                            <label class="form-check-label fw-bold" for="paymentMethodCOD">
                                                Thanh toán khi nhận hàng (COD)
                                            </label>
                                        </div>
                                        <i class="fa-regular fa-money-bill-1 fs-4 text-success"></i>
                                    </div>

                                    <div
                                        class="form-check d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                        <div class="d-flex align-items-center">
                                            <input class="form-check-input me-3" value="2" type="radio"
                                                name="phuong_thuc_thanh_toan_id" id="paymentMethodVNPAY">
                                            <label class="form-check-label fw-bold" for="paymentMethodVNPAY">
                                                Thanh toán qua VNPAY-QR
                                            </label>
                                        </div>
                                        <i class="fa-solid fa-qrcode fs-4 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Cột phải - Thông tin sản phẩm và tổng tiền --}}
                        <div class="col-12 col-lg-4">
                            <div class="card shadow-sm sticky-top" style="top: 20px;">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0"><i class="fa-solid fa-shopping-cart me-2"></i>Thông tin đơn hàng
                                    </h5>
                                </div>
                                <div class="card-body">
                                    {{-- Sản phẩm --}}
                                    <div class="mb-4">
                                        <h6 class="fw-bold mb-3">Sản phẩm đã chọn</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <tbody>
                                                    @php $total = 0; @endphp
                                                    @foreach ($items as $item)
                                                        @php
                                                            $subtotal = $item['price'] * $item['quantity'];
                                                            $total += $subtotal;
                                                        @endphp
                                                        @foreach (['cart_item_id', 'id', 'name', 'price', 'quantity', 'image', 'product_variant_id'] as $field)
                                                            <input type="hidden"
                                                                name="items[{{ $loop->parent->index }}][{{ $field }}]"
                                                                value="{{ $item[$field] }}">
                                                        @endforeach

                                                        <tr>
                                                            <td style="width: 60px">
                                                                <img src="{{ $item['image'] }}"
                                                                    alt="{{ $item['name'] }}" class="img-fluid rounded"
                                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                                            </td>
                                                            <td class="small">
                                                                <strong>{{ $item['name'] }}</strong><br>
                                                                <span class="text-muted">SL:
                                                                    {{ $item['quantity'] }}</span><br>
                                                                <span class="text-muted">Dung tích:
                                                                    {{ $item['volume_name'] }}</span><br>
                                                                <span
                                                                    class="text-primary">{{ number_format($item['price'], 0, ',', '.') }}
                                                                    VNĐ</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    {{-- Tổng tiền --}}
                                    <div class="border-top pt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Tạm tính:</span>
                                            <strong>{{ number_format($total, 0, ',', '.') }} VNĐ</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Phí vận chuyển:</span>
                                            <strong
                                                id="shipping-fee-display">{{ number_format($shippingFee, 0, ',', '.') }}
                                                VNĐ</strong>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between mb-3">
                                            <span class="fw-bold fs-5">Tổng tiền:</span>
                                            <h5 class="text-primary mb-0"><strong
                                                    id="total-display">{{ number_format($total + $shippingFee, 0, '.', '.') }}
                                                    VNĐ</strong></h5>
                                            <input type="hidden" id="total-input" name="tong_tien"
                                                value="{{ $total + $shippingFee }}">
                                        </div>
                                    </div>

                                    {{-- Nút đặt hàng --}}
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary btn-lg checkout-btn" type="submit">
                                            <i class="fa-solid fa-check me-2"></i>Đặt hàng ngay
                                        </button>
                                        <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary">
                                            <i class="fa-solid fa-angle-left me-2"></i>Quay về giỏ hàng
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sticky-top {
                position: relative !important;
                top: 0 !important;
            }
        }

        @media (max-width: 767.98px) {
            .card-header h5 {
                font-size: 1rem;
            }

            .table-sm td {
                padding: 0.5rem 0.25rem;
            }

            .btn-lg {
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
            }
        }

        /* Custom card styling */
        .card {
            border: none;
            border-radius: 8px;
        }

        .card-header {
            border-radius: 8px 8px 0 0 !important;
            border-bottom: none;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.userLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        document.addEventListener("DOMContentLoaded", function() {
            const radios = document.querySelectorAll('input[name="delivery_method"]');
            const shippingFeeInput = document.getElementById('shipping_fee_input');
            const totalInput = document.getElementById('total-input');
            const totalDisplay = document.getElementById('total-display');
            const shippingFeeDisplay = document.getElementById('shipping-fee-display');
            const productTotal = {{ $total }};

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedFee = parseInt(this.dataset.fee);
                    shippingFeeInput.value = selectedFee;
                    const total = productTotal + selectedFee;
                    totalInput.value = total;
                    totalDisplay.textContent = total.toLocaleString('vi-VN') + ' VNĐ';
                    shippingFeeDisplay.textContent = selectedFee.toLocaleString('vi-VN') + ' VNĐ';
                });
            });

            document.querySelector('.checkout-btn').addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('checkoutForm');
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const name = document.getElementById('name').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const address = document.getElementById('address').value.trim();
                let errors = [];

                if (!name) errors.push("Vui lòng nhập tên người nhận.");
                if (!phone) errors.push("Vui lòng nhập số điện thoại.");
                else if (!/^\d{10,11}$/.test(phone)) errors.push("Số điện thoại không hợp lệ.");
                if (!address) errors.push("Vui lòng nhập địa chỉ.");

                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thiếu thông tin',
                        html: errors.join('<br>'),
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    });
                    return;
                }

                if (window.userLoggedIn) {
                    Swal.fire({
                        title: 'Xác nhận đặt hàng',
                        text: 'Bạn có chắc chắn muốn đặt đơn hàng này?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Đặt hàng',
                        cancelButtonText: 'Hủy',
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
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(form);
                            fetch('{{ route('cart.checkout.process') }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            }).then(response => {
                                const contentType = response.headers.get('content-type');
                                if (!contentType.includes('application/json')) {
                                    return response.text().then(text => {
                                        throw new Error('Lỗi phản hồi: ' + text
                                            .substring(0, 100));
                                    });
                                }
                                return response.json().then(data => ({
                                    status: response.status,
                                    data
                                }));
                            }).then(({
                                status,
                                data
                            }) => {
                                Swal.close();
                                if (status !== 200) throw data;
                                if (data.success) {
                                    if (data.payment_method === 'vnpay') {
                                        window.location.href = data.redirect_url;
                                    } else {
                                        Swal.fire({
                                            title: 'Thành công!',
                                            text: data.message,
                                            icon: 'success',
                                            customClass: {
                                                popup: 'custom-swal-popup'
                                            }
                                        }).then(() => window.location.href = data
                                            .redirect_url);
                                    }
                                }
                            }).catch(error => {
                                Swal.close();
                                let errorMessage = 'Đã có lỗi xảy ra.';
                                if (error.errors) errorMessage = Object.values(error.errors)
                                    .flat().join('<br>');
                                else if (error.message) errorMessage = error.message;

                                Swal.fire({
                                    title: 'Lỗi!',
                                    html: errorMessage,
                                    icon: 'error'
                                });
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Cần đăng nhập',
                        text: 'Bạn cần đăng nhập để đặt hàng.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng nhập',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) window.location.href = '{{ route('login') }}';
                    });
                }
            });
        });
    </script>
@endsection
