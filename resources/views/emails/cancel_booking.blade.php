<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hủy lịch hẹn {{ $appointment->appointment_code }} - Barber House</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; padding: 20px; color: #333;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h2 style="color: #cc0000;">Hủy lịch hẹn thành công</h2>
        <p>Xin chào <strong>{{ $appointment->name }}</strong>,</p>

        <p>Chúng tôi xác nhận rằng lịch hẹn có mã <strong>{{ $appointment->appointment_code }}</strong> của bạn tại <strong>Barber House</strong> đã được hủy thành công.</p>

        <p>Nếu bạn thực hiện việc hủy này do thay đổi kế hoạch, chúng tôi luôn sẵn sàng hỗ trợ bạn đặt lại lịch mới bất cứ lúc nào.</p>

        <p>Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ với đội ngũ chăm sóc khách hàng của chúng tôi.</p>

        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>
</html>
