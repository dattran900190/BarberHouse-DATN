# Hướng dẫn cấu hình VNPAY cho thanh toán online

## Cấu hình môi trường

Thêm các biến môi trường sau vào file `.env`:

```env
# VNPAY Configuration
VNPAY_TMN_CODE=your_tmn_code_here
VNPAY_HASH_SECRET=your_hash_secret_here
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_RETURN_URL=http://your-domain.com/payment/vnpay/callback
```

## Giải thích các biến:

- `VNPAY_TMN_CODE`: Mã Terminal ID từ VNPAY
- `VNPAY_HASH_SECRET`: Khóa bí mật để tạo chữ ký
- `VNPAY_URL`: URL thanh toán VNPAY (sandbox hoặc production)
- `VNPAY_RETURN_URL`: URL callback cho thanh toán lịch hẹn

## Chức năng đã được thêm:

### 1. Thanh toán VNPAY cho đặt lịch
- Route: `POST /payment/vnpay`
- Callback: `GET /payment/vnpay/callback`
- Controller: `PaymentController@vnpayPayment` và `PaymentController@vnpayCallback`

### 2. Thanh toán VNPAY cho đơn hàng sản phẩm
- Route: `GET/POST /payment/vnpay/order`
- Callback: `GET /payment/vnpay/order/callback`
- Controller: `PaymentController@vnpayOrderPayment` và `PaymentController@vnpayOrderCallback`

### 3. Cập nhật CartController
- Xử lý thanh toán VNPAY trong `processCheckout()`
- Chuyển hướng đến VNPAY khi chọn phương thức thanh toán VNPAY

### 4. Cập nhật Model Order
- Thêm các trường: `email`, `shipping_method`, `shipping_fee`, `payment_status`
- Thêm relationship với User

## Luồng thanh toán:

### Đặt lịch:
1. User chọn VNPAY trong form đặt lịch
2. `AppointmentController@store` tạo appointment với `payment_method = 'vnpay'`
3. Chuyển hướng đến `PaymentController@vnpayPayment`
4. User thanh toán trên VNPAY
5. VNPAY callback về `PaymentController@vnpayCallback`
6. Cập nhật `payment_status = 'paid'`

### Đặt hàng sản phẩm:
1. User chọn VNPAY trong form checkout
2. `CartController@processCheckout` tạo order với `payment_method = 'vnpay'`
3. Chuyển hướng đến `PaymentController@vnpayOrderPayment`
4. User thanh toán trên VNPAY
5. VNPAY callback về `PaymentController@vnpayOrderCallback`
6. Cập nhật `payment_status = 'paid'`

## Lưu ý:
- Đảm bảo cấu hình đúng URL callback trong VNPAY Merchant
- Test với sandbox trước khi chuyển sang production
- Kiểm tra chữ ký bảo mật trong callback để đảm bảo an toàn
- Route `/payment/vnpay/order` hỗ trợ cả GET và POST để tránh lỗi method not supported