<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận lịch hẹn thành công - BarberHouse</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #6b7280, #1e3a8a);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 2rem;
            overflow-y: auto;
        }

        .background-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .content-wrapper {
            text-align: center;
            max-width: 550px;
            width: 90%;
            animation: fadeInUp 0.9s ease-out;
        }

        .success-icon {
            font-size: 5.5rem;
            color: #34d399;
            margin-bottom: 1.5rem;
            animation: bounceIn 1s ease-out;
        }

        .title {
            color: #ffffff;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .message {
            color: #e2e8f0;
            font-size: 1.15rem;
            line-height: 1.7;
            margin-bottom: 1.5rem;
            background: rgba(0, 0, 0, 0.2);
            padding: 1rem;
            border-radius: 10px;
        }

        .appointment-details {
            background: rgba(0, 0, 0, 0.3);
            padding: 1.5rem;
            border-radius: 15px;
            margin-top: 1.5rem;
            border: 1px solid rgba(52, 211, 153, 0.3);
        }

        .appointment-details .detail-grid {
            text-align: left;
        }

        .appointment-details .detail-item {
            /* display: flex; */
            /* justify-content: space-between; */
            margin-bottom: 0.75rem;
        }

        .appointment-details .detail-label {
            color: #34d399;
            font-weight: 600;
            min-width: 120px; /* Độ rộng cố định cho nhãn */
        }

        .appointment-details .detail-value {
            color: #e2e8f0;
            text-align: right;
            flex-grow: 1;
            word-wrap: break-word;
        }

        .additional-services {
            margin-top: 0.5rem;
            padding-left: 1.5rem;
        }

        .additional-services-item {
            color: #e2e8f0;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .total-amount {
            color: #fbbf24;
            font-weight: 700;
            font-size: 1.1em;
            margin-top: 0.5rem;
            text-align: right;
        }

        .error-message {
            text-align: center;
            padding: 2rem;
        }

        .error-message h2 {
            margin-bottom: 1rem;
            color: #fbbf24;
        }

        .error-message p {
            margin-bottom: 1.5rem;
            color: #e2e8f0;
        }

        .footer-info {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 2rem;
        }

        .footer-text {
            color: #e2e8f0;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .footer-text strong {
            font-size: 1.1rem;
            color: #ffffff;
            letter-spacing: 1px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-icon {
            color: #ffffff;
            font-size: 1.3rem;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            color: #34d399;
            transform: translateY(-3px);
        }

        .logo {
            position: fixed;
            top: 1rem;
            left: 1.5rem;
            z-index: 10;
        }

        .logo-img {
            height: 70px;
            width: auto;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .logo-img:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.1); }
            70% { transform: scale(0.95); }
            100% { opacity: 1; transform: scale(1); }
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(40px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1.5rem;
            }

            .title {
                font-size: 2rem;
            }

            .message {
                font-size: 1rem;
            }

            .success-icon {
                font-size: 4.5rem;
            }

            .social-links {
                gap: 0.75rem;
            }

            .social-icon {
                font-size: 1.1rem;
            }

            .logo-img {
                height: 50px;
            }

            .appointment-details .detail-label {
                min-width: 100px;
            }
        }

        @media (max-width: 480px) {
            .content-wrapper {
                padding: 1rem;
            }

            .title {
                font-size: 1.7rem;
            }

            .success-icon {
                font-size: 4rem;
            }

            .logo-img {
                height: 40px;
            }

            .footer-text {
                font-size: 0.85rem;
            }

            .appointment-details .detail-label {
                min-width: 80px;
            }
        }
    </style>
</head>

<body>
    <div class="background-particles"></div>
    <div class="logo">
        <img src="{{ asset('storage/' . ($imageSettings['black_logo'] ?? 'default-images/black_logo.png')) }}" alt="BarberHouse Logo" class="logo-img">
    </div>

    <div class="content-wrapper">
        @if (!$appointment)
            <div class="error-message">
                <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #fbbf24; margin-bottom: 1rem;"></i>
                <h2>Không tìm thấy thông tin lịch hẹn</h2>
                <p>Vui lòng quay lại trang chủ hoặc liên hệ với chúng tôi để được hỗ trợ.</p>
                <a href="{{ route('home') }}" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-home me-2"></i>
                    Về trang chủ
                </a>
            </div>
        @else
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1 class="title">Xác nhận thành công!</h1>

            <p class="message">
                Lịch hẹn của bạn đã được xác nhận thành công.
                Chúng tôi sẽ liên hệ với bạn sớm nhất để xác nhận chi tiết.
                Cảm ơn bạn đã tin tưởng BarberHouse! Theo dõi chúng tôi trên Instagram để nhận ưu đãi đặc biệt.
            </p>

            <div class="appointment-details">
                <div class="detail-grid">
                    <div class="detail-item"><span class="detail-label">Họ tên: </span><span class="detail-value">{{ $appointment->name ?? 'N/A' }}</span></div>
                    <div class="detail-item"><span class="detail-label">Thời gian: </span><span class="detail-value">
                        @if ($appointment->appointment_time)
                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i, d/m/Y') }}
                        @else
                            N/A
                        @endif
                    </span></div>
                    <div class="detail-item"><span class="detail-label">Dịch vụ: </span><span class="detail-value">
                        @if ($appointment->service)
                            {{ $appointment->service->name }}
                        @else
                            N/A
                        @endif
                    </span></div>
                    @if (!empty($additionalServices))
                        <div class="detail-item"><span class="detail-label">Dịch vụ bổ sung: </span>
                            <div class="additional-services">
                                @foreach ($additionalServices as $serviceName)
                                    <div class="additional-services-item">• {{ $serviceName }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if ($appointment->total_amount)
                        <div class="detail-item"><span class="detail-label">Tổng tiền: </span><span class="detail-value"><span class="total-amount">{{ number_format($appointment->total_amount) }} VNĐ</span></span></div>
                    @endif
                    <div class="detail-item"><span class="detail-label">Thợ cắt tóc: </span><span class="detail-value">
                        @if ($appointment->barber)
                            {{ $appointment->barber->name }}
                        @else
                            N/A
                        @endif
                    </span></div>
                    <div class="detail-item"><span class="detail-label">Chi nhánh: </span><span class="detail-value">
                        @if ($appointment->branch)
                            {{ $appointment->branch->name }}
                        @else
                            N/A
                        @endif
                    </span></div>
                    <div class="detail-item"><span class="detail-label">Địa chỉ: </span><span class="detail-value">
                        @if ($appointment->branch && $appointment->branch->address)
                            {{ $appointment->branch->address }}
                        @else
                            N/A
                        @endif
                    </span></div>
                    <div class="detail-item"><span class="detail-label">Số điện thoại: </span><span class="detail-value">{{ $appointment->phone ?? 'N/A' }}</span></div>
                    <div class="detail-item"><span class="detail-label">Email: </span><span class="detail-value">{{ $appointment->email ?? 'N/A' }}</span></div>
                    @if ($appointment->notes)
                        <div class="detail-item"><span class="detail-label">Ghi chú: </span><span class="detail-value">{{ $appointment->notes }}</span></div>
                    @endif
                </div>
            </div>

            <div class="footer-info">
                <p class="footer-text">
                    <strong>BarberHouse</strong><br>
                    @if ($appointment->branch)
                        {{ $appointment->branch->address ?? 'N/A' }}<br>
                        @if ($appointment->branch->phone)
                            Hotline: {{ $appointment->branch->phone }} |
                        @else
                            Hotline: 090xxx xxxx |
                        @endif
                        Giờ mở cửa: 08:00 - 19:30
                    @else
                        123 Đường ABC, Quận 1, TP.HCM<br>
                        Hotline: 090xxx xxxx | Giờ mở cửa: 08:00 - 19:30
                    @endif
                </p>
                <div class="social-links">
                    @foreach ($social_links as $key => $url)
                        <a href="{{ $url }}" target="_blank" class="social-icon">
                            @if (str_contains($key, 'facebook'))
                                <i class="fa-brands fa-facebook"></i>
                            @elseif (str_contains($key, 'instagram'))
                                <i class="fa-brands fa-instagram"></i>
                            @elseif (str_contains($key, 'youtube'))
                                <i class="fa-brands fa-youtube"></i>
                            @elseif (str_contains($key, 'tiktok'))
                                <i class="fa-brands fa-tiktok"></i>
                            @else
                                <i class="fa-solid fa-link"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        particlesJS('background-particles', {
            particles: {
                number: { value: window.innerWidth < 768 ? 20 : 50, density: { enable: true, value_area: 800 } },
                color: { value: '#ffffff' },
                opacity: { value: 0.3, random: true },
                size: { value: 3, random: true },
                move: { enable: true, speed: 1, direction: 'none', random: true }
            }
        });
    </script>
</body>
</html>