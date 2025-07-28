<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu - Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h2 style="color: #cc0000;">Yêu cầu đặt lại mật khẩu</h2>
        <p>Xin chào <strong>{{ $user->name }}</strong>,</p>

        <p>Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</p>
        <p>Mật khẩu mới của bạn là: <strong>{{ $newPassword }}</strong></p>
        <p>Vui lòng đăng nhập và thay đổi mật khẩu của bạn ngay lập tức.</p>

        <p>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>

        <p>Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ với đội ngũ chăm sóc khách hàng của chúng tôi.</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>
</html>
