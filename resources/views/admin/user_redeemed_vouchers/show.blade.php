
@extends('layouts.AdminLayout')

@section('title', 'Chi tiết voucher đã đổi')
@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Voucher đã đổi</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="{{ url('admin/dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
       
        
        <li class="nav-item"><a href="{{ url('admin/user_redeemed_vouchers') }}">Danh sách voucher đã đổi</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ url('admin/user_redeemed_vouchers/'.$item->id) }}">Chi tiết voucher đã đổi</a></li>

    </ul>
</div>

    <!-- Card: Thông tin voucher đã đổi -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center">
            
            <h4 class="card-title mb-0">Chi tiết voucher {{ $item->promotion->code ?? 'Không rõ' }} đã đổi</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-12">
                
                    <div class="mb-2">
                        <i class="fa fa-user me-2 text-primary"></i>
                        <span class="fw-semibold">Người dùng:</span>
                        {{ $item->user->name ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-envelope me-2 text-info"></i>
                        <span class="fw-semibold">Email:</span>
                        {{ $item->user->email ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-phone me-2 text-success"></i>
                        <span class="fw-semibold">Số điện thoại:</span>
                        {{ $item->user->phone ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-ticket-alt me-2 text-success"></i>
                        <span class="fw-semibold">Mã voucher:</span>
                        <td>{{ $item->promotion->code ?? 'Không rõ' }}</td>
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-calendar-plus me-2 text-info"></i>
                        <span class="fw-semibold">Ngày nhận:</span>
                        {{ $item->redeemed_at ? \Carbon\Carbon::parse($item->redeemed_at)->format('d/m/Y H:i') : '-' }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-calendar-check me-2 text-warning"></i>
                        <span class="fw-semibold">Ngày sử dụng:</span>
                        {{ $item->used_at ? \Carbon\Carbon::parse($item->used_at)->format('d/m/Y H:i') : '-' }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-info-circle me-2 text-secondary"></i>
                        <span class="fw-semibold">Trạng thái:</span>
                        @if ($item->used_at)
                            <span class="badge bg-success">Đã sử dụng</span>
                        @else
                            <span class="badge bg-secondary">Chưa sử dụng</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title mb-0">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a a href="{{ url('admin/user_redeemed_vouchers') }}"" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

@endsection
 