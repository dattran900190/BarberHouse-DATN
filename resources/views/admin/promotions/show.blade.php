@extends('layouts.AdminLayout')

@section('title', 'Chi tiết mã giảm giá')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Mã giảm giá</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"> <a href="{{ url('admin/dashboard') }}">Quản lý đặt lịch</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/promotions') }}">Quản lý khuyến mãi</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">Chi tiết mã giảm giá</li>
        </ul>
    </div>

    <!-- Card: Thông tin mã giảm giá -->
    <div class="card shadow-sm mb-4">
    <div class="card-header text-white d-flex align-items-center">
        <h4 class="card-title mb-0">Chi tiết mã giảm giá</h4>
    </div>
    <div class="card-body">
        <div class="row gy-3">
            {{-- Dòng 1 --}}
            <div class="col-md-6">
                <i class="fa fa-barcode me-2 text-muted"></i>
                <strong>Mã giảm giá:</strong> {{ $promotion->code }}
            </div>
            <div class="col-md-6">
                <i class="fa fa-tag me-2 text-primary"></i>
                <strong>Loại giảm giá:</strong> 
                {{ $promotion->discount_type === 'percent' ? 'Phần trăm' : 'Cố định' }}
            </div>

            {{-- Dòng 2 --}}
            <div class="col-md-6">
                <i class="fa fa-percent me-2 text-success"></i>
                <strong>Giá trị giảm giá:</strong> 
                {{ $promotion->discount_value }} {{ $promotion->discount_type === 'percent' ? '%' : 'VNĐ' }}
            </div>
            <div class="col-md-6">
                <i class="fas fa-minus-circle me-2 text-warning"></i>
                <strong>Giảm giá tối đa:</strong> {{ number_format($promotion->max_discount_amount, 0, ',', '.') }} VNĐ
            </div>

            {{-- Dòng 3 --}}
            <div class="col-md-6">
                <i class="fa fa-money-bill-alt me-2 text-info"></i>
                <strong>Giá trị đơn hàng tối thiểu:</strong> {{ number_format($promotion->min_order_value, 0, ',', '.') }} VNĐ
            </div>
            <div class="col-md-6">
                <i class="fa fa-boxes me-2 text-muted"></i>
                <strong>Số lượng:</strong> {{ $promotion->quantity }}
            </div>

            {{-- Dòng 4 --}}
            <div class="col-md-6">
                <i class="fa fa-user-lock me-2 text-muted"></i>
                <strong>Giới hạn sử dụng:</strong> {{ $promotion->usage_limit }}
            </div>
            <div class="col-md-6">
                <i class="fa fa-gem me-2 text-warning"></i>
                <strong>Điểm yêu cầu:</strong> {{ $promotion->required_point ?? 0 }}
            </div>

            {{-- Dòng 5 --}}
            <div class="col-md-6">
                <i class="fa fa-calendar-alt me-2 text-success"></i>
                <strong>Ngày bắt đầu:</strong> {{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y') }}
            </div>
            <div class="col-md-6">
                <i class="fa fa-calendar-check me-2 text-success"></i>
                <strong>Ngày kết thúc:</strong> {{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y') }}
            </div>

            {{-- Dòng 6 --}}
            <div class="col-md-6">
                <i class="fa fa-toggle-on me-2 text-muted"></i>
                <strong>Trạng thái:</strong>
                <span class="badge bg-{{ $promotion->is_active ? 'success' : 'secondary' }}">
                    {{ $promotion->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                </span>
            </div>
            <div class="col-md-6">
                <i class="fa fa-info-circle me-2 text-muted"></i>
                <strong>Mô tả:</strong> {{ $promotion->description ?? 'Không có mô tả' }}
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('promotions.edit', $promotion->id) }}" class="btn btn-outline-primary btn-sm"><i
                class="fa fa-edit me-1"></i> Sửa</a>
                <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
    </div>
</div>




@endsection
