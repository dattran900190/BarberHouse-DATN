<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đơn hàng hoàn thành - Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h2 style="color: #4caf50;">Đơn hàng của bạn đã hoàn thành!</h2>

        <p>Xin chào <strong>{{ $order->name }}</strong>,</p>

        <p>Chúng tôi xin thông báo đơn hàng với mã <strong>{{ $order->order_code }}</strong> của bạn tại <strong>Barber House</strong> đã được hoàn thành thành công.</p>

        <h4>Thông tin đơn hàng:</h4>
        <ul>
            <li><strong>Người nhận:</strong> {{ $order->name }}</li>
            <li><strong>Số điện thoại:</strong> {{ $order->phone }}</li>
            <li><strong>Địa chỉ:</strong> {{ $order->address }}</li>
            <li><strong>Phí vận chuyển:</strong> {{ number_format($order->shipping_fee) }} VNĐ</li>
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

        <p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi. Nếu có bất kỳ thắc mắc hoặc góp ý nào, vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>
</html> 