<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Barber Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('select.form-select');
            selects.forEach(select => {
                if (select.value) {
                    select.classList.add('filled');
                }

                select.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.add('filled');
                    } else {
                        this.classList.remove('filled');
                    }
                });
            });
        });
    </script>
</head>

<body>
    <div class="form">
        <div class="form_title">Đăng ký</div>

        @if (session('success'))
            <small class="form_message" style="color: green">{{ session('success') }}</small>
        @endif
        @if (session('messageError'))
            <small class="form_message">{{ session('messageError') }}</small>
        @endif

        <form action="{{ route('postRegister') }}" method="POST">
            @csrf

            <div class="form_row">
                <input type="text" name="name" id="name" class="input-field" placeholder=" "
                    value="{{ old('name') }}">
                <label for="name">Họ và tên</label>
            </div>
            @error('name')
                <small class="form_message" id="error-name" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form-floating mb-3">
                <select class="form-select" id="gender" name="gender">
                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Chọn giới tính</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                </select>
                <label for="gender">Giới tính</label>
            </div>
            @error('gender')
                <small class="form_message" id="error-gender" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form_row">
                <input type="text" name="phone" id="phone" class="input-field" placeholder=" "
                    value="{{ old('phone') }}">
                <label for="phone">Số điện thoại</label>
            </div>
            @error('phone')
                <small class="form_message" id="error-phone" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form_row">
                <input type="text" name="address" id="address" class="input-field" placeholder=" "
                    value="{{ old('address') }}">
                <label for="address">Địa chỉ</label>
            </div>
            @error('address')
                <small class="form_message" id="error-address" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form_row">
                <input type="email" name="email" id="email" class="input-field" placeholder=" "
                    value="{{ old('email') }}">
                <label for="email">Email</label>
            </div>
            @error('email')
                <small class="form_message" id="error-email" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form_row" style="position: relative;">
                <input type="password" name="password" id="password" class="input-field" placeholder=" ">
                <label for="password">Mật khẩu</label>
                <span class="toggle-password" onclick="togglePassword('password', 'eye-icon1')">
                    <i class="fa-solid fa-eye" id="eye-icon1"></i>
                </span>
            </div>
            @error('password')
                <small class="form_message" id="error-password" style="display: block">{{ $message }}</small>
            @enderror

            <div class="form_row" style="position: relative;">
                <input type="password" name="password_confirmation" id="password_confirmation" class="input-field"
                    placeholder=" ">
                <label for="password_confirmation">Nhập lại mật khẩu</label>
                <span class="toggle-password" onclick="togglePassword('password_confirmation', 'eye-icon2')">
                    <i class="fa-solid fa-eye" id="eye-icon2"></i>
                </span>
            </div>
            @error('password_confirmation')
                <small class="form_message" id="error-password_confirmation"
                    style="display: block">{{ $message }}</small>
            @enderror

            <button type="submit" class="form-submit">Đăng ký</button>

            <div class="form_link">
                <a href="{{ route('login') }}">Bạn đã có tài khoản?</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
        document.querySelectorAll('.form_row input').forEach(function(input) {
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
