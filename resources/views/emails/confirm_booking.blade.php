<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác nhận lịch hẹn</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Xác nhận lịch hẹn - Barber House</h2>

        <div style="padding: 30px;">
            <p style="font-size: 16px;">Xin chào <strong>{{ $appointment->name }}</strong>,</p>

            <p style="font-size: 16px;">Cảm ơn bạn đã đặt lịch tại <strong>Barber House</strong>. Vui lòng xác nhận lịch
                hẹn của bạn bằng cách nhấp vào nút bên dưới trước <strong>{{ $expirationTime }}</strong> (trong vòng 10 phút kể từ khi đặt):</p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $url }}"
                    style="background-color: #28a745; color: #ffffff; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-size: 16px;">
                    Xác nhận lịch hẹn
                </a>
            </div>

            <h3 style="color: #333;">📅 Chi tiết lịch hẹn</h3>
            <ul style="font-size: 16px; padding-left: 20px; line-height: 1.6;">
                <li><strong>Ngày:</strong> {{ $appointment->appointment_time->format('d/m/Y') }}</li>
                <li><strong>Giờ:</strong> {{ $appointment->appointment_time->format('H:i') }}</li>
                <li><strong>Dịch vụ chính:</strong> {{ $appointment->service->name }}</li>
                @if (!empty($additionalServices))
                    <li><strong>Dịch vụ bổ sung:</strong>
                        <ul>
                            @foreach ($additionalServices as $serviceName)
                                <li>{{ $serviceName }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endif
                <li><strong>Mã lịch hẹn:</strong> {{ $appointment->appointment_code }}</li>
            </ul>

            <p style="font-size: 14px; color: #777;">Nếu bạn không thực hiện lịch hẹn này hoặc không xác nhận trước {{ $expirationTime }}, lịch hẹn sẽ tự động bị hủy.</p>

            <p style="margin-top: 30px;">
                Trân trọng,<br>
                <strong>Đội ngũ Barber House</strong><br>
                <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
            </p>
        </div>
    </div>
</body>

</html>