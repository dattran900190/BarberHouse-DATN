<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập Barber House</title>
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
                    <img src="{{ asset('storage/' . ($imageSettings['black_logo'] ?? 'default-images/black_logo.png')) }}"
                        alt="">
                </div>
                @if (session('success'))
                    <div class="alert-box" id="customAlert">
                        <div class="alert-message">
                            <span>{{ session('success') }}</span>
                        </div>
                        <span class="alert-close" onclick="document.getElementById('customAlert').remove()">×</span>
                    </div>
                @endif
                <div class="form">

                    <h3>Đăng nhập</h3>
                    <form action="{{ route('postLogin') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control input-field" id="email" name="email"
                                placeholder="Nhập email" value="{{ old('email') }}">
                            @error('email')
                                <small class="form_message text-danger" id="error-email"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div style="position: relative;">
                                <input type="password" class="form-control input-field" id="password" name="password"
                                    placeholder="Nhập mật khẩu">
                                <span class="toggle-password" onclick="togglePassword()"
                                    style="position:absolute; right:10px; top: 50%; transform: translateY(-50%); cursor:pointer;">
                                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                                </span>
                            </div>
                            @error('password')
                                <small class="form_message text-danger" id="error-password"
                                    style="display: block">{{ $message }}</small>
                            @enderror
                        </div>

                        @if (session('messageError'))
                            <small class="form_message text-danger">{{ session('messageError') }}</small>
                        @endif

                        <div class="mb-3">
                            <a href="{{ route('password.request') }}" style="text-decoration: none;"
                                onmouseover="this.style.textDecoration='underline'"
                                onmouseout="this.style.textDecoration='none'">
                                Quên mật khẩu?
                            </a>
                        </div>


                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>

                        <p class="mt-3">Bạn chưa có tài khoản? <a href="{{ asset('register') }}"
                                style="text-decoration: none;" onmouseover="this.style.textDecoration='underline'"
                                onmouseout="this.style.textDecoration='none'">Đăng ký</a></p>
                    </form>
                </div>
            </div>

            <a href="{{ asset('/') }}" class="back-button">
                &#8592;
            </a>
        </div>
    </main>

    <style>
        .alert-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            border-left: 4px solid #16a34a;
            /* xanh lá */
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            color: #1f2937;
            position: relative;
        }

        .alert-message {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-icon {
            color: #16a34a;
            font-size: 20px;
        }

        .alert-close {
            cursor: pointer;
            font-size: 20px;
            color: #6b7280;
            transition: color 0.2s ease;
        }

        .alert-close:hover {
            color: #111827;
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
