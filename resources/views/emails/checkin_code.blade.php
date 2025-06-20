<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mã Check-in của bạn</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #2c3e50;">Xin chào {{ $appointment->user->name }},</h2>

        <p>
            Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi. Dưới đây là thông tin chi tiết về lịch hẹn của bạn:
        </p>

        <ul style="list-style: none; padding: 0;">
            <li><strong>🗓 Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}</li>
            <li><strong>⏰ Giờ hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}</li>
            <li><strong>🏠 Chi nhánh:</strong> {{ $appointment->branch->name ?? 'Không xác định' }}</li>
            <li><strong>💇‍♂️ Thợ cắt:</strong> {{ $appointment->barber->name ?? 'Không xác định' }}</li>
            <li><strong>🛠 Dịch vụ:</strong> {{ $appointment->service->name ?? 'Không xác định' }}</li>
            @if (!empty($appointment->name))
                <li><strong>🙍‍♀️ Người được phục vụ:</strong> {{ $appointment->name }} (Đặt hộ)</li>
            @endif
           
        </ul>

        <p style="margin-top: 20px;">
            <strong>Mã check-in của bạn là:</strong>
        </p>

        <h1 style="text-align: center; color: #007bff; font-size: 36px;">{{ $code }}</h1>

        <p style="text-align: center; font-style: italic; color: #888;">
            Vui lòng cung cấp mã này khi đến tiệm để xác nhận check-in.
        </p>

        <hr style="margin: 30px 0;">

        <p style="color: #555;">
            Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline được cung cấp trên trang web.
        </p>

        <p style="margin-top: 40px;">
            Trân trọng,<br>
            <strong>Đội ngũ hỗ trợ BarberHouse</strong>
        </p>
    </div>
</body>
</html>
