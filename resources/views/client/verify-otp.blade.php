@extends('layouts.ClientLayout')

@section('title-page', 'Xác minh OTP')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="container py-5">
        <h3 class="mb-4">Xác minh mã OTP</h3>

        <form method="POST" action="{{ route('password.verifyOtp') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}">

            <div class="mb-3">
                <label>Mã OTP</label>
                <input type="text" name="otp" class="form-control" placeholder="Nhập mã OTP"
                    value="{{ old('otp') }}">
                @error('otp')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mb-3">
                <label>Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-control"
                    placeholder="Nhập lại mật khẩu mới">
            </div>

            <div class="mb-3">
                <span>Thời gian còn lại: <span id="countdown">02:00</span></span>
            </div>

            <button type="submit" class="btn btn-success">Đổi mật khẩu</button>

        </form>

    </div>
@endsection

@section('scripts')
    <script>
        let countdown = 120;
        const countdownEl = document.getElementById('countdown');

        const timer = setInterval(() => {
            const minutes = Math.floor(countdown / 60);
            const seconds = countdown % 60;
            countdownEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            countdown--;

            if (countdown < 0) {
                clearInterval(timer);
                countdownEl.textContent = 'Hết hạn';
            }
        }, 1000);
    </script>
@endsection
