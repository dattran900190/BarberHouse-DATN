<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng ký Barber House</title>
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
                <img src="{{ asset('storage/' . ($imageSettings['anh_dang_ky'] ?? 'default-images/no-image.png')) }}" alt="Đăng ký" />
            </div>

            <div class="form-login">
                <div class="image">
                    <img src="{{ asset('images/black_logo.png') }}" alt="Barber House Logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <h2 style="display: none; color: #000; font-weight: bold; font-size: 2rem; margin: 0;">BARBER HOUSE</h2>
                </div>

                <div class="form">
                    <h3>Đăng ký</h3>

                    @if (session('success'))
                        <small class="form_message" style="color: green">{{ session('success') }}</small>
                    @endif
                    @if (session('messageError'))
                        <small class="form_message text-danger">{{ session('messageError') }}</small>
                    @endif

                    <form action="{{ route('postRegister') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control input-field" id="name" name="name"
                                placeholder="Nhập họ tên" value="{{ old('name') }}">
                            @error('name')
                                <small class="form_message text-danger" id="error-name"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control input-field" id="email" name="email"
                                placeholder="Nhập email" value="{{ old('email') }}">
                            @error('email')
                                <small class="form_message text-danger" id="error-email"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control input-field" id="phone" name="phone"
                                placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                            @error('phone')
                                <small class="form_message text-danger" id="error-phone"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control input-field" id="password" name="password"
                                    placeholder="Nhập mật khẩu">
                                <span class="toggle-password" onclick="togglePassword('password', 'eye-icon1')"
                                    style="position:absolute; right:10px; top: 50%; transform: translateY(-50%); cursor:pointer;">
                                    <i class="fa-solid fa-eye" id="eye-icon1"></i>
                                </span>
                            </div>
                            @error('password')
                                <small class="form_message text-danger" id="error-password"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control input-field" id="password_confirmation"
                                    name="password_confirmation" placeholder="Nhập lại mật khẩu">
                                <span class="toggle-password"
                                    onclick="togglePassword('password_confirmation', 'eye-icon2')"
                                    style="position:absolute; right:10px; top: 50%; transform: translateY(-50%); cursor:pointer;">
                                    <i class="fa-solid fa-eye" id="eye-icon2"></i>
                                </span>
                            </div>
                            @error('password_confirmation')
                                <small class="form_message text-danger" id="error-password_confirmation"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-dark w-100">Đăng ký</button>

                        <p class="mt-3">Bạn đã có tài khoản? <a href="{{ asset('dang-nhap') }}"  style="text-decoration: none;"
                                onmouseover="this.style.textDecoration='underline'"
                                onmouseout="this.style.textDecoration='none'">Đăng nhập</a></p>
                    </form>
                </div>
            </div>
        </div>
        <a href="{{ asset('/') }}" class="back-button">&#8592;</a>
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
            padding: 25px;
            background: #fff;
            position: relative;
        }

        .form-login .image {
            text-align: center;
            margin-bottom: 15px;
        }

        .form-login .image img {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .form-login h3 {
            margin-bottom: 15px;
            color: #000;
            font-size: 1.6rem;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #000;
            margin-bottom: 5px;
            font-size: 0.85rem;
        }

        .input-field {
            border-radius: 8px;
            border: 2px solid #e1e5e9;
            padding: 8px 10px;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #000;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
            outline: none;
        }

        .toggle-password {
            color: #6c757d;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #000;
        }

        .btn-dark {
            background: #000;
            border: none;
            border-radius: 8px;
            padding: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-dark:hover {
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
            z-index: 1000;
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
                background: #f8f7f7;
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
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                background: #fff;
                position: relative;
            }

            .form-login .image {
                text-align: center;
                margin-bottom: 30px;
            }

            .form-login .image img {
                max-width: 200px;
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
                margin-bottom: 25px;
                color: #000;
                font-size: 1.8rem;
                font-weight: 600;
            }

            .form-label {
                font-weight: 600;
                color: #000;
                margin-bottom: 10px;
                font-size: 0.95rem;
            }

            .input-field {
                border-radius: 8px;
                border: 1px solid #e1e5e9;
                padding: 12px 15px;
                font-size: 16px;
                transition: all 0.3s ease;
                background: #f8f9fa;
                width: 100%;
                box-sizing: border-box;
            }

            .input-field:focus {
                border-color: #007bff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
                background: #fff;
                outline: none;
            }

            .btn-dark {
                background: #000;
                border: none;
                border-radius: 8px;
                padding: 12px;
                font-size: 16px;
                font-weight: 600;
                transition: all 0.3s ease;
                margin-top: 15px;
                width: 100%;
            }

            .btn-dark:hover {
                background: #333;
                transform: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            .back-button {
                position: fixed;
                top: 20px;
                left: 20px;
                background: #000;
                border-radius: 10px;
                width: 40px;
                height: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #fff;
                font-size: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
                z-index: 1000;
                border: none;
            }

            .back-button:hover {
                background: #333;
                transform: none;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            /* Cải thiện link đăng nhập */
            .mb-3 a {
                color: #000;
                font-weight: 500;
                transition: color 0.3s ease;
            }

            .mb-3 a:hover {
                color: #333;
            }

            /* Cải thiện thông báo lỗi */
            .form_message {
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
                border-color: #000;
                box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
            }

            .btn-dark {
                background: #000;
            }

            .btn-dark:hover {
                background: #333;
            }
        }

        /* Responsive cho màn hình nhỏ */
        @media (max-width: 480px) {
            .login {
                padding: 15px;
            }

            .form-login {
                padding: 25px 20px;
                max-width: 100%;
            }

            .form-login .image img {
                max-width: 180px;
            }

            .form-login h3 {
                font-size: 1.6rem;
                margin-bottom: 20px;
            }

            .input-field {
                padding: 12px 15px;
                font-size: 16px;
            }

            .btn-dark {
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
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Ẩn lỗi khi người dùng nhập lại
        document.querySelectorAll('.input-field').forEach(function(input) {
            input.addEventListener('input', function() {
                const errorId = 'error-' + input.id;
                const error = document.getElementById(errorId);
                if (error) {
                    error.style.display = 'none';
                }
            });
        });

        // Ẩn lỗi khi người dùng chọn lại giới tính
        document.getElementById('gender').addEventListener('change', function() {
            const error = document.getElementById('error-gender');
            if (error) {
                error.style.display = 'none';
            }
        });
    </script>
</body>

</html>
