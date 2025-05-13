<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Barber Booking</title>
    <link rel="stylesheet" href="css.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
    <div class="form">
        <div class="form_title">Đăng nhập</div>
        <form action="{{ route('postLogin') }}" method="POST">
            @csrf
            <div class="form_row">
                <input type="email" id="email" name="email" placeholder=" " value="{{ old('email') }}"
                    class="input-field">
                <label for="email">Email</label>
            </div>
            @error('email')
                <small class="form_message" id="error-email" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form_row">
                <input type="password" id="password" name="password" placeholder=" " class="input-field">
                <label for="password">Mật khẩu</label>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                </span>
            </div>
            @error('password')
                <small class="form_message" id="error-password" style="display: block">{{ $message }}</small>
            @enderror

            @if (session('messageError'))
                <small class="form_message">{{ session('messageError') }}</small>
            @endif

            <button type="submit" class="form-submit">Đăng nhập</button>
            <div class="form_link">
                <a href="{{ route('register') }}">Chưa có tài khoản?</a>
            </div>
        </form>
    </div>

    <script>
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
