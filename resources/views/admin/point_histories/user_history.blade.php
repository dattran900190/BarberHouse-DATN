@extends('layouts.AdminLayout')

@section('title', 'Lịch sử điểm - ' . $user->name)

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Lịch sử điểm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Quản lý khách hàng</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('point_histories.index') }}">Lịch sử điểm</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết người dùng</a></li>
        </ul>
    </div>

    <!-- Card: Chi tiết người dùng -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Chi tiết người dùng - {{ $user->name }}</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-4 text-center">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="max-height: 200px; width: 200px; object-fit: cover; border-radius: 10px;">
                    @else
                        <i class="fa fa-user-circle fa-5x text-muted"></i>
                        <p class="mt-2">Không có ảnh</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <i class="fa fa-user me-2 text-muted"></i>
                            <strong>Họ tên:</strong> {{ $user->name }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-envelope me-2 text-primary"></i>
                            <strong>Email:</strong> {{ $user->email }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-phone me-2 text-success"></i>
                            <strong>Số điện thoại:</strong> {{ $user->phone ?? '-' }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-venus-mars me-2 text-info"></i>
                            <strong>Giới tính:</strong>
                            @if ($user->gender === 'male')
                                Nam
                            @elseif ($user->gender === 'female')
                                Nữ
                            @else
                                Không xác định
                            @endif
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-map-marker-alt me-2 text-warning"></i>
                            <strong>Địa chỉ:</strong> {{ $user->address ?? 'Không rõ' }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-coins me-2 text-info"></i>
                            <strong>Điểm hiện tại:</strong>
                            <span class="badge bg-info">{{ $user->points_balance }} điểm</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('point_histories.index', ['page' => request('page', 1)]) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Lịch sử điểm -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Lịch sử điểm</h4>
        </div>
        <div class="card-body">
            @if ($pointHistories->isEmpty())
                <p>Không có lịch sử điểm nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>Điểm</th>
                                <th>Loại</th>
                                <th>Mã liên quan</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pointHistories as $history)
                                <tr class="text-center">
                                    <td>
                                        <span class="{{ $history->type === 'earned' ? 'text-success' : 'text-danger' }}">
                                            {{ $history->type === 'earned' ? '+' : '-' }}{{ abs($history->points) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $history->type === 'earned' ? 'success' : ($history->type === 'redeemed' ? 'danger' : 'secondary') }}">
                                            {{ $history->type === 'earned' ? 'Tích điểm' : ($history->type === 'redeemed' ? 'Đổi điểm' : 'Không xác định') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($history->type === 'earned' && $history->appointment)
                                            {{ $history->appointment->appointment_code }}
                                        @elseif ($history->type === 'redeemed' && $history->promotion)
                                            {{ $history->promotion->code }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $pointHistories->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control-plaintext {
            padding-top: 0.3rem;
        }
    </style>
@endsection
