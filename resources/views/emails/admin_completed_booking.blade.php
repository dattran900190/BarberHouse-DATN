<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Lịch hẹn của bạn đã hoàn thành - Barber House</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f7f7f7; padding: 20px;">
    <div
        style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #007BFF;">Lịch hẹn của bạn đã hoàn thành!</h2>

        <p style="font-size: 16px;">Xin chào <strong>{{ $appointment->name }}</strong>,</p>

        <p>Chúng tôi xin thông báo lịch hẹn với mã <strong>{{ $appointment->appointment_code }}</strong> của bạn tại
            <strong>Barber House</strong> đã được hoàn thành thành công.</p>

        <h3 style="color: #333;">📅 Chi tiết lịch hẹn</h3>
        <ul style="font-size: 16px; padding-left: 20px; line-height: 1.6;">
            <li><strong>Mã lịch hẹn:</strong> {{ $appointment->appointment_code }}</li>
            <li><strong>Ngày hẹn:</strong>
                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y') }}</li>
            <li><strong>Giờ hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
            </li>
            <li><strong>Chi nhánh:</strong> {{ $appointment->branch->name ?? 'Không xác định' }}</li>
            <li><strong>Thợ cắt:</strong> {{ $appointment->barber->name ?? 'Không xác định' }}</li>
            <li><strong>Dịch vụ:</strong> {{ $appointment->service->name ?? 'Không xác định' }}</li>
             @if (!empty($additionalServices))
                    <li><strong>Dịch vụ bổ sung:</strong>
                        <ul>
                            @foreach ($additionalServices as $serviceName)
                                <li>{{ $serviceName }}</li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            @if (!empty($appointment->name))
                <li><strong>Người được phục vụ:</strong> {{ $appointment->name }}</li>
            @endif

        </ul>

        <p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của chúng tôi.
            Nếu có bất kỳ thắc mắc hoặc góp ý nào, vui lòng liên hệ với chúng tôi để được hỗ trợ.</p>

        <p>Chúng tôi rất mong nhận được đánh giá từ bạn để cải thiện dịch vụ ngày càng tốt hơn.</p>

        <!-- Đánh giá dịch vụ của bạn -->
        <p>Bạn có thể đánh giá dịch vụ của bạn tại đây:</p>
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ route('client.detailAppointmentHistory', $appointment->id) }}"
                target="_blank"
                style="display: inline-block; padding: 12px 20px; background-color: #007BFF; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: bold;">
                Đánh giá dịch vụ của bạn
             </a>
        </div>


        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Đội ngũ Barber House</strong><br>
            <em>Chất lượng – Tận tâm – Chuyên nghiệp</em>
        </p>
    </div>
</body>

</html>
