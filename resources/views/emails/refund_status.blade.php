<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Thông báo trạng thái yêu cầu hoàn tiền</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd;">
        @if ($status === 'refunded')
            <h2 style="color: #28a745;">Yêu cầu hoàn tiền đã được duyệt</h2>
            <p>Kính chào {{ $user->name ?? 'Khách hàng' }},</p>
            <p>Chúng tôi xin thông báo rằng yêu cầu hoàn tiền của bạn đã được xử lý thành công. Dưới đây là chi tiết:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Mã đơn hàng:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $order->order_code ?? ($appointment->appointment_code ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Số tiền hoàn:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Ngân hàng:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $refund->bank_name }} - {{ $refund->bank_account_number }} ({{ $refund->bank_account_name }})</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Ngày hoàn tiền:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $refund->refunded_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        @else
            <h2 style="color: #dc3545;">Yêu cầu hoàn tiền bị từ chối</h2>
            <p>Kính chào {{ $user->name ?? 'Khách hàng' }},</p>
            <p>Chúng tôi rất tiếc phải thông báo rằng yêu cầu hoàn tiền của bạn đã bị từ chối. Dưới đây là chi tiết:</p>
            
            <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Mã đơn hàng:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $order->order_code ?? ($appointment->appointment_code ?? 'N/A') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Số tiền yêu cầu:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><strong>Lý do yêu cầu:</strong></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">{{ $refund->reason }}</td>
                </tr>
                @if ($reject_reason)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Lý do từ chối:</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{{ $reject_reason }}</td>
                    </tr>
                @endif
            </table>
        @endif

        <p>Nếu bạn có bất kỳ câu hỏi nào hoặc cần thêm thông tin, vui lòng liên hệ với chúng tôi qua email hoặc hotline.</p>
        <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
    </div>
</body>
</html>