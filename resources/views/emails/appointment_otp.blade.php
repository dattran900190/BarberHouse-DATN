<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Mã OTP xác nhận đặt lịch</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Xác nhận lịch hẹn - Barber House</h2>

        <div style="padding: 30px;">
            <p style="font-size: 16px;">Xin chào <strong>{{ $appointment['name'] ?? '' }}</strong>,</p>

            <p style="font-size: 16px;">Cảm ơn bạn đã đặt lịch tại <strong>Barber House</strong>
            <div style="text-align: center; margin: 30px 0;">
                <p>Mã OTP của bạn để xác nhận đặt lịch là:</p>
                <h2 style="color: #2c3e50;">{{ $otp }}</h2>
                <p>Mã này có hiệu lực trong vòng 2 phút.</p>
                @if (!empty($appointment))
                    <p>Dịch vụ: {{ $appointment['service_name'] ?? '' }}</p>
                    <p>Ngày giờ: {{ $appointment['appointment_date'] ?? '' }} lúc
                        {{ $appointment['appointment_time'] ?? '' }}</p>
                @endif
            </div>

            <p style="font-size: 14px; color: #777;">Nếu bạn không thực hiện lịch hẹn này hoặc không xác nhận lịch hẹn
                sẽ tự động bị hủy.</p>

            <p style="margin-top: 30px;">
                Trân trọng,<br>
                <strong>Đội ngũ Barber House</strong><br>
                <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
            </p>
        </div>
    </div>
</body>

</html>
