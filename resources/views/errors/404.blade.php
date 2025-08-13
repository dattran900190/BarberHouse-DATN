<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Trang không tìm thấy | Barber House</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .scissors {
            position: absolute;
            color: rgba(255, 255, 255, 0.05);
            font-size: 2rem;
            animation: float 6s ease-in-out infinite;
        }

        .scissors:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .scissors:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: 2s;
        }

        .scissors:nth-child(3) {
            bottom: 15%;
            left: 20%;
            animation-delay: 4s;
        }

        .scissors:nth-child(4) {
            bottom: 25%;
            right: 10%;
            animation-delay: 1s;
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

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 30px;
            padding: 4rem 3rem;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 95%;
            min-height: 80vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: slideUp 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 280px;
            height: auto;
            margin-bottom: 2rem;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.15));
        }

        .error-icon {
            font-size: 5rem;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.15);
            letter-spacing: -0.02em;
        }

        .error-title {
            font-size: 2.2rem;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .error-message {
            font-size: 1.3rem;
            color: #718096;
            margin-bottom: 3rem;
            line-height: 1.8;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-container {
            display: flex;
            flex-direction: row;
            gap: 1.5rem;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 16px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 220px;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2c2c2c, #1a1a1a);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(44, 44, 44, 0.4);
        }

        .btn-secondary {
            background: #f7fafc;
            color: #4a5568;
            border: 2px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #edf2f7;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .barber-tools {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
            opacity: 0.6;
        }

        .tool {
            font-size: 2rem;
            color: #2c2c2c;
            animation: wiggle 3s ease-in-out infinite;
        }

        .tool:nth-child(2) {
            animation-delay: 1s;
        }

        .tool:nth-child(3) {
            animation-delay: 2s;
        }

        @keyframes wiggle {

            0%,
            100% {
                transform: rotate(0deg);
            }

            25% {
                transform: rotate(5deg);
            }

            75% {
                transform: rotate(-5deg);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 3rem 2rem;
                margin: 1rem;
                width: calc(100% - 2rem);
                min-height: 85vh;
                border-radius: 20px;
            }

            .logo {
                width: 220px;
            }

            .error-code {
                font-size: 5rem;
            }

            .error-title {
                font-size: 1.8rem;
            }

            .error-message {
                font-size: 1.1rem;
                margin-bottom: 2.5rem;
            }

            .btn-container {
                flex-direction: column;
                gap: 1rem;
            }

            .btn {
                min-width: 100%;
                padding: 14px 30px;
            }

            .barber-tools {
                margin-top: 2rem;
                gap: 1.5rem;
            }

            .tool {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 2rem 1.5rem;
                min-height: 90vh;
            }

            .logo {
                width: 180px;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-message {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="animated-bg">
        <div class="scissors"><i class="fas fa-cut"></i></div>
        <div class="scissors"><i class="fas fa-cut"></i></div>
        <div class="scissors"><i class="fas fa-cut"></i></div>
        <div class="scissors"><i class="fas fa-cut"></i></div>
    </div>

    <div class="container">
        <!-- Logo Barber House -->
        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjgwIiB2aWV3Qm94PSIwIDAgMjAwIDgwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iODAiIGZpbGw9IiMyYzJjMmMiLz48dGV4dCB4PSIzMCIgeT0iMzAiIGZpbGw9IndoaXRlIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMjIiIGZvbnQtd2VpZ2h0PSJib2xkIj5iYXJiZXI8L3RleHQ+PHRleHQgeD0iMzAiIHk9IjU1IiBmaWxsPSJ3aGl0ZSIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjIyIiBmb250LXdlaWdodD0iYm9sZCI+aG91c2U8L3RleHQ+PHBhdGggZD0iTTE1IDIwaDEwdjE1SDE1eiIgZmlsbD0id2hpdGUiLz48Y2lyY2xlIGN4PSIyMCIgY3k9IjEyIiByPSIzIiBmaWxsPSJ3aGl0ZSIvPjxwYXRoIGQ9Im0xNyAxOGwtMyAzIDMgMyIgc3Ryb2tlPSJ3aGl0ZSIgc3Ryb2tlLXdpZHRoPSIyIiBmaWxsPSJub25lIi8+PC9zdmc+"
            alt="Barber House Logo" class="logo">

        <div class="error-icon">
            <i class="fas fa-cut"></i>
        </div>

        <div class="error-code">404</div>

        <h1 class="error-title">Oops! Trang không tìm thấy</h1>

        <p class="error-message">
            Có vẻ như bạn đã đi nhầm đường rồi! Trang bạn tìm kiếm có thể đã được di chuyển,
            xóa hoặc không tồn tại. Đừng lo lắng, chúng tôi sẽ giúp bạn tìm đúng hướng.
        </p>

        <div class="btn-container">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Về trang chủ
            </a>

            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Quay lại trang trước
            </a>
        </div>

        <div class="barber-tools">
            <span class="tool"><i class="fas fa-cut"></i></span>
            <span class="tool"><i class="fas fa-razor"></i></span>
            <span class="tool"><i class="fas fa-cut"></i></span>
        </div>
    </div>

    <script>
        // Thêm hiệu ứng tương tác
        document.addEventListener('mousemove', function(e) {
            const container = document.querySelector('.container');
            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            const rotateX = (y / rect.height) * 10;
            const rotateY = -(x / rect.width) * 10;

            container.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });

        document.addEventListener('mouseleave', function() {
            const container = document.querySelector('.container');
            container.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        });

        // Auto redirect after 10 seconds (optional)
        setTimeout(function() {
            const autoRedirect = confirm('Bạn có muốn tự động chuyển về trang chủ không?');
            if (autoRedirect) {
                window.location.href = '{{ url('/') }}';
            }
        }, 100000);
    </script>
</body>

</html>
