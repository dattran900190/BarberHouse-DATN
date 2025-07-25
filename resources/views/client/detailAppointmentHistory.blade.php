@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đặt lịch
@endsection

@section('content')
    @php
        $time = \Carbon\Carbon::parse($appointment->appointment_time);
        $formattedTime = $time->format('d/m/Y - H:i');
        $period = $time->format('H') < 12 ? 'Sáng' : 'Chiều tối';

        $statusLabels = [
            '' => 'Tất cả',
            'completed' => 'Đã hoàn thành',
            'pending' => 'Đang chờ',
            'progress' => 'Đang làm tóc',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
        ];
    @endphp
    <main style="padding: 10%">
        <div class="modal-body">
            <div class="card mb-0">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-sm-flex">
                                <div class="flex-grow-1">
                                    {{-- Logo công ty --}}
                                    <img src="{{ asset('storage/' . ($imageSettings['black_logo'] ?? 'default-images/black_logo.png')) }}" class="card-logo card-logo-dark"
                                        alt="logo tối" height="56">

                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">

                                    <h6><span class="text-muted fw-normal">Email:</span> {{ $appointment->email }}
                                    </h6>

                                    <h6 class="mb-0"><span class="text-muted fw-normal">Điện thoại:</span>
                                        {{ $appointment->phone }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Mã đặt lịch</p>
                                    <h5 class="fs-15 mb-0">{{ $appointment->appointment_code }}</h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Ngày đặt</p>
                                    <h5 class="fs-15 mb-0">{{ $appointment->created_at->format('d/m/Y') }} <small
                                            class="text-muted">{{ $appointment->created_at->format('h:ia') }}</small></h5>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Trạng thái</p>
                                    <span class="status-label status-{{ $appointment->status }}">
                                        {{ $statusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
                                    </span>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <p class="text-muted mb-2 text-uppercase fw-semibold fs-14">Tổng cộng</p>
                                    <h5 class="fs-15 mb-0">{{ number_format($appointment->total_amount, 0, ',', '.') }} VNĐ
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4 border-top border-top-dashed">
                            <div class="row g-3">
                                <div class="col-6">
                                    <h6 class="text-muted text-uppercase fw-semibold fs-15 mb-3">Thông tin chi tiết</h6>
                                    <p class="text-muted mb-1">Chi nhánh: <span class="fw-medium">
                                            {{ $appointment->branch->name }}
                                        </span></p>
                                    <p class="text-muted mb-1">Điện thoại: <span class="fw-medium">
                                            {{ $appointment->phone }}
                                        </span></p>
                                    <p class="text-muted mb-1">Thợ: <span class="fw-medium">
                                            {{ $appointment->barber->name }}
                                        </span></p>
                                    @if ($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                                        <p class="text-muted mb-1">Lý do huỷ: <span
                                                class="fw-medium">{{ $appointment->cancellation_reason }}</span></p>
                                    @endif
                                    <p class="text-muted mb-1">Thời gian lịch hẹn:
                                        <span class="fw-medium">
                                            {{ $formattedTime }} {{ $period }}
                                        </span>
                                    </p>
                                    <p class="text-muted mb-1">Dịch vụ: <span class="fw-medium">
                                            {{ $appointment->service->name }}
                                        </span></p>
                                    @if ($additionalServices->isNotEmpty())
                                        <p class="text-muted mb-1">Dịch vụ bổ xung: <span class="fw-medium">
                                                <ul class="m-2 mt-1 ps-3">
                                                    @foreach ($additionalServices as $service)
                                                        <li>
                                                            {{ $service->name }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </span></p>
                                    @endif

                                </div>

                                <div class="col-6">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Chi tiết thanh toán</h6>
                                    <p class="text-muted mb-1">Phương thức thanh toán: <span
                                            class="fw-medium">{{ $appointment->payment_method === 'cash' ? 'Thanh toán tại tiệm' : 'Thanh toán vnpay' }}</span>
                                    </p>

                                    <p class="text-muted">Tổng cộng: <span
                                            class="fw-medium">{{ number_format($appointment->total_amount, 0, ',', '.') }}
                                            VNĐ</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="alert alert-light">
                                    <p class="mb-0"><span class="fw-semibold">GHI CHÚ:</span>
                                        {{ $appointment->notes ?? 'Không có ghi chú' }}</p>
                                </div>
                            </div>
                            <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                @if (
                                    !($appointment instanceof \App\Models\CancelledAppointment) &&
                                        $appointment->status != 'completed' &&
                                        $appointment->status != 'cancelled')
                                    <a href="#" class="btn btn-outline-danger btn-sm me-2">Hủy đặt lịch</a>
                                @endif
                                <a href="{{ route('client.appointmentHistory') }}"
                                    class="btn btn-outline-secondary btn-sm">Quay
                                    lại</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
@endsection
