@extends('layouts.ClientLayout')

@section('title-page')
    Thanh toán Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-100 h-custom">
            <div class="mainCheckout">
                @if (session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mt-3" id="error-alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger mt-3" id="error-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('cart.checkout.process') }}" class="mainCheckout">
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
                                value="{{ old('email', $userInfo['email']) }}" readonly />
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
                        @endphp
                        @php
                            $shippingFee = old('shipping_fee', 25000); // Mặc định là phí giao hàng tiêu chuẩn
                        @endphp
                        <input type="hidden" name="shipping_fee" value="{{ $shippingFee }}" />
                        <input type="hidden" name="delivery_method" value="standard" />

                        @foreach ($shippingOptions as $option)
                            <div class="form-check" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
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
                        <input type="hidden" id="shipping_fee_input" name="shipping_fee" value="{{ $shippingFee }}">

                        <h3>Thanh toán</h3>
                        <div class="form-check" style="display: flex; justify-content: space-between">
                            <div class="chon">
                                <input class="form-check-input" value="1" type="radio"
                                    name="phuong_thuc_thanh_toan_id" id="paymentMethodCOD" checked />
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
                                <span>{{ number_format($shippingFee, 0, ',', '.') }} VNĐ</span>
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
            // Chọn phương thức thanh toán
            document.querySelectorAll('input[name="phuong_thuc_thanh_toan_id"]').forEach(method => {
                method.addEventListener('change', function() {
                    console.log('Phương thức thanh toán đã chọn:', this.value);
                });
            });

            // Cập nhật phí vận chuyển + tổng tiền khi chọn phương thức giao hàng
            const radios = document.querySelectorAll('input[name="delivery_method"]');
            const shippingFeeInput = document.getElementById('shipping_fee_input');
            const totalInput = document.getElementById('total-input');
            const totalDisplay = document.getElementById('total-display');
            const productTotal = {{ $total }}; // Dữ liệu tổng tiền sản phẩm

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const selectedFee = parseInt(this.getAttribute('data-fee'));
                    shippingFeeInput.value = selectedFee;

                    // Cập nhật lại tổng tiền
                    const total = productTotal + selectedFee;
                    totalInput.value = total;
                    totalDisplay.textContent = total.toLocaleString('vi-VN', {
                        maximumFractionDigits: 0
                    });
                });
            });
        });
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const errorAlert = document.getElementById('error-alert');
            if (errorAlert) {
                // Cuộn lên đầu trang khi có lỗi
                window.scrollTo({ top: errorAlert.offsetTop - 20, behavior: 'smooth' });
            }
        });
    </script>
@endsection
