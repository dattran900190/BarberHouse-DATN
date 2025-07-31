
@extends('layouts.AdminLayout')

@section('title', 'Chi tiết voucher đã đổi')
@section('content')
<div class="page-header">
    <h3 class="fw-bold mb-3">Chi tiết voucher đã đổi</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="{{ url('admin/dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ url('admin/user_redeemed_vouchers') }}">Danh sách người dùng đã đổi voucher</a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item"><a href="{{ url('admin/user_redeemed_vouchers/'.$user->id) }}">Chi tiết voucher của {{ $user->name }}</a></li>
    </ul>
</div>

    <!-- Card: Thông tin người dùng -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title mb-0">Thông tin người dùng</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <i class="fa fa-user me-2 text-primary"></i>
                        <span class="fw-semibold">Tên:</span>
                        {{ $user->name }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-envelope me-2 text-info"></i>
                        <span class="fw-semibold">Email:</span>
                        {{ $user->email }}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <i class="fa fa-phone me-2 text-success"></i>
                        <span class="fw-semibold">Số điện thoại:</span>
                        {{ $user->phone ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <i class="fa fa-ticket-alt me-2 text-warning"></i>
                        <span class="fw-semibold">Tổng voucher đã đổi:</span>
                        <span class="badge badge-primary">{{ $user->redeemedVouchers->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Danh sách voucher đã đổi -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title mb-0">Danh sách voucher đã đổi</h4>
        </div>
        <div class="card-body">
            @if($user->redeemedVouchers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>STT</th>
                                <th>Mã voucher</th>
                                <th>Ngày đổi</th>
                                <th>Trạng thái</th>
                                <th>Ngày sử dụng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->redeemedVouchers as $voucher)
                                <tr>
                                    <td>{{ $voucher->id }}</td>
                                    <td>{{ $voucher->promotion->code ?? 'Không rõ' }}</td>
                                    <td>{{ $voucher->redeemed_at ? \Carbon\Carbon::parse($voucher->redeemed_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $voucher->is_used ? 'success' : 'secondary' }}">
                                            {{ $voucher->is_used ? 'Đã dùng' : 'Chưa dùng' }}
                                        </span>
                                    </td>
                                    <td>{{ $voucher->used_at ? \Carbon\Carbon::parse($voucher->used_at)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted">
                    <i class="fa fa-ticket-alt fa-3x mb-3"></i>
                    <p>Người dùng này chưa đổi voucher nào.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title mb-0">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ url('admin/user_redeemed_vouchers') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

@endsection
 