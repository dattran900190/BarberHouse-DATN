<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo không hoàn tất lịch hẹn - Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Lịch hẹn không được hoàn tất</h2>

        <p>Xin chào <strong>{{ $appointment->name }}</strong>,</p>

        <p>Chúng tôi nhận thấy bạn đã không đến cửa hàng vào thời gian hẹn <strong>{{ $appointment->appointment_time }}</strong> ngày <strong>{{ $appointment->appointment_date }}</strong> theo lịch đã đăng ký với mã <strong>{{ $appointment->appointment_code }}</strong>.</p>

        <p>Chúng tôi rất tiếc vì đã không thể phục vụ bạn như mong đợi. Nếu bạn gặp sự cố hoặc thay đổi kế hoạch đột xuất, bạn hoàn toàn có thể đặt lại lịch hẹn vào thời gian phù hợp hơn.</p>

        <p>Để đặt lại lịch nhanh chóng, bạn có thể truy cập trang đặt lịch hoặc liên hệ trực tiếp với chúng tôi để được hỗ trợ.</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Luôn sẵn sàng phục vụ bạn!</em>
        </p>
    </div>
</body>
</html>
