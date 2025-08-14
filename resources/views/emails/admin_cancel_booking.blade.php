<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hủy lịch hẹn {{ $appointment->appointment_code }} - Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Thông Báo Hủy Lịch Hẹn</h2>
        <p>Xin chào <strong>{{ $appointment->name }}</strong>,</p>

        <p>Chúng tôi rất tiếc phải thông báo rằng lịch hẹn có mã <strong>{{ $appointment->appointment_code }}</strong> của bạn tại <strong>Barber House</strong> đã bị hủy bởi quản trị viên.</p>

        <p><strong>Lý do hủy:</strong> {{ $appointment->cancellation_reason ?? 'Không có lý do cụ thể' }}</p>

        <p>Nếu bạn có bất kỳ thắc mắc nào hoặc muốn đặt lại lịch hẹn, vui lòng liên hệ với chúng tôi qua số điện thoại <strong><a href="tel:0123456789">0123 456 789</a></strong> hoặc email <strong><a href="mailto:trandiep490@gmail.com">trandiep490@gmail.com</a></strong>.</p>
        <p>Chúng tôi rất mong được phục vụ bạn trong tương lai!</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>
</html>