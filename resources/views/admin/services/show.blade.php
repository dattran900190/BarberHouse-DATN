@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Dịch vụ</h3>
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
                <a href="{{ url('admin/services') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/services') }}">Dịch vụ</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/services/' . $service->id) }}">Chi tiết dịch vụ</a>
            </li>
        </ul>
    </div>

   <!-- Card 1: Thông tin dịch vụ -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết dịch vụ</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                @if ($service->image)
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('storage/' . $service->image) }}" alt="Ảnh dịch vụ"
                            class="img-fluid rounded mb-3" style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                    </div>
                @endif
                <div class="col-md-{{ $service->image ? '8' : '12' }}">
                    <h4 class="fw-bold mb-3">{{ $service->name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fa fa-info-circle me-2 text-primary"></i>
                        {{ $service->description ?? 'Không có mô tả' }}
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div>
                            <i class="fa fa-dollar-sign me-2 text-success"></i>
                            <span class="fw-semibold">Giá:</span>
                            <span class="text-success fw-bold">{{ number_format($service->price, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div>
                            <i class="fa fa-clock me-2 text-warning"></i>
                            <span class="fw-semibold">Thời lượng:</span>
                            <span>{{ $service->duration }} phút</span>
                        </div>
                        <div>
                            <i class="fa fa-tag me-2 text-info"></i>
                            <span class="fw-semibold">Loại:</span>
                            <span class="badge bg-{{ $service->is_combo ? 'warning text-dark' : 'secondary' }}">
                                {{ $service->is_combo ? 'Combo' : 'Thông thường' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-muted mb-3">
                        <i class="fa fa-calendar-alt me-2 text-muted"></i>
                        <span class="fw-semibold">Ngày tạo:</span>
                        {{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : 'Không xác định' }}
                    </div>
                    <div class="text-muted">
                        <i class="fa fa-calendar-check me-2 text-muted"></i>
                        <span class="fw-semibold">Ngày cập nhật:</span>
                        {{ $service->updated_at ? $service->updated_at->format('d/m/Y H:i') : 'Không xác định' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('services.edit', $service->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá dịch vụ này không?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                </form>
                <a href="{{ route('services.index', ['page' => request('page', 1)]) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

@endsection
