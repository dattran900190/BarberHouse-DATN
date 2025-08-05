@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Check-in')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Quản lý Check-in</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/checkins') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ route('checkins.index') }}">Checkin</a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/checkins/' . $checkin->id) }}">Chi tiết Check-in</a>
            </li>
        </ul>
    </div>

    <!-- Card 1: Thông tin Check-in -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết Check-in</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-12">
                    <h4 class="fw-bold mb-3">
                        <i class="fa fa-qrcode me-2 text-primary"></i>
                        Mã Checkin: <strong>{{ $checkin->qr_code_value }}</strong>
                    </h4>

                    <div class="row flex-wrap gap-3 ">
                        <div>
                            <i class="fa fa-toggle-on me-2 text-success"></i>
                            <span class="fw-semibold">Trạng thái:</span>
                            @if ($checkin->is_checked_in)
                                <span class="badge bg-success">Đã Check-in</span>
                            @else
                                <span class="badge bg-warning text-dark">Chưa Check-in</span>
                            @endif
                        </div>
                        <div>
                            <i class="fa fa-calendar-check me-2 text-info"></i>
                            <span class="fw-semibold">Thời gian Checkin:</span>
                            {{ $checkin->checkin_time ? \Carbon\Carbon::parse($checkin->checkin_time)->format('d/m/Y H:i') : '-' }}
                        </div>
                        <div>
                            <i class="fa fa-user me-2 text-primary"></i>
                            <span class="fw-semibold">Khách hàng:</span>
                            {{ optional($checkin->appointment->user)->name ?? '-' }}
                        </div>
                        <div>
                            <i class="fa fa-clock me-2 text-warning"></i>
                            <span class="fw-semibold">Thời gian hẹn:</span>
                            {{ optional($checkin->appointment)->appointment_time ? \Carbon\Carbon::parse($checkin->appointment->appointment_time)->format('d/m/Y H:i') : '-' }}
                        </div>
                        <div>
                            <i class="fa fa-calendar-alt me-2 text-muted"></i>
                            <span class="fw-semibold">Ngày tạo:</span>
                            {{ $checkin->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <i class="fa fa-calendar-check me-2 text-muted"></i>
                            <span class="fw-semibold">Ngày cập nhật:</span>
                            {{ $checkin->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <a href="{{ route('checkins.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>
@endsection
