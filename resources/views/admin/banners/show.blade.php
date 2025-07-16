@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Banner')

@section('content')
    <!-- Header + Breadcrumb -->
    <div class="page-header">
        <h3 class="fw-bold mb-3">Banner</h3>
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
                <a href="{{ route('banners.index') }}">Banner</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Chi tiết banner</a>
            </li>
        </ul>
    </div>

    <!-- Card: Thông tin banner -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết Banner</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                @if ($banner->image_url)
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('storage/' . $banner->image_url) }}" alt="Ảnh banner"
                             class="img-fluid rounded mb-3" style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                    </div>
                @endif

                <div class="col-md-{{ $banner->image_url ? '8' : '12' }}">
                    <h4 class="fw-bold mb-3">{{ $banner->title }}</h4>

                    @if (!empty($banner->link_url))
                        <p class="text-muted mb-2">
                            <i class="fa fa-link me-2 text-primary"></i><strong>Liên kết:</strong>
                            <a href="{{ $banner->link_url }}" target="_blank">{{ $banner->link_url }}</a>
                        </p>
                    @endif

                    <p class="text-muted mb-2">
                        <i class="fa fa-eye me-2 text-success"></i><strong>Trạng thái:</strong>
                        @if ($banner->is_active)
                            <span class="badge bg-success">Hiển thị</span>
                        @else
                            <span class="badge bg-secondary">Ẩn</span>
                        @endif
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-calendar me-2 text-muted"></i><strong>Ngày tạo:</strong>
                        {{ $banner->created_at->format('d/m/Y H:i') }}
                    </p>
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
                <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <a href="{{ route('banners.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
@endsection
