@extends('layouts.ClientLayout')

@section('title-page')
    Danh sách thợ Barber House
@endsection

@section('content')
    <main class="container py-5">
        <div class="list-barber">
            <h2 class="text-center mb-5 fw-bold text-uppercase">Top thợ cắt của Barber House</h2>

            <!-- Bộ lọc -->
            <form method="GET" action="{{ route('client.listBarber') }}" class="row g-3 justify-content-center mb-4">
                <div class="col-md-4">
                    <select name="branch_id" id="branch_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả chi nhánh</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="rating" id="rating" class="form-select" onchange="this.form.submit()">
                        <option value="">Tất cả đánh giá</option>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>Từ
                                {{ $i }} sao</option>
                        @endfor
                    </select>
                </div>
            </form>

            <!-- Danh sách thợ -->
            <div class="row g-4">
                @forelse ($barbers as $barber)
                    <div class="col-md-4 col-lg-3">
                        <div class="card barber-card border-0 shadow-sm h-100">
                            <div class="barber-img-wrapper">
                                <a href="{{ route('client.detailBarber', $barber->id) }}">
                                    <img src="{{ asset('storage/' . $barber->avatar) }}" class="card-img-top barber-img"
                                        alt="{{ $barber->name }}">
                                </a>
                            </div>
                            <div class="card-body text-center">
                                <h5 class="card-title mb-1">{{ $barber->name }}</h5>
                                <p class="mb-1 text-muted">Chi nhánh: {{ $barber->branch->name ?? 'Không rõ' }}</p>
                                <div class="rating mb-3">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= round($barber->rating_avg))
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-secondary"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-1">({{ number_format($barber->rating_avg, 1) }})</span>
                                </div>
                                <a href="{{ url('/dat-lich?barber_id=' . $barber->id) }}" class="book-btn">
                                    <i class="fas fa-calendar-alt me-1"></i> Đặt lịch
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center">
                        <p>Không có thợ nào phù hợp với bộ lọc.</p>
                    </div>
                @endforelse
            </div>

            <!-- Phân trang -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $barbers->withQueryString()->links() }}
            </div>
        </div>
    </main>

    <!-- Style tùy chỉnh -->
    <style>
        #mainNav {
            background-color: #000;
        }

        .barber-card {
            border-radius: 18px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .barber-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .barber-img-wrapper {
            overflow: hidden;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .barber-img {
            height: 240px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .barber-img-wrapper:hover .barber-img {
            transform: scale(1.05);
        }

        .rating i {
            font-size: 0.9rem;
        }

        .book-btn {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #fff;
            background: linear-gradient(135deg, #000000, #f5c518);
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .book-btn:hover {
            background: linear-gradient(135deg, #f5c518, #000000);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 197, 24, 0.4);
        }
    </style>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
