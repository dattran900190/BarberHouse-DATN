@extends('layouts.ClientLayout')

@section('title-page')
    Thanh toán Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-100 h-custom">
            <div class="mainCheckout">
                <form method="POST" action="{{ route('cart.checkout.process') }}" class="mainCheckout" id="checkoutForm">
                    @if (isset($buyNow) && $buyNow)
                        <input type="hidden" name="buy_now" value="1">
                    @endif

                    @csrf
                    {{-- Thông tin người dùng --}}
                    <div class="informationUser">
                        <h3>Thông tin nhận hàng</h3>
                        <div class="mb-3">
                            <label for="name" class="form-label">Tên người nhận</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $userInfo['name']) }}" />
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $userInfo['email']) }}" readonly required />
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $userInfo['phone']) }}" />
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="address" name="address"
                                value="{{ old('address', $userInfo['address']) }}" />
                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" id="note" rows="5">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Vận chuyển và thanh toán --}}
                    <div class="informationProduct">
                        <h3>Vận chuyển</h3>
                        @php
                            $shippingOptions = [
                                ['label' => 'Tiêu chuẩn (2-4 ngày)', 'value' => 'standard', 'fee' => 25000],
                                ['label' => 'Giao nhanh (1 ngày)', 'value' => 'express', 'fee' => 100000],
                            ];
                            $shippingFee = old('shipping_fee', 25000);
                        @endphp
                        <input type="hidden" id="shipping_fee_input" name="shipping_fee" value="{{ $shippingFee }}" />
                        <input type="hidden" name="delivery_method" value="standard" />

                        @foreach ($shippingOptions as $option)
                            <div class="form-check"
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <div class="chon">
                                    <input class="form-check-input" type="radio" name="delivery_method"
                                        id="delivery_{{ $option['value'] }}" value="{{ $option['value'] }}"
                                        data-fee="{{ $option['fee'] }}" {{ $loop->first ? 'checked' : '' }} />
                                    <label class="form-check-label" for="delivery_{{ $option['value'] }}">
                                        {{ $option['label'] }}
                                    </label>
                                </div>
                                <div class="ship">{{ number_format($option['fee'], 0, ',', '.') }} VNĐ</div>
                            </div>
                        @endforeach

                        <h3>Thanh toán</h3>
                        <div class="form-check" style="display: flex; justify-content: space-between">
                            <div class="chon">
                                <input class="form-check-input" value="1" type="radio"
                                    name="phuong_thuc_thanh_toan_id" id="paymentMethodCOD" checked required />
                                <label class="form-check-label" for="paymentMethodCOD">Thanh toán khi giao hàng
                                    (COD)</label>
                            </div>
                            <div class="icon-bank">
                                <i class="fa-regular fa-money-bill-1"></i>
                            </div>
                        </div>
                        <div class="form-check" style="display: flex; justify-content: space-between">
                            <div class="chon">
                                <input class="form-check-input" value="2" type="radio"
                                    name="phuong_thuc_thanh_toan_id" id="paymentMethodVNPAY" />
                                <label class="form-check-label" for="paymentMethodVNPAY">Thanh toán qua VNPAY-QR</label>
                            </div>
                            <div class="icon-bank">
                                <i class="fa-solid fa-qrcode"></i>
                            </div>
                        </div>
                    </div>

                    <div class="informationProduct">
                        <h3>Thông tin sản phẩm</h3>
                        <table>
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
                                            value="{{ $item[$field] }}" />
                                    @endforeach

                                    <tr style="border-bottom: 1px solid #ccc; padding: 10px 0">
                                        <td style="padding: 10px">
                                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" width="80px" />
                                        </td>
                                        <td style="padding: 20px; line-height: 20px; font-size: 12px">
                                            Tên sản phẩm: {{ $item['name'] }}<br />
                                            Số lượng: {{ $item['quantity'] }} <br />
                                            Giá: {{ number_format($item['price'], 0, ',', '.') }} VNĐ
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="phi-tam-thoi">
                            <div class="tam-tinh">
                                <p>Tạm tính</p>
                                {{ number_format($total, 0, ',', '.') }} VNĐ
                            </div>
                            <div class="phi-van-chuyen">
                                <p>Phí vận chuyển</p>
                                <span id="shipping-fee-display">{{ number_format($shippingFee, 0, ',', '.') }} VNĐ</span>
                            </div>
                        </div>

                        <div class="tong-cong">
                            <div class="tong-thanh-toan">
                                <p>Tổng tiền thanh toán</p>
                                <input type="hidden" id="total-input" name="tong_tien"
                                    value="{{ $total + $shippingFee }}" />
                                <h5><span id="total-display">{{ number_format($total + $shippingFee, 0, '.', '.') }}
                                        VNĐ</span></h5>
                            </div>
                        </div>

                        <div class="dat-hang">
                            <a href="{{ route('cart.show') }}"><i class="fa-solid fa-angle-left"></i> Quay về giỏ
                                hàng</a>
                            <button class="btn-outline-buy checkout-btn" type="submit" style="padding: 6px 14px;">
                                <i class="fa-solid fa-check"></i> Đặt hàng
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Kiểm tra trạng thái đăng nhập
        window.userLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        document.addEventListener("DOMContentLoaded", function() {
            // Cập nhật phí vận chuyển và tổng tiền khi thay đổi phương thức giao hàng
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
                    totalDisplay.textContent = total.toLocaleString('vi-VN', {
                        maximumFractionDigits: 0
                    }) + ' VNĐ';


                    shippingFeeDisplay.textContent = selectedFee.toLocaleString('vi-VN') + ' VNĐ';
                });
            });

            // Xử lý sự kiện click nút Đặt hàng
            document.querySelector('.checkout-btn').addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById('checkoutForm');

                // Kiểm tra form trước khi gửi
                if (!form.checkValidity()) {
                    form.reportValidity(); // Hiển thị lỗi HTML5 mặc định
                    return;
                }

                // Kiểm tra thêm các trường bắt buộc phía client
                const name = document.getElementById('name').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const address = document.getElementById('address').value.trim();
                let errors = [];

                if (!name) {
                    errors.push("Vui lòng nhập tên người nhận.");
                }
                if (!phone) {
                    errors.push("Vui lòng nhập số điện thoại.");
                } else if (!/^\d{10,11}$/.test(phone)) {
                    errors.push("Số điện thoại không hợp lệ. Vui lòng nhập 10 hoặc 11 số.");
                }
                if (!address) {
                    errors.push("Vui lòng nhập địa chỉ.");
                }

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
                    // Hiển thị SweetAlert2 để xác nhận khi đã đăng nhập
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
                            // Cửa sổ loading
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

                            // Thu thập dữ liệu từ form
                            const formData = new FormData(form);

                            // Gửi yêu cầu AJAX
                            fetch('{{ route('cart.checkout.process') }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => {
                                    // Kiểm tra xem phản hồi có phải JSON không
                                    const contentType = response.headers.get('content-type');
                                    if (!contentType || !contentType.includes(
                                            'application/json')) {
                                        return response.text().then(text => {
                                            throw new Error(
                                                'Phản hồi từ server không phải JSON: ' +
                                                text.substring(0, 100));
                                        });
                                    }
                                    return response.json().then(data => ({
                                        status: response.status,
                                        data
                                    }));
                                })
                                .then(({
                                    status,
                                    data
                                }) => {
                                    Swal.close();
                                    if (status !== 200) {
                                        throw data;
                                    }
                                    if (data.success) {
                                        if (data.payment_method === 'vnpay') {
                                            // Chuyển hướng đến thanh toán VNPay
                                            window.location.href = data.redirect_url;
                                        } else {
                                            Swal.fire({
                                                title: 'Thành công!',
                                                text: data.message,
                                                icon: 'success',
                                                customClass: {
                                                    popup: 'custom-swal-popup'
                                                }
                                            }).then(() => {
                                                window.location.href = data
                                                    .redirect_url;
                                            });
                                        }
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    let errorMessage = 'Đã có lỗi xảy ra khi xử lý yêu cầu.';
                                    if (error.errors) {
                                        errorMessage = Object.values(error.errors).flat().join(
                                            '<br>');
                                    } else if (error.message) {
                                        errorMessage = error.message;
                                    }

                                    Swal.fire({
                                        title: 'Lỗi!',
                                        html: errorMessage,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                } else {
                    // Nếu chưa đăng nhập, yêu cầu đăng nhập
                    Swal.fire({
                        title: 'Cần đăng nhập',
                        text: 'Bạn cần đăng nhập để đặt hàng.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Đăng nhập',
                        cancelButtonText: 'Hủy',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('login') }}';
                        }
                    });
                }
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
