@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Bình luận</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews') }}">Bình luận</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews/' . $review->id) }}">Chi tiết bình luận</a>
            </li>
        </ul>
    </div>

    <!-- Card: Chi tiết bình luận -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Chi tiết bình luận</h4>
        </div>

        <div class="card-body">
            <div class="row gy-3">
                <div class="col-12">
                    <p class="fw-bold mb-3">
                        <i class="fa fa-user me-2 text-primary"></i>Người bình luận:
                        <span class="fw-normal text-dark">{{ $review->user->name }}</span>
                    </p>

                    <p class="mb-2">
                        <i class="fas fa-user-check me-2 text-info"></i>
                        <strong>Thợ:</strong> {{ $review->barber->name }}
                    </p>

                    <p class="mb-2">
                        <i class="fa fa-star me-2 text-warning"></i>
                        <strong>Đánh giá:</strong> {{ $review->rating }}
                    </p>

                    <p class="mb-2">
                        <i class="fa fa-comment-dots me-2 text-secondary"></i>
                        <strong>Bình luận:</strong> {{ $review->comment }}
                    </p>

                    <p class="mb-3">
                        <i class="fa fa-eye me-2 text-success"></i>
                        <strong>Trạng thái:</strong>
                        @if ($review->is_visible == 1)
                            <span class="badge bg-success">Hiện</span>
                        @else
                            <span class="badge bg-danger">Ẩn</span>
                        @endif
                    </p>


                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá dịch vụ này không?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                </form>
                <a href="{{ route('reviews.index', ['page' => request('page', 1)]) }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

@endsection
