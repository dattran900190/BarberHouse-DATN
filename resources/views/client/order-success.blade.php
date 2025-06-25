@extends('layouts.ClientLayout')

@section('title-page', 'Đặt hàng thành công')


@section('content')
    <main class="container">
        <div class="text-center">
            {{-- Biểu tượng thành công --}}
            <div style="font-size: 60px; color: #28a745;">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            {{-- Tiêu đề --}}
            <h2 class="mt-3">Đặt hàng thành công!</h2>

            {{-- Nội dung thông báo --}}
            <p class="lead">
                Cảm ơn bạn đã đặt hàng tại <strong>Barber House</strong>.
            </p>

            {{-- Nút điều hướng --}}
            <div class="mt-4">
                <a href="{{ route('home') }}" class="btn btn-primary px-4 py-2">
                    <i class="fa-solid fa-house"></i> Về trang chủ
                </a>
                <a href="{{ route('cart.show') }}" class="btn btn-outline-secondary px-4 py-2 ms-2">
                    <i class="fa-solid fa-cart-shopping"></i> Xem giỏ hàng
                </a>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
        .container {
            margin-top: 200px;
            padding: 20px;
            text-align: center;
        }
    </style>
@endsection
