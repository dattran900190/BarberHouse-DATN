@extends('layouts.ClientLayout')

@section('title-page')
    Đổi điểm lấy mã giảm giá
@endsection

@section('content')
    <main class="container py-4">
        <section class="h-100">

            {{-- Thông tin người dùng --}}
            <div class="bg-white p-4 rounded shadow mb-4">
                <h3 class="mb-4 text-primary">Thông tin người dùng</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tên người dùng</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->email }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số điểm hiện tại</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->points_balance }} điểm" disabled>
                    </div>
                </div>
            </div>

            {{-- Danh sách mã khuyến mãi --}}
            <div class="bg-white p-4 rounded shadow">
                <h3 class="mb-4 text-primary">Danh sách mã khuyến mãi</h3>
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($promotions->count())
                    <ul class="list-group">
                        @foreach ($promotions as $promotion)
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="mb-1 text-danger">Mã: <strong>{{ $promotion->code }}</strong></h6>
                                    <small>
                                        🎁 Giảm
                                        @if ($promotion->discount_type === 'percent')
                                            {{ $promotion->discount_value }}%
                                        @else
                                            {{ number_format($promotion->discount_value) }}đ
                                        @endif
                                        @if ($promotion->max_discount_amount)
                                            (Tối đa {{ number_format($promotion->max_discount_amount) }}đ)
                                        @endif
                                        <br>
                                        🎯 Cần: {{ $promotion->required_points }} điểm |
                                        📦 Còn: {{ $promotion->quantity }} |
                                        🕒 HSD: {{ $promotion->end_date->format('d/m/Y') }}
                                    </small>
                                </div>
                                <div>
                                    <form action="{{ route('client.redeem.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                                        <button type="submit" class="btn btn-sm btn-warning">Đổi mã</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Phân trang --}}
                    <div class="mt-4">
                        {{ $promotions->links() }}
                    </div>
                @else
                    <div class="text-center mt-4">
                        <p>😢 Hiện tại bạn chưa đủ điều kiện để đổi mã khuyến mãi.</p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    {{-- CSS tùy chỉnh --}}
    <style>
        #mainNav {
            background-color: #000;
        }

        body {
            background-color: #f4f6f9;
        }

        h3 {
            font-weight: 600;
        }

        .list-group-item {
            border: 1px solid #ff5722;
            border-radius: 10px;
            margin-bottom: 10px;
            background-color: #fff3e0;
            padding: 15px;
            transition: 0.2s;
        }

        .list-group-item:hover {
            background-color: #ffe0b2;
        }

        .btn-warning {
            background-color: #ff5722;
            border: none;
            font-weight: 500;
            padding: 6px 14px;
            font-size: 14px;
        }

        .btn-warning:hover {
            background-color: #e64a19;
        }

        .btn-close {
            float: right;
        }

        .pagination {
            justify-content: center;
        }

        .pagination .page-link {
            color: #ff5722;
        }

        .pagination .active .page-link {
            background-color: #ff5722;
            border-color: #ff5722;
            color: #fff;
        }
    </style>
@endsection
