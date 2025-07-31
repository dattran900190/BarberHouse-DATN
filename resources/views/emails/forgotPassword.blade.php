<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Mã OTP - Barber House</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background-color: #fff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">

       

        <h2 style="color: #007bff;"> Mã OTP đặt lại mật khẩu</h2>

        <p>Xin chào, {{ $name }}</p>
        <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>

        <p style="font-size: 18px;"> <strong>Mã OTP của bạn là: <span style="color: #000; font-size: 22px;">{{ $otp }}</span></strong></p>

        <p>Vui lòng sử dụng mã này để xác nhận yêu cầu trong <strong>{{ config('app.otp_expiration', 2) }} phút</strong>.</p>

        <p>Nếu bạn không yêu cầu điều này, vui lòng bỏ qua email hoặc liên hệ với chúng tôi để được hỗ trợ kịp thời.</p>

        <p style="font-size: 14px; color: #777;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>

</html>
