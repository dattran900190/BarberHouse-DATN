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

                <div class="form">
                    <h3>Quên mật khẩu</h3>
                    <form method="POST" action="{{ route('password.sendOtp') }}">
                        @csrf
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email " >
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Gửi mã OTP</button>
                    </form>
                </div>
            </div>

            <a href="{{ asset('/') }}" class="back-button">
                &#8592;
            </a>
        </div>
    </main>

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
