@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Bài viết')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tin tức</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/posts') }}">Bài viết</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/posts/' . $post->id) }}">Chi tiết bài viết</a>
            </li>
        </ul>
    </div>

    <!-- Card: Thông tin bài viết -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết bài viết</h4>
        </div>
        <div class="card-body">

            <div class="col-md-{{ $post->image ? '8' : '12' }}">
                <h4 class="fw-bold mb-3">{{ $post->title }}</h4>

                <p class="text-muted mb-2">
                    <i class="fa fa-link me-2 text-primary"></i><strong>Slug:</strong> {{ $post->slug }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-info-circle me-2 text-info"></i><strong>Mô tả:</strong> {{ $post->short_description }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-star me-2 text-warning"></i><strong>Nổi bật:</strong>
                    {{ $post->is_featured ? 'Nổi bật' : 'Không nổi bật' }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-calendar me-2 text-muted"></i><strong>Ngày xuất bản:</strong>
                    {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') : 'Chưa có' }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-eye me-2 text-success"></i><strong>Trạng thái:</strong>
                    @if ($post->status)
                        <span class="badge bg-success">Đã xuất bản</span>
                    @else
                        <span class="badge bg-secondary">Bản nháp</span>
                    @endif
                </p>
                <p class="text-muted mb-2">
                    @if ($post->image)
                        <div class="col-md-4 ">
                            <i class="fa fa-image me-2 text-success"></i><strong class="text-muted mb-2">Ảnh nền:</strong>
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh bài viết"
                                class="img-fluid rounded mb-3"
                                style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                        </div>
                    @endif
                </p>
                <div class="mt-3">
                    <p class="fa fa-info-circle text-muted mb-2"><strong> Nội dung:</strong></p>
                    <div>{!! $post->content !!}</div>
                </div>
            </div>
        </div>
    </div>


    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
@endsection
