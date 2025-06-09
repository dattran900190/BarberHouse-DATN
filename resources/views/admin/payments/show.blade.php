@extends('adminlte::page')

@section('title', 'Chi tiết Thanh toán')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Chi tiết Thanh toán</h3>
        </div>

        <div class="card-body">

            <div class="mb-3"><strong>Mã giao dịch:</strong> {{ $payment->transaction_code ?? 'Không có' }}</div>

            <div class="mb-3"><strong>Số tiền:</strong> {{ number_format($payment->amount, 0, ',', '.') }} VNĐ</div>

            <div class="mb-3"><strong>Phương thức thanh toán:</strong>
                @php
                    $methodColors = [
                        'momo' => 'secondary',
                        'cash' => 'primary',
                    ];
                    $statusTexts = [
                        'momo' => 'Chuyển khoản Momo',
                        'cash' => 'Tiền mặt',
                    ];
                @endphp
                <span class="badge bg-{{ $methodColors[$payment->method] ?? 'secondary' }}">
                    {{ $statusTexts[$payment->method] ?? 'Không xác định' }}
                </span>
            </div>

            @php
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

                $paymentColors = [
                    'unpaid' => 'warning',
                    'paid' => 'success',
                    'refunded' => 'info',
                    'failed' => 'danger',
                ];
                $paymentTexts = [
                    'unpaid' => 'Chưa thanh toán',
                    'paid' => 'Thanh toán thành công',
                    'refunded' => 'Hoàn trả thanh toán',
                    'failed' => 'Thanh toán thất bại',
                ];
            @endphp

            <div class="mb-3"><strong>Trạng thái:</strong>
                <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                    {{ $statusTexts[$payment->status] ?? ucfirst($payment->status) }}
                </span>
            </div>

            <div class="mb-3"><strong>Thời gian thanh toán:</strong>
                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : 'Chưa thanh toán' }}
            </div>

            <div class="mt-4">
                <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning me-2">Sửa</a>
                <a href="{{ route('payments.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a>
            </div>
        </div>
    </div>
    @if ($payment->appointment)
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">Lịch hẹn mã giao dịch {{ $payment->transaction_code ?? 'Không có' }}</h4>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Khách hàng:</strong>
                        {{ $payment->appointment->user->name ?? 'Không xác định' }}</div>
                    <div class="col-md-6"><strong>Thời gian hẹn:</strong>
                        {{ \Carbon\Carbon::parse($payment->appointment->appointment_time)->format('d/m/Y H:i') }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6"><strong>Dịch vụ:</strong>
                        {{ $payment->appointment->service->name ?? 'Không xác định' }}</div>
                    <div class="col-md-6"><strong>Thợ cắt tóc:</strong>
                        {{ $payment->appointment->barber->name ?? 'Không xác định' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6"><strong>Chi nhánh:</strong>
                        {{ $payment->appointment->branch->name ?? 'Không xác định' }}</div>
                    <div class="col-md-6"><strong>Khuyến mãi:</strong>
                        {{ $payment->appointment->promotion->code ?? 'Không áp dụng' }}</div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6"><strong>Trạng thái:</strong>
                        <span class="badge bg-{{ $statusColors[$payment->appointment->status] ?? 'secondary' }}">
                            {{ $statusTexts[$payment->appointment->status] ?? ucfirst($payment->appointment->status) }}
                        </span>
                    </div>
                    <div class="col-md-6"><strong>Ghi chú:</strong> {{ $payment->appointment->note ?? 'Không có' }}</div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('appointments.show', [
                        'appointment' => $payment->appointment->id,
                        'from_payment' => $payment->id,
                        'page_payment' => request('page', 1),
                    ]) }}"
                        class="btn btn-outline-info">
                        Xem chi tiết lịch hẹn
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
