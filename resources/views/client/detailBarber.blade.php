@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết thợ cắt tóc - {{ $barber->name }}
@endsection

@section('content')
    <main class="container py-5">
        <div class="main-detail-barber">
            <section class="py-3">
                <div class="container">
                    <!-- Barber Profile Card -->
                    <div class="row mb-5">
                        <div class="col-lg-12">
                            <div class="barber-card shadow-lg rounded-4 overflow-hidden bg-white">
                                <div class="row g-0">
                                    <!-- Barber Image -->
                                    <div class="col-md-4 p-4 d-flex align-items-center justify-content-center bg-light">
                                        <div class="position-relative">
                                            <img src="{{ $barber->avatar ? asset('storage/' . $barber->avatar) : 'https://bootdey.com/img/Content/avatar/avatar7.png' }}"
                                                alt="{{ $barber->name }}" class="img-fluid rounded-circle shadow barber-avatar">
                                            <div
                                                class="rating-badge position-absolute top-0 end-0 bg-warning text-white rounded-circle p-2 shadow">
                                                {{ number_format($barber->rating_avg ?? 0, 1) }} <i
                                                    class="fa-solid fa-star"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Barber Info -->
                                    <div class="col-md-8 p-4 position-relative">
                                        <div class="d-flex flex-column h-100">
                                            <div class="mb-3">
                                                <h1 class="text-dark fw-bold mb-2">{{ $barber->name }}</h1>
                                                <div class="d-flex align-items-center mb-3">
                                                    <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                    <span class="text-muted"> Chi Nhánh:
                                                        {{ $barber->branch->name ?? 'Chưa có chi nhánh' }}</span>
                                                </div>

                                                @php
                                                    $skillLevels = [
                                                        'assistant' => 'Thử việc',
                                                        'junior' => 'Sơ cấp',
                                                        'senior' => 'Chuyên nghiệp',
                                                        'master' => 'Bậc thầy',
                                                        'expert' => 'Chuyên gia',
                                                    ];
                                                    $skillLevelColors = [
                                                        'assistant' => 'secondary',
                                                        'junior' => 'info',
                                                        'senior' => 'primary',
                                                        'master' => 'success',
                                                        'expert' => 'warning',
                                                    ];
                                                    $levelKey = $barber->skill_level;
                                                @endphp

                                                <div class="skills mb-3">
                                                    <span
                                                        class="badge bg-{{ $skillLevelColors[$levelKey] ?? 'dark' }} me-2 mb-2">
                                                        <i class="fas fa-cut me-1"></i>
                                                        {{ $skillLevels[$levelKey] ?? 'Không xác định' }}
                                                    </span>
                                                </div>


                                                <!-- Contact Information -->
                                                <div class="contact-info mb-4">
                                                    <h5 class="fw-bold mb-3">Thông tin liên hệ</h5>
                                                    <ul class="list-unstyled">
                                                        <li class="mb-2 d-flex">
                                                            <i class="fas fa-map-marker-alt text-muted mt-1 me-2"></i>
                                                            <span>{{ $barber->branch->address ?? 'Chưa cập nhật địa chỉ' }}</span>
                                                        </li>
                                                        <li class="d-flex">
                                                            <i class="fas fa-phone-alt text-muted mt-1 me-2"></i>
                                                            <span>{{ $barber->branch->phone ?? 'Chưa cập nhật số điện thoại' }}</span>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="mt-auto">
                                                <a href="{{ route('dat-lich', $barber->id) }}" class="btn-outline-buy">
                                                    <i class="fa-solid fa-calendar-check me-2"></i>Đặt lịch ngay
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0 rounded-4">
                                <div class="card-body p-4">
                                    <h4 class="mb-4 text-dark fw-bold border-bottom pb-3">Hồ sơ thợ cắt</h4>
                                    <div class="profile-content">
                                        {!! $barber->profile ? e($barber->profile) : '<p class="text-muted">Chưa có thông tin hồ sơ.</p>' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0 rounded-4">
                                <div class="card-body p-4">
                                        <div class="d-flex justify-content-between align-items-center mb-4 reviews-header">
                                        <h4 class="mb-0 text-dark fw-bold">Đánh giá</h4>
                                        <div class="d-flex align-items-center reviews-toolbar">
                                            <span class="badge bg-warning text-dark me-3">
                                                {{ $reviews->total() }} đánh giá
                                            </span>
                                            <!-- Bộ lọc số sao -->
                                             <form action="{{ route('client.detailBarber', $barber->id) }}" method="GET"
                                                class="d-flex reviews-filter">
                                                 <select name="star" onchange="this.form.submit()" class="form-select">
                                                    <option value="">Tất cả sao</option>
                                                    <option value="5" {{ request('star') == '5' ? 'selected' : '' }}>5
                                                        sao</option>
                                                    <option value="4" {{ request('star') == '4' ? 'selected' : '' }}>4
                                                        sao</option>
                                                    <option value="3" {{ request('star') == '3' ? 'selected' : '' }}>3
                                                        sao</option>
                                                    <option value="2" {{ request('star') == '2' ? 'selected' : '' }}>2
                                                        sao</option>
                                                    <option value="1" {{ request('star') == '1' ? 'selected' : '' }}>1
                                                        sao</option>
                                                </select>
                                            </form>
                                        </div>
                                    </div>

                                    @if ($reviews->isEmpty())
                                        <div class="text-center py-5">
                                            <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Chưa có đánh giá nào.</p>
                                        </div>
                                    @else
                                        <div class="review-section">
                                            @foreach ($reviews as $review)
                                                <div class="review-item p-3 mb-3 rounded-3 border bg-white">
                                                    <div class="d-flex">
                                                        <div class="review-avatar me-3"
                                                            style="background-image: url('{{ $review->user && $review->user->avatar ? asset('storage/' . $review->user->avatar) : 'https://bootdey.com/img/Content/avatar/avatar2.png' }}')">
                                                        </div>
                                                        <div class="flex-grow-1">
                                                             <div
                                                                class="d-flex justify-content-between align-items-start mb-2 review-top">
                                                                <div>
                                                                    <h6 class="mb-0 fw-bold">
                                                                        {{ $review->user->name ?? 'Khách hàng ẩn danh' }}
                                                                    </h6>
                                                                    <small
                                                                        class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                                                </div>
                                                                 <div class="text-warning review-stars">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <i
                                                                            class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star"></i>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <p class="mb-0">
                                                                {{ $review->comment ?? 'Không có bình luận.' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="pagination mt-4 d-flex justify-content-center">
                                                {{ $reviews->links() }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <style>
        /* Main Styles */
        body {
            background-color: #f8f9fa;
        }

        #mainNav {
            background-color: #000;
        }

        /* Barber Card */
        .barber-card {
            transition: transform 0.3s ease,
                box-shadow 0.3s ease;
            border: none;
        }

        .barber-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .rating-badge {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }


        /* Review Section */
        .review-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .review-item {
            transition: transform 0.2s ease;
        }

        .review-item:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        /* Skills Badges */
        .skills .badge {
            font-weight: 500;
            padding: 6px 12px;
            /* border-radius: 20px; */
        }

        /* Profile Content */
        .profile-content {
            line-height: 1.8;
        }

        .profile-content p {
            margin-bottom: 1rem;
        }

        /* Pagination */
        /* .pagination .page-item.active .page-link {
                                background-color: #000;
                                border-color: #000;
                            }

                            .pagination .page-link {
                                color: #000;
                                border-radius: 50% !important;
                                width: 40px;
                                height: 40px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 3px;
                                border: 1px solid #dee2e6;
                            }

                            .pagination .page-link:hover {
                                background-color: #f8f9fa;
                            } */

        /* Card Styles */
        .card {
            border: none;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        /* Breadcrumb */
        .breadcrumb {
            background-color: transparent;
            padding: 0;
        }

        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: #000;
            font-weight: 500;
        }

        /* Contact Info */
        .contact-info {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        /* Responsive Tweaks */
        .barber-avatar {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border: 5px solid #fff;
        }

        @media (max-width: 991.98px) {
            .barber-avatar {
                width: 200px;
                height: 200px;
            }
        }

        @media (max-width: 767.98px) {
            .barber-avatar {
                width: 140px;
                height: 140px;
            }

            .rating-badge {
                width: 40px;
                height: 40px;
                font-size: 0.95rem;
            }

            .barber-card .p-4 {
                padding: 1rem !important;
            }

            .reviews-header {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.75rem;
            }

            .reviews-filter {
                width: 100%;
            }

            .reviews-filter .form-select {
                width: 100% !important;
            }

            .reviews-toolbar {
                flex-wrap: wrap;
            }

            .reviews-toolbar .badge {
                margin-bottom: 8px;
            }

            .review-top {
                flex-direction: column;
                gap: 6px;
            }

            .review-stars {
                align-self: flex-start;
            }

            /* Contact info wrap and align */
            .contact-info ul li {
                align-items: flex-start !important;
            }

            .contact-info ul li i {
                flex: 0 0 auto;
                margin-top: 2px;
            }

            .contact-info ul li span {
                flex: 1 1 auto;
                min-width: 0;
                overflow-wrap: anywhere;
                word-break: break-word;
                white-space: normal;
                display: block;
            }

            /* Booking button fits small widths */
            .main-detail-barber .btn-outline-buy {
                width: 100%;
                justify-content: center;
                text-align: center;
                flex-wrap: wrap;
                white-space: normal;
                line-height: 1.3;
                padding: 8px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 360px) {
            .barber-avatar {
                width: 120px;
                height: 120px;
            }

            h1.text-dark.fw-bold {
                font-size: 1.25rem;
            }

            .rating-badge {
                width: 36px;
                height: 36px;
                font-size: 0.85rem;
            }

            .barber-card .p-4 {
                padding: 0.75rem !important;
            }
        }
    </style>
@endsection
