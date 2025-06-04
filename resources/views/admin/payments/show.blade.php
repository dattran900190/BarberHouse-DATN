@extends('adminlte::page')

@section('title', 'Chi tiết Thanh toán')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Chi tiết Thanh toán</h3>
        </div>

        <div class="card-body">

            <div class="mb-3"><strong>Mã giao dịch:</strong> {{ $payment->transaction_code ?? 'Không có' }}</div>

            {{-- <div class="mb-3"><strong>Lịch hẹn:</strong>
                @if ($payment->appointment)
                    <div><strong>Thời gian hẹn:</strong>
                        {{ \Carbon\Carbon::parse($payment->appointment->appointment_time)->format('d/m/Y H:i') }}</div>
                @else
                    <div><strong>Thời gian hẹn:</strong> Không có dữ liệu lịch hẹn</div>
                @endif
            </div> --}}

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

            <div class="mb-3"><strong>Trạng thái:</strong>
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'paid' => 'success',
                        'refunded' => 'info',
                        'failed' => 'danger',
                    ];
                    $statusLabels = [
                        'pending' => 'Chờ xử lý',
                        'paid' => 'Thanh toán thành công',
                        'refunded' => 'Hoàn trả thanh toán',
                        'failed' => 'Thanh toán thất bại',
                    ];
                @endphp
                <span class="badge bg-{{ $statusColors[$payment->status] ?? 'secondary' }}">
                    {{ $statusLabels[$payment->status] ?? ucfirst($payment->status) }}
                </span>
            </div>

            <div class="mb-3"><strong>Thời gian thanh toán:</strong>
                {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : 'Chưa thanh toán' }}
            </div>

            <div class="mt-4">
                <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning me-2">Sửa</a>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
