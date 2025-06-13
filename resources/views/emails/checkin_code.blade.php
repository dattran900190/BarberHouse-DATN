<!DOCTYPE html>
<html>
<head>
    <title>Mã Check-in của bạn</title>
</head>
<body>
    <h2>Xin chào {{ $appointment->user->name }},</h2>
    <p>Bạn đã đặt lịch hẹn thành công vào ngày <strong>{{ $appointment->date }}</strong> lúc <strong>{{ $appointment->time }}</strong>.</p>
    <p>Mã check-in của bạn là:</p>
    <h1 style="color: blue;">{{ $code }}</h1>
    <p>Vui lòng cung cấp mã này khi đến tiệm để xác nhận check-in.</p>
    <br>
    <p>Trân trọng,<br>Đội ngũ hỗ trợ</p>
</body>
</html>
