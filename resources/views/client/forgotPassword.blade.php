<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="{{ asset('css/client.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

     <link rel="icon" href="{{ asset('images/favicon_logo.png') }}" type="image/png" />
</head>

<body>

    <main>

        <div class="login">
            <div class="image-login">
                <img src="{{ asset('storage/' . ($imageSettings['anh_dang_nhap'] ?? 'default-images/no-banggia.png')) }}"
                    alt="Ảnh đăng nhập" />
            </div>

            <div class="form-login">
                <div class="image">
                    <img src="{{ asset('images/black_logo.png') }}" alt="Barber House Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <h2 style="display: none; color: #000; font-weight: bold; font-size: 2rem; margin: 0;">BARBER HOUSE</h2>
                </div>

                <div class="form">
                    <h3>Quên mật khẩu</h3>
                    <form method="POST" action="{{ route('password.sendOtp') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="email" id="email" class="form-control input-field" placeholder="Nhập email" value="{{ old('email') }}">
                            @error('email')
                                <small class="text-danger" id="error-email" style="display: block">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi mã OTP</button>
                    </form>
                </div>
            </div>

            <a href="{{ asset('/') }}" class="back-button">
                &#8592;
            </a>
        </div>
    </main>

    <style>
        /* CSS cơ bản cho layout */
        .login {
            display: flex;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .image-login {
            flex: 8;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .image-login img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-login {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            background: #fff;
        }



        .form-login .image {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-login .image img {
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .form-login h3 {
            margin-bottom: 30px;
            color: #000;
            font-size: 2rem;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #000;
            margin-bottom: 8px;
        }

        .input-field {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 12px 15px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }

        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
            outline: none;
        }

        .btn-primary {
            background: #000;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #000;
            border-radius: 12px;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #fff;
            font-size: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: none;
        }

        .back-button:hover {
            background: #333;
            transform: scale(1.05);
            text-decoration: none;
            color: #fff;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        /* Fallback nếu logo không load được */
        .form-login .image img:not([src*="black_logo.png"]) {
            content: url("{{ asset('images/black_logo.png') }}");
        }

        /* Styling cho fallback text */
        .form-login .image h2 {
            text-align: center;
            color: #000;
            font-weight: bold;
            font-size: 2rem;
            margin: 0;
            letter-spacing: 2px;
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .login {
                flex-direction: column;
                min-height: 100vh;
                padding: 20px;
                background: #f5f5f5;
            }

            .image-login {
                display: none !important;
            }

            .form-login {
                width: 100%;
                max-width: 400px;
                margin: 0 auto;
                padding: 40px 25px;
                border-radius: 20px;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
                background: #fff;
                position: relative;
            }





            .form-login .image {
                text-align: center;
                margin-bottom: 35px;
            }

            .form-login .image img {
                max-width: 500px;
                height: auto;
                filter: none;
                display: block;
                margin: 0 auto;
            }

            .form-login .image h2 {
                font-size: 1.8rem;
                letter-spacing: 1px;
            }

            .form-login h3 {
                text-align: center;
                margin-bottom: 30px;
                color: #000;
                font-size: 2rem;
                font-weight: 700;
            }

            .form-label {
                font-weight: 600;
                color: #000;
                margin-bottom: 10px;
                font-size: 0.95rem;
            }

            .input-field {
                border-radius: 12px;
                border: 1px solid #d1d5db;
                padding: 15px 18px;
                font-size: 16px;
                transition: all 0.3s ease;
                background: #fff;
            }

            .input-field:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
                background: #fff;
                transform: translateY(-1px);
            }

            .btn-primary {
                background: #000;
                border: none;
                border-radius: 12px;
                padding: 15px;
                font-size: 16px;
                font-weight: 600;
                transition: all 0.3s ease;
                margin-top: 10px;
            }

            .btn-primary:hover {
                background: #333;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            }

            .btn-primary:active {
                transform: translateY(0);
            }

            .back-button {
                position: fixed;
                top: 20px;
                left: 20px;
                background: #000;
                border-radius: 12px;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #fff;
                font-size: 18px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
                z-index: 1000;
                border: none;
            }

            .back-button:hover {
                background: #333;
                transform: scale(1.05);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            }

            /* Cải thiện thông báo lỗi */
            .text-danger {
                font-size: 0.9rem;
                margin-top: 5px;
                padding: 8px 12px;
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                border-radius: 8px;
                color: #721c24;
            }
        }

        /* Responsive cho tablet */
        @media (min-width: 769px) and (max-width: 1024px) {
            .login {
                padding: 20px;
            }

            .form-login {
                padding: 40px 30px;
            }

            .image-login img {
                max-width: 100%;
                height: auto;
            }

            .form-login .image img {
                max-width: 200px;
            }

            .form-login h3 {
                font-size: 1.8rem;
                color: #000;
            }

            .form-label {
                color: #000;
            }

            .input-field:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15);
            }

            .btn-primary {
                background: #000;
            }

            .btn-primary:hover {
                background: #333;
            }
        }

        /* Responsive cho màn hình nhỏ */
        @media (max-width: 480px) {
            .login {
                padding: 15px;
            }

            .form-login {
                padding: 30px 20px;
                max-width: 100%;
            }



            .form-login .image img {
                max-width: 200px;
            }

            .form-login h3 {
                font-size: 1.6rem;
                margin-bottom: 25px;
            }

            .input-field {
                padding: 12px 15px;
                font-size: 16px;
            }

            .btn-primary {
                padding: 12px;
                font-size: 16px;
            }

            .back-button {
                width: 40px;
                height: 40px;
                font-size: 16px;
                background: #000;
                border-radius: 10px;
                color: #fff;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script>
        // Show/hide password
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const eyeIcon = document.getElementById("eye-icon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
            }
        }

        // Ẩn lỗi khi người dùng bắt đầu gõ lại
        document.querySelectorAll('.input-field').forEach(function(input) {
            input.addEventListener('input', function() {
                const errorId = 'error-' + input.id;
                const error = document.getElementById(errorId);
                if (error) {
                    error.style.display = 'none';
                }
            });
        });
    </script>
</body>

</html>
