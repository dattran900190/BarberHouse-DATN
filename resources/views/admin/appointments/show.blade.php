@extends('adminlte::page')

@section('title', 'Chi tiết Lịch hẹn')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Chi tiết Lịch hẹn</h3>
        </div>

        <div class="card-body">
            <p><strong>Khách hàng:</strong> {{ $appointment->user->name ?? 'Không xác định' }}</p>
            <p><strong>Thợ cắt tóc:</strong> {{ $appointment->barber->name ?? 'Không xác định' }}</p>
            <p><strong>Dịch vụ:</strong> {{ $appointment->service->name ?? 'Không xác định' }}</p>
            <p><strong>Chi nhánh:</strong> {{ $appointment->branch->name ?? 'Không xác định' }}</p>

            <p><strong>Thời gian hẹn:</strong> {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</p>

            @php
                $statusColors = [
                    'pending' => 'warning',
                    'confirmed' => 'primary',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                ];
                $paymentColors = [
                    'unpaid' => 'warning',
                    'paid' => 'success',
                    'refunded' => 'info',
                    'failed' => 'danger',
                ];
            @endphp

            <p><strong>Trạng thái:</strong> 
                <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </p>

            <p><strong>Thanh toán:</strong> 
                <span class="badge bg-{{ $paymentColors[$appointment->payment_status] ?? 'secondary' }}">
                    {{ ucfirst($appointment->payment_status) }}
                </span>
            </p>

            <p><strong>Khuyến mãi:</strong> {{ $appointment->promotion->code ?? 'Không áp dụng' }}</p>
            <p><strong>Số tiền giảm:</strong> {{ number_format($appointment->discount_amount, 0, ',', '.') }} VNĐ</p>
            <p><strong>Ghi chú:</strong> {{ $appointment->note ?? 'Không có' }}</p>

            <p><strong>Ngày tạo:</strong> 
                {{ $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'Không xác định' }}
            </p>

            <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
@endsection