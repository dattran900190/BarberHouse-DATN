@extends('adminlte::page')

@section('title', 'Chi tiết điểm thưởng')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Chi tiết lịch sử điểm: {{ $pointHistory->id }}</h3>
            <a href="{{ route('point-histories.index', ['page' => request('page', 1)]) }}"
                class="btn btn-secondary d-flex align-items-center">
                <i class="fas fa-arrow-left"></i>
                <span class="btn-text ms-2">Quay lại danh sách</span>
            </a>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <p><strong>Khách hàng:</strong> {{ $pointHistory->user->name ?? 'Khách vãng lai' }}</p>
                <p><strong>Số điểm:</strong> {{ $pointHistory->points }}</p>
                <p><strong>Loại:</strong>
                    @if ($pointHistory->type === 'earned')
                        <span class="badge badge-success">Tích điểm</span>
                    @elseif ($pointHistory->type === 'redeemed')
                        <span class="badge badge-danger">Đổi điểm</span>
                    @else
                        <span class="badge badge-secondary">Không xác định</span>
                    @endif
                </p>
                <p><strong>Mô tả:</strong> {{ $pointHistory->description ?? '-' }}</p>
                <p><strong>Ngày tạo:</strong> {{ $pointHistory->created_at->format('d/m/Y H:i') }}</p>
            </div>

            @if ($pointHistory->appointment)
                <h4>Thông tin lịch hẹn liên quan:</h4>
                <div class="border p-3 rounded bg-light">
                    <p><strong>Mã lịch hẹn:</strong> {{ $pointHistory->appointment->appointment_code }}</p>
                    <p><strong>Thời gian hẹn:</strong> {{ $pointHistory->appointment->appointment_time }}</p>
                    <p><strong>Dịch vụ:</strong> {{ $pointHistory->appointment->service->name ?? '-' }}</p>
                    <p><strong>Chi nhánh:</strong> {{ $pointHistory->appointment->branch->name ?? '-' }}</p>
                    <p><strong>Barber:</strong> {{ $pointHistory->appointment->barber->name ?? '-' }}</p>
                    <p><strong>Ghi chú:</strong> {{ $pointHistory->appointment->note ?? '-' }}</p>

                    @php
                        $status = $pointHistory->appointment->status;

                        $statusColors = [
                            'pending' => 'warning',
                            'confirmed' => 'primary',
                            'completed' => 'success',
                            'cancelled' => 'danger',
                        ];

                        $statusTexts = [
                            'pending' => 'Chờ xác nhận',
                            'confirmed' => 'Đã xác nhận',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ];

                        $badgeColor = $statusColors[$status] ?? 'secondary';
                        $badgeText = $statusTexts[$status] ?? 'Không xác định';
                    @endphp

                    <p><strong>Trạng thái:</strong>
                        <span class="badge badge-{{ $badgeColor }}">{{ $badgeText }}</span>
                    </p>

                </div>
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection
