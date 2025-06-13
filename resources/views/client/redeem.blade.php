@extends('layouts.ClientLayout')

@section('title-page')
    Đổi điểm khuyến mãi
@endsection

@section('content')
    <main class="container py-4">
        <section class="h-100">
            {{-- Thông tin người dùng --}}
            <div class="bg-white p-4 rounded shadow mb-4">
                <h3 class="mb-4 text-primary">Thông tin người dùng</h3>
                <div class="mb-3">
                    <label class="form-label">Tên người dùng</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name ?? 'Chưa đăng nhập' }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->email ?? 'Chưa đăng nhập' }}" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điểm hiện tại</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->points_balance ?? 0 }} điểm" disabled>
                </div>
            </div>

            {{-- Danh sách khuyến mãi --}}
            <div class="bg-white p-4 rounded shadow">
                <h3 class="mb-4 text-primary">Danh sách phiếu giảm giá</h3>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    @forelse ($promotions as $promotion)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card border-primary h-100 shadow-sm">
                                <div class="card-body text-center d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="card-title text-primary">{{ $promotion->code }}</h5>
                                        <p class="card-text">
                                            Giảm:
                                            <strong>
                                                {{ $promotion->discount_amount ?? $promotion->discount_percent . '%' }}
                                            </strong>
                                        </p>
                                        <p class="card-text">Cần: <strong>{{ $promotion->required_points }} điểm</strong></p>
                                    </div>
                                    <form action="{{ route('client.redeem.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="promotion_id" value="{{ $promotion->id }}">
                                        <button type="submit" class="btn btn-success w-100 mt-3">Đổi ngay</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center mt-4">
                            <p>Hiện không có mã khuyến mãi nào để đổi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }

        body {
            background-color: #f8f9fa;
        }

        h3 {
            font-weight: 600;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            transition: 0.2s;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
@endsection
