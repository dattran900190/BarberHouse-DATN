<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng tại Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Cảm ơn bạn đã đặt hàng tại Barber House</h2>

        <p>Xin chào <strong>{{ $order->name }}</strong>,</p>

        <p>Chúng tôi đã nhận được đơn hàng của bạn với mã <strong>{{ $order->order_code }}</strong>.</p>

        <h4>Thông tin đơn hàng:</h4>
        <ul>
            <li><strong>Người nhận:</strong> {{ $order->name }}</li>
            <li><strong>Số điện thoại:</strong> {{ $order->phone }}</li>
            <li><strong>Địa chỉ:</strong> {{ $order->address }}</li>
            <li><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }} VNĐ</li>
            @php
                $paymentMap = [
                    'cash' => 'Tiền mặt',
                    'vnpay' => 'VNPay',
                    'momo' => 'Momo',
                    'card' => 'Thẻ ngân hàng',
                ];
            @endphp
            <li>
                <strong>Phương thức thanh toán:</strong>
                <span style="font-weight: bold;">
                    {{ $paymentMap[$order->payment_method] ?? $order->payment_method }}
                </span>
            </li>
            @php
                $statusMap = [
                    'pending' => 'Chờ xác nhận',
                    'processing' => 'Đang xử lý',
                    'shipping' => 'Đang giao hàng',
                    'completed' => 'Hoàn thành',
                    'cancelled' => 'Đã hủy',
                ];
                $statusColor = [
                    'pending' => '#ff9800',      // cam
                    'processing' => '#2196f3',   // xanh dương
                    'shipping' => '#673ab7',     // tím
                    'completed' => '#4caf50',    // xanh lá
                    'cancelled' => '#f44336',    // đỏ
                ];
                $color = $statusColor[$order->status] ?? '#333';
            @endphp
            <li>
                <strong>Trạng thái:</strong>
                <span style="color: {{ $color }}; font-weight: bold; font-size: 1.1em;">
                    {{ $statusMap[$order->status] ?? $order->status }}
                </span>
            </li>
        </ul>

        <h4>Danh sách sản phẩm:</h4>
        <table style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="border:1px solid #ddd; padding:8px;">Sản phẩm</th>
                    <th style="border:1px solid #ddd; padding:8px;">Số lượng</th>
                    <th style="border:1px solid #ddd; padding:8px;">Giá</th>
                    <th style="border:1px solid #ddd; padding:8px;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td style="border:1px solid #ddd; padding:8px;">{{ $item->productVariant->product->name ?? 'Sản phẩm' }}</td>
                        <td style="border:1px solid #ddd; padding:8px;">{{ $item->quantity }}</td>
                        <td style="border:1px solid #ddd; padding:8px;">{{ number_format($item->price_at_time) }} VNĐ</td>
                        <td style="border:1px solid #ddd; padding:8px;">{{ number_format($item->total_price) }} VNĐ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="margin-top: 20px;"><strong>Tổng tiền:</strong> {{ number_format($order->total_money) }} VNĐ</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>
</html> 