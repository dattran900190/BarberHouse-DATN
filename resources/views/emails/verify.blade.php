<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác thực tài khoản</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Xác nhận tài khoản - Barber House</h2>

        <div style="padding: 30px;">
            <p style="font-size: 16px;">Xin chào,</p>
            <p>Vui lòng click vào link dưới đây để xác thực tài khoản:</p>
            <a href="{{ route('verify.email', $token) }}" style="background-color: #28a745; color: #ffffff; padding: 12px 24px; border-radius: 5px; text-decoration: none; font-size: 16px;">Xác thực ngay</a>
            <p style="margin-top: 30px;">
                Trân trọng,<br>
                <strong>Đội ngũ Barber House</strong><br>
                <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
            </p>
        </div>
    </div>
</body>

</html>
