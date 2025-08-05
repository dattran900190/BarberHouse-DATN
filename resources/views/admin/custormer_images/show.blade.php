@extends('layouts.AdminLayout')

@section('title', 'Chi tiết ảnh khách hàng')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Ảnh khách hàng</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ route('customer-images.index') }}">Ảnh khách hàng</a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <span>Chi tiết ảnh</span>
            </li>
        </ul>
    </div>

    <!-- Card: Thông tin ảnh -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white">
            <h4 class="card-title">Chi tiết ảnh khách hàng</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong class="text-muted">Trạng thái:</strong>
                @if ($customerImage->status)
                    <span class="badge bg-success">Hiển thị</span>
                @else
                    <span class="badge bg-secondary">Ẩn</span>
                @endif
            </div>

            @if ($customerImage->image)
                <div class="mb-3">
                    <strong class="text-muted">Hình ảnh:</strong><br>
                    <img src="{{ asset('storage/' . $customerImage->image) }}" alt="Ảnh khách hàng"
                        class="img-fluid rounded shadow-sm border" style="max-height: 300px; object-fit: cover;">
                </div>
            @else
                <p class="text-muted">Chưa có hình ảnh.</p>
            @endif
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm">
        <div class="card-header text-white">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body d-flex gap-2">
            <a href="{{ route('customer-images.edit', $customerImage->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-edit me-1"></i> Sửa
            </a>
            <a href="{{ route('customer-images.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
@endsection
