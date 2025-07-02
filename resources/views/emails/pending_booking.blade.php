<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đặt lịch tại Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h2 style="color: #007BFF;">Cảm ơn bạn đã đặt lịch tại Barber House</h2>

        <p>Xin chào <strong>{{ $appointment->name }}</strong>,</p>

        <p>Chúng tôi đã nhận được yêu cầu đặt lịch của bạn với mã <strong>{{ $appointment->appointment_code }}</strong>. Hiện tại, lịch hẹn của bạn đang được xem xét và sẽ được xác nhận trong thời gian sớm nhất.</p>

        <p>Bạn sẽ nhận được email hoặc thông báo từ chúng tôi ngay khi lịch hẹn được phê duyệt.</p>

        <p>Trong thời gian chờ đợi, nếu bạn có bất kỳ thắc mắc hoặc muốn điều chỉnh thông tin lịch hẹn, vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>
</html>
