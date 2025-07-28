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
</head>

<body>
    <main>
        <div class="login">
            <div class="image-login">
                <img src="{{ asset('storage/' . ($imageSettings['anh_danh_ky'] ?? 'default-images/no-banggia.png')) }}" alt="Bảng đăng ký" />
            </div>

            <div class="form-login">
                <div class="image">
                    <img src="{{ asset('storage/' . ($imageSettings['black_logo'] ?? 'default-images/black_logo.png')) }}" alt="">
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
                            <input type="email" class="form-control input-field" id="email" name="email"
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

                        <p class="mt-3">Bạn đã có tài khoản? <a href="{{ asset('login') }}"  style="text-decoration: none;"
                                onmouseover="this.style.textDecoration='underline'"
                                onmouseout="this.style.textDecoration='none'">Đăng nhập</a></p>
                    </form>
                </div>
                <a href="{{ asset('/') }}" class="back-button">&#8592;</a>
            </div>
        </div>
    </main>

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
