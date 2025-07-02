@extends('adminlte::page')

@section('title', 'Chi tiết Lịch hẹn')

@section('content')
    @php
        $statusColors = [
            'pending' => 'warning',
            'confirmed' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'pending_cancellation' => 'warning',
        ];
        $statusTexts = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'pending_cancellation' => 'Chờ huỷ',
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

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Chi tiết Lịch hẹn</h3>
        </div>

        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6"><strong>Khách hàng:</strong> {{ $appointment->user->name ?? 'Không xác định' }}</div>
                <div class="col-md-6"><strong>Thợ cắt tóc:</strong> {{ $appointment->barber->name ?? 'Không xác định' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Dịch vụ:</strong> {{ $appointment->service->name ?? 'Không xác định' }}</div>
                <div class="col-md-6"><strong>Chi nhánh:</strong> {{ $appointment->branch->name ?? 'Không xác định' }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Thời gian hẹn:</strong>
                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</div>
                <div class="col-md-6">
                    <strong>Trạng thái:</strong>
                    <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                        {{ $statusTexts[$appointment->status] ?? ucfirst($appointment->status) }}
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Thanh toán:</strong>
                    <span class="badge bg-{{ $paymentColors[$appointment->payment_status] ?? 'secondary' }}">
                        {{ $paymentTexts[$appointment->payment_status] ?? ucfirst($appointment->payment_status) }}
                    </span>
                </div>
                <div class="col-md-6"><strong>Khuyến mãi:</strong> {{ $appointment->promotion->code ?? 'Không áp dụng' }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6"><strong>Số tiền thanh toán:</strong>
                    {{ number_format($appointment->total_amount, 0, ',', '.') }} VNĐ</div><br>
                <div class="col-md-6"><strong>Số tiền giảm:</strong>
                    {{ number_format($appointment->discount_amount, 0, ',', '.') }} VNĐ</div>
                <div class="col-md-6"><strong>Ngày tạo:</strong>
                    {{ $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'Không xác định' }}</div>
            </div>

            <div class="mb-3"><strong>Ghi chú:</strong> {{ $appointment->note ?? 'Không có' }}</div>

            <div class="mt-4">
                <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning me-2">Sửa</a>
                @if (request()->has('from_payment'))
                    <a href="{{ route('payments.show', [
                        'payment' => request('from_payment'),
                        'page' => request('page_payment', 1),
                    ]) }}"
                        class="btn btn-secondary">
                        Quay lại Payment
                    </a>
                @else
                    <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">
                        Quay lại
                    </a>
                @endif
            </div>
        </div>
    </div>
    @if ($otherBarberAppointments->count())
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">Lịch hẹn khác của thợ - {{ $appointment->barber->name }}</div>
            <ul class="list-group list-group-flush">
                @foreach ($otherBarberAppointments as $item)
                    <li class="list-group-item">
                        <a href="{{ route('appointments.show', $item->id) }}">
                            {{ $item->user->name ?? 'Không xác định' }} -
                            {{ \Carbon\Carbon::parse($item->appointment_time)->format('d/m/Y H:i') }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">Lịch hẹn khác của thợ - {{ $appointment->barber->name }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item text-muted">Thợ không có lịch hẹn khác</li>
            </ul>
        </div>
    @endif

    @if ($otherUserAppointments->count())
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">Lịch hẹn khác của khách - {{ $appointment->user->name }}</div>
            <ul class="list-group list-group-flush">
                @foreach ($otherUserAppointments as $item)
                    <li class="list-group-item">
                        <a href="{{ route('appointments.show', $item->id) }}">
                            {{ $item->barber->name ?? 'Không xác định' }} -
                            {{ \Carbon\Carbon::parse($item->appointment_time)->format('d/m/Y H:i') }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">Lịch hẹn khác của khách - {{ $appointment->user->name }}</div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item text-muted">Khách không có lịch hẹn khác</li>
            </ul>
        </div>
    @endif
@endsection
