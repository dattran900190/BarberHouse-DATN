@extends('layouts.ClientLayout')

@section('title-page')
    Thanh toán Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-100 h-custom">
            <div class="mainCheckout">
                <form method="POST" action="{{ route('cart.checkout.process') }}" class="mainCheckout">
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
                                value="{{ old('name', $userInfo['name']) }}"  />
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
                                <h5><span id="total-display">{{ number_format($total + $shippingFee, 0, ',') }}</span> VNĐ
                                </h5>
                            </div>
                        </div>

                        <div class="dat-hang">
                            <a href="{{ route('cart.show') }}"><i class="fa-solid fa-angle-left"></i> Quay về giỏ
                                hàng</a>
                            <button class="btn-outline-buy" type="submit" style="padding: 6px 14px;">
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
    <script>
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
                    totalDisplay.textContent = total.toLocaleString('vi-VN');
                    shippingFeeDisplay.textContent = selectedFee.toLocaleString('vi-VN') + ' VNĐ';
                });
            });

            // Xử lý sự kiện submit form
            document.querySelector('form.mainCheckout').addEventListener('submit', function(e) {
                e.preventDefault(); // Ngăn submit mặc định để kiểm tra

                // Lấy giá trị các trường bắt buộc
const name = document.getElementById('name').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const address = document.getElementById('address').value.trim();

                // Mảng chứa các lỗi
                let errors = [];

                // Kiểm tra các trường bắt buộc
                if (!name) {
                    errors.push("Vui lòng nhập tên người nhận.");
                }
                if (!phone) {
                    errors.push("Vui lòng nhập số điện thoại.");
                } else if (!/^\d{10,11}$/.test(phone)) { // Kiểm tra định dạng số điện thoại (10-11 số)
                    errors.push("Số điện thoại không hợp lệ. Vui lòng nhập 10 hoặc 11 số.");
                }
                if (!address) {
                    errors.push("Vui lòng nhập địa chỉ.");
                }

                // Nếu có lỗi client-side, hiển thị thông báo
                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Thiếu thông tin',
                        html: errors.join('<br>'),
                    });
                    return; // Dừng lại, không submit form
                }

                // Nếu không có lỗi client-side, submit form qua AJAX để kiểm tra lỗi server
                const form = this;
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                })
                .then(response => response.json()) // Giả sử server trả về JSON
                .then(data => {
                    if (data.error) {
                        // Hiển thị lỗi từ server (session)
                        Swal.fire({
                            icon: 'warning',
                            title: 'Thông báo',
                            text: data.error
                        });
                    } else {
                        // Nếu không có lỗi, submit form bình thường
                        form.submit();
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi gửi yêu cầu. Vui lòng thử lại.'
                    });
                });
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection