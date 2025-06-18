@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết thợ cắt tóc - {{ $barber->name }}
@endsection

@section('content')
    <main class="container py-5">
        <div class="main-detail-barber">
            <section class="bg-light py-5">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 mb-5">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body p-4 p-md-5 p-lg-6">
                                    <div class="row align-items-center">
                                        <div class="col-lg-6 mb-4 mb-lg-0">
                                            <img src="{{ $barber->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar7.png' }}"
                                                alt="{{ $barber->name }}" class="img-fluid rounded">
                                        </div>
                                        <div class="col-lg-6 px-xl-10">
                                            <div class="bg-white p-4 mb-4 rounded">
                                                <h3 class="h2 text-dark mb-0">{{ $barber->name }}</h3>
                                                <span class="text-primary">Thợ cắt tóc tại
                                                    {{ $barber->branch->name ?? 'chưa có chi nhánh' }}</span>
                                            </div>
                                            <ul class="list-unstyled mb-4">
                                                <li class="mb-3">
                                                    <span class="text-secondary me-2 font-weight-600">Chi nhánh:</span>
                                                    {{ $barber->branch->address ?? 'Chưa cập nhật' }}
                                                </li>
                                                <li class="mb-3">
                                                    <span class="text-secondary me-2 font-weight-600">Trình độ kỹ
                                                        năng:</span>
                                                    {{ $barber->skill_level ?? 'Chưa cập nhật' }}
                                                </li>
                                                <li class="mb-3">
                                                    <span class="text-secondary me-2 font-weight-600">Đánh giá trung
                                                        bình:</span>
                                                    {{ number_format($barber->rating_avg ?? 0, 1) }}
                                                    <i class="fa-solid fa-star text-warning"></i>
                                                </li>
                                            </ul>
                                            <a href="{{ route('dat-lich', $barber->id) }}" class="btn btn-primary">Đặt lịch
                                                hẹn</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-5">
                            <div>
                                <h4 class="text-primary mb-4">Hồ sơ</h4>
                                <p>{!! $barber->profile ?? 'Chưa có thông tin hồ sơ.' !!}</p>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <h4 class="text-primary mb-4">Đánh giá</h4>
                            @if ($barber->reviews->where('is_visible', true)->isEmpty())
                                <p class="text-muted">Chưa có đánh giá nào.</p>
                            @else
                                <div class="py-2">
                                    @foreach ($barber->reviews->where('is_visible', true) as $review)
                                        <div class="media mb-4">
                                            <div style="background-image: url({{ $review->user->avatar ?? 'https://bootdey.com/img/Content/avatar/avatar2.png' }})"
                                                class="media-object avatar avatar-md mr-3 rounded-circle"></div>
                                            <div class="media-body">
                                                <div class="media-heading">
                                                    <small
                                                        class="float-right text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                                    <h5>{{ $review->user->name ?? 'Khách hàng ẩn danh' }}</h5>
                                                </div>
                                                <div class="text-muted mb-2">
                                                    {{ $review->comment ?? 'Không có bình luận.' }}</div>
                                                <div class="text-warning">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fa-solid fa-star {{ $i <= $review->rating ? '' : 'fa-regular' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    @endforeach
                                    <nav class="pagination mt-4" aria-label="Page navigation">
                                        {{ $reviews->links() }}
                                    </nav>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
@endsection

<style>
    .main-detail-barber .card {
        transition: transform 0.3s;
    }

    .main-detail-barber .card:hover {
        transform: translateY(-5px);
    }

    .avatar {
        width: 50px;
        height: 50px;
        background-size: cover;
        background-position: center;
    }

    .pagination .page-link {
        color: #007bff;
        margin: 0 5px;
        border-radius: 5px;
    }

    .pagination .page-link:hover {
        background-color: #f8f9fa;
    }

    .pagination .active .page-link {
        background-color: #007bff;
        color: white;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
</style>
