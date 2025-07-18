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
            <h4 class="card-title">Chi tiết mã giảm giá</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">

                    <p> <strong>Mã giảm giá: </strong>{{ $promotion->code }}</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Loại giảm giá:
                        </strong>{{ $promotion->discount_type === 'percent' ? 'Phần trăm' : 'Cố định' }}</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Giá trị giảm giá: </strong>{{ $promotion->discount_value }}
                        {{ $promotion->discount_type === 'percent' ? '%' : 'VNĐ' }}</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Giảm giá tối đa: </strong>{{ number_format($promotion->max_discount_amount) }} VNĐ</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Giá trị đơn hàng tối thiểu: </strong>{{ number_format($promotion->min_order_value) }} VNĐ
                    </p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Số lượng: </strong>{{ $promotion->quantity }}</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Giới hạn sử dụng:</strong>{{ $promotion->usage_limit }}</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Điểm yêu cầu: </strong>{{ $promotion->required_point ?? 0 }}</p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Ngày bắt đầu: </strong>{{ \Carbon\Carbon::parse($promotion->start_date)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="col-md-6">

                    <p> <strong>Ngày kết thúc: </strong>{{ \Carbon\Carbon::parse($promotion->end_date)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="col-md-12">
                    <p> <strong>Mô tả: </strong>{{ $promotion->description ?? 'Không có mô tả' }}</p>
                </div>

                <div class="col-md-6">
                    <p>
                        <strong>Trạng thái: </strong>
                        <strong class="{{ $promotion->is_active ? 'text-success' : 'text-danger' }}">
                            {{ $promotion->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}
                        </strong>
                    </p>

                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>



@endsection
