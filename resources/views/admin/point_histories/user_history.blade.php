@extends('layouts.AdminLayout')

@section('title', 'Lịch sử điểm - ' . $user->name)

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 flex-grow-1 text-center">Lịch sử điểm - {{ $user->name }}</h3>
            <a href="{{ route('point_histories.index', ['page' => request('page', 1)]) }}"
                class="btn btn-secondary btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-arrow-left"></i>
                <span class="btn-text ms-2">Quay lại danh sách</span>
            </a>

        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3 text-center">
                    <img src="{{ $user->avatar ?? asset('default-avatar.png') }}" alt="Avatar"
                        class="img-thumbnail rounded-circle" style="width: 120px; height: 120px;">
                </div>
                <div class="col-md-9">
                    <div class="mb-2">
                        <strong>👤 Họ tên:</strong> {{ $user->name }}
                    </div>
                    <div class="mb-2">
                        <strong>📧 Email:</strong> {{ $user->email }}
                    </div>
                    <div class="mb-2">
                        <strong>📞 Số điện thoại:</strong> {{ $user->phone ?? '-' }}
                    </div>
                    <div class="mb-2">
                        <strong>⚧ Giới tính:</strong>
                        @if ($user->gender === 'male')
                            Nam
                        @elseif ($user->gender === 'female')
                            Nữ
                        @else
                            Không xác định
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>🏠 Địa chỉ:</strong> {{ $user->address ?? '-' }}
                    </div>
                    <div class="mb-2">
                        <strong>🎯 Điểm hiện tại:</strong>
                        <span class="badge bg-info">{{ $user->points_balance }} điểm</span>
                    </div>
                </div>
            </div>

            <hr>

            <h4 class="text-primary">Lịch sử điểm</h4>

            @if ($pointHistories->isEmpty())
                <p>Không có lịch sử điểm nào.</p>
            @else
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
                <div class="d-flex mt-3">
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

        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection
