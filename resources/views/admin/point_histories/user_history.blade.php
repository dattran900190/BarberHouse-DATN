@extends('layouts.AdminLayout')

@section('title', 'Lịch sử điểm - ' . $user->name)

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Lịch sử điểm</h3>
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

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chi tiết người dùng - {{ $user->name }}</div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <!-- Cột ảnh -->
                <div class="col-md-4 text-center mb-3">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}" alt="Avatar" class="img-fluid rounded" style="max-height: 300px;">
                    @else
                        <p>Không có ảnh</p>
                    @endif
                </div>

                <!-- Cột thông tin -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Họ tên: <span class="text-black">{{ $user->name }}</span></label>
                        <div class="form-control-plaintext"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email: <span class="text-black">{{ $user->email }}</span></label>
                        <div class="form-control-plaintext"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại: <span
                                class="text-black">{{ $user->phone ?? '-' }}</span></label>
                        <div class="form-control-plaintext"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giới tính: <span class="text-black">
                                @if ($user->gender === 'male')
                                    Nam
                                @elseif ($user->gender === 'female')
                                    Nữ
                                @else
                                    Không xác định
                                @endif
                            </span>
                        </label>
                        <div class="form-control-plaintext">

                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ: <span
                                class="text-black">{{ $user->address ?? 'không rõ' }}</span></label>
                        <div class="form-control-plaintext"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Điểm hiện tại: <span class="badge bg-info">{{ $user->points_balance }}
                                điểm</span></label>
                        <div class="form-control-plaintext">

                        </div>
                    </div>

                    <a href="{{ route('point_histories.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>

            <hr>

            <h4 class="text-primary">Lịch sử điểm</h4>

            @if ($pointHistories->isEmpty())
                <p>Không có lịch sử điểm nào.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light text-center">
                            <tr>
                                <th>Điểm</th>
                                <th>Loại</th>
                                <th>Mã đặt lịch / Khuyến mãi</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pointHistories as $history)
                                <tr class="text-center">
                                    <td class="{{ $history->type === 'earned' ? 'text-success' : 'text-danger' }}">
                                        {{ $history->type === 'earned' ? '+' : '-' }}{{ abs($history->points) }}
                                    </td>
                                    <td>
                                        @if ($history->type === 'earned')
                                            <span class="badge bg-success">Tích điểm</span>
                                        @elseif ($history->type === 'redeemed')
                                            <span class="badge bg-danger">Đổi điểm</span>
                                        @else
                                            <span class="badge bg-secondary">Không xác định</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($history->type === 'earned' && $history->appointment)
                                            {{ $history->appointment->appointment_code ?? 'Không rõ' }}
                                        @elseif ($history->type === 'redeemed' && $history->promotion)
                                            {{ $history->promotion->code ?? 'Không rõ' }}
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
