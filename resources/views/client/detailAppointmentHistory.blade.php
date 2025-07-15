@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết đặt lịch
@endsection

@section('content')
    @php
        $time = \Carbon\Carbon::parse($appointment->appointment_time);
        $formattedTime = $time->format('d/m/Y - H:i');
        $period = $time->format('H') < 12 ? 'Sáng' : 'Chiều tối';
    @endphp
    <main style="padding: 10%">
        <div class="container mt-5">
            <div class="card order-detail shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center border-0">
                    <h3 class="mb-0 fw-bold">Chi tiết đặt lịch - {{ $appointment->appointment_code }}</h3>
                    <div>
                        <strong>Trạng thái:</strong>
                        @if ($appointment->status == 'pending')
                            <span class="status-label status-processing">Đang chờ</span>
                        @elseif ($appointment->status == 'confirmed')
                            <span class="status-label status-confirmed">Đã xác nhận</span>
                        @elseif ($appointment->status == 'cancelled')
                            <span class="status-label status-cancelled">Đã hủy</span>
                        @elseif ($appointment->status == 'completed')
                            <span class="status-label status-completed">Đã hoàn thành</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Họ và tên:</strong> {{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}
                            </p>
                            <p><strong>Số điện thoại:</strong>
                                <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}
                            </p>
                            <p><strong>Email:</strong>
                                <td>{{ $appointment->email ?? ($appointment->user?->email ?? 'N/A') }}
                            </p>
                            <p><strong>Mã đặt lịch:</strong> {{ $appointment->appointment_code }}</p>
                            <p><strong>Thời gian:</strong> {{ $formattedTime }} {{ $period }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Dịch vụ:</strong> {{ $appointment->service?->name ?? 'N/A' }}</p>
                            <p><strong>Thợ:</strong> {{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</p>
                            <p><strong>Chi nhánh:</strong> {{ $appointment->branch?->name ?? 'N/A' }}</p>
                            @if ($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                                <div class="form-group">
                                    <p><strong>Lý do huỷ: </strong>{{ $appointment->cancellation_reason }}

                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if ($appointment->review)
                    <div class="review mt-4">
                        <h5>Đánh giá của bạn:</h5>
                        <div>
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star"
                                    style="color: {{ $i <= $appointment->review->rating ? '#f1c40f' : '#ccc' }}"></i>
                            @endfor
                        </div>
                        <p class="mt-2">{{ $appointment->review->comment }}</p>
                    </div>
                @endif


                <div class="card-footer d-flex justify-content-between align-items-center border-0">
                    <h5 class="fw-bold">Tổng tiền: {{ number_format($appointment->total_amount) }}đ</h5>
                    <div>
                        @if (
                            !($appointment instanceof \App\Models\CancelledAppointment) &&
                                $appointment->status != 'completed' &&
                                $appointment->status != 'cancelled')
                            <a href="#" class="btn btn-outline-danger btn-sm me-2">Hủy đặt lịch</a>
                        @endif
                        <a href="{{ route('client.appointmentHistory') }}" class="btn btn-outline-secondary btn-sm">Quay
                            lại</a>
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
