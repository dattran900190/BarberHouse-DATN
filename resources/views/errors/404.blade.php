<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang | BarberHouse</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                radial-gradient(circle at 40% 60%, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px, 30px 30px, 70px 70px;
            z-index: 0;
        }

        .container {
            text-align: center;
            position: relative;
            z-index: 1;
            max-width: 800px;
            padding: 20px;
        }

        /* Logo */
        .logo {
            margin-bottom: 30px;
            animation: fadeInDown 1s ease-out;
        }

        .logo img {
            height: 80px;
            filter: brightness(1.2);
        }

        .logo-text {
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 10px;
            position: relative;
        }

        .logo-text::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #c9a96e, #f4d03f);
            border-radius: 2px;
        }

        /* 404 Number */
        .error-number {
            font-size: 12rem;
            font-weight: 900;
            color: transparent;
            background: linear-gradient(45deg, #c9a96e, #f4d03f, #c9a96e);
            background-clip: text;
            -webkit-background-clip: text;
            text-shadow: 0 0 30px rgba(201, 169, 110, 0.5);
            margin: 20px 0;
            line-height: 1;
            animation: glow 2s ease-in-out infinite alternate;
            position: relative;
        }

        .error-number::before {
            content: '404';
            position: absolute;
            top: 0;
            left: 0;
            color: rgba(201, 169, 110, 0.1);
            z-index: -1;
            transform: translate(5px, 5px);
        }

        /* Error Messages */
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #f4d03f;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .error-message {
            font-size: 1.1rem;
            color: #ccc;
            margin-bottom: 10px;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .error-submessage {
            font-size: 1rem;
            color: #999;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-out 0.7s both;
        }

        .highlight {
            color: #c9a96e;
            font-weight: bold;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.3s ease;
        }

        .highlight:hover {
            color: #f4d03f;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease-out 0.9s both;
        }

        .btn {
            padding: 15px 30px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #c9a96e, #f4d03f);
            color: #1a1a1a;
            box-shadow: 0 8px 25px rgba(201, 169, 110, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(201, 169, 110, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: #c9a96e;
            border: 2px solid #c9a96e;
        }

        .btn-secondary:hover {
            background: #c9a96e;
            color: #1a1a1a;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(201, 169, 110, 0.3);
        }

        /* Decorative Elements */
        .scissors {
            position: absolute;
            font-size: 2rem;
            color: rgba(201, 169, 110, 0.3);
            animation: float 3s ease-in-out infinite;
        }

        .scissors-1 {
            top: 20%;
            left: 10%;
            animation-delay: -1s;
        }

        .scissors-2 {
            top: 60%;
            right: 15%;
            animation-delay: -2s;
        }

        .scissors-3 {
            bottom: 20%;
            left: 20%;
            animation-delay: -0.5s;
        }

        /* Animations */
        @keyframes glow {
            from {
                text-shadow: 0 0 30px rgba(201, 169, 110, 0.5);
            }

            to {
                text-shadow: 0 0 50px rgba(244, 208, 63, 0.8), 0 0 70px rgba(201, 169, 110, 0.6);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .logo-text {
                font-size: 2rem;
                letter-spacing: 2px;
            }

            .error-number {
                font-size: 8rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-message {
                font-size: 1rem;
            }

            .error-submessage {
                font-size: 0.9rem;
            }

            .button-group {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }

            .btn {
                width: 250px;
                padding: 12px 25px;
                font-size: 0.9rem;
            }

            .scissors {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .logo img {
                height: 60px;
            }

            .logo-text {
                font-size: 1.5rem;
                letter-spacing: 1px;
            }

            .error-number {
                font-size: 6rem;
                margin: 15px 0;
            }

            .error-title {
                font-size: 1.3rem;
                margin-bottom: 12px;
            }

            .error-message {
                font-size: 0.95rem;
                margin-bottom: 8px;
            }

            .error-submessage {
                font-size: 0.85rem;
                margin-bottom: 25px;
            }

            .btn {
                width: 220px;
                padding: 10px 20px;
                font-size: 0.85rem;
            }

            .scissors {
                display: none;
            }
        }

        @media (max-width: 320px) {
            .container {
                padding: 10px;
            }

            .error-number {
                font-size: 4.5rem;
            }

            .error-title {
                font-size: 1.1rem;
            }

            .btn {
                width: 200px;
                padding: 8px 15px;
                font-size: 0.8rem;
            }
        }

        /* High DPI Displays */
        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .logo img {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }
    </style>
</head>

<body>
    <!-- Decorative Scissors -->
    <div class="scissors scissors-1">✂️</div>
    <div class="scissors scissors-2">✂️</div>
    <div class="scissors scissors-3">✂️</div>

    <div class="container">
        <!-- Logo Section -->
        <div class="logo">
            <div class="logo-text">BarberHouse</div>
        </div>

        <!-- Error Number -->
        <div class="error-number">404</div>

        <!-- Error Messages -->
        <h1 class="error-title">Oops! Trang không tồn tại</h1>
        <p class="error-message">Chúng tôi không thể tìm thấy trang mà bạn đang tìm kiếm!</p>
        <p class="error-submessage">
            Bạn có thể <span class="highlight" onclick="goBack()">quay lại</span> hoặc trang web sẽ tự động chuyển hướng
            sau
            <span id="countdown">5</span> giây.
        </p>

        <!-- Action Buttons -->
        <div class="button-group">
            <a href="/" class="btn btn-primary">Về Trang Chủ</a>
            <a href="/booking" class="btn btn-secondary">Đặt Lịch Cắt Tóc</a>
        </div>
    </div>

    <script>
        // Auto redirect countdown
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = '/';
            }
        }, 1000);

        // Go back function
        function goBack() {
            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = '/';
            }
        }

        // Add some interactive effects
        document.addEventListener('mousemove', (e) => {
            const scissors = document.querySelectorAll('.scissors');
            const mouseX = e.clientX;
            const mouseY = e.clientY;

            scissors.forEach((scissor, index) => {
                const speed = (index + 1) * 0.0005;
                const x = (mouseX * speed);
                const y = (mouseY * speed);

                scissor.style.transform = `translate(${x}px, ${y}px) rotate(${x * 0.1}deg)`;
            });
        });

        // Add click effect to logo
        document.querySelector('.logo-text').addEventListener('click', () => {
            window.location.href = '/';
        });

        // Prevent right click on decorative elements
        document.querySelectorAll('.scissors').forEach(scissors => {
            scissors.addEventListener('contextmenu', (e) => {
                e.preventDefault();
            });
        });
    </script>
</body>

</html>
