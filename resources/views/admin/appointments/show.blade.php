@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Lịch hẹn')

@section('content')
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

    <div class="page-header">
        <h3 class="fw-bold mb-3">Đặt lịch cắt tóc</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/appointments') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/appointments/' . $appointment->id) }}">Chi tiết đặt lịch</a>
            </li>
        </ul>
    </div>

    <!-- Card 1: Thông tin khách hàng -->
    <div class="card shadow-sm mb-4">

        <div class="card-header text-white align-items-center">
            <h3 class="card-title">Thông tin khách hàng</h3>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6"><i class="fa fa-user me-2 text-primary"></i><strong>Tên:</strong>
                    {{ $appointment->user->name ?? 'N/A' }}</div>
                <div class="col-md-6"><i class="fa fa-phone me-2 text-primary"></i><strong>SĐT:</strong>
                    {{ $appointment->phone ?? 'N/A' }}</div>
                <div class="col-md-6"><i class="fa fa-envelope me-2 text-primary"></i><strong>Email:</strong>
                    {{ $appointment->email ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Card 2: Chi tiết lịch hẹn -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white align-items-center">
            <h3 class="card-title">Chi tiết đặt lịch</h3>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                {{-- Dòng 1 --}}
                <div class="col-md-6">
                    <i class="fa fa-barcode me-2 text-muted"></i>
                    <strong>Mã lịch hẹn:</strong> {{ $appointment->appointment_code ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-cut me-2 text-success"></i>
                    <strong>Thợ:</strong> {{ $appointment->barber->name ?? 'N/A' }}
                </div>

                {{-- Dòng 2 --}}
                <div class="col-md-6">
                    <i class="fa fa-map-marker-alt me-2 text-success"></i>
                    <strong>Chi nhánh:</strong> {{ $appointment->branch->name ?? 'N/A' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-clock me-2 text-warning"></i>
                    <strong>Thời gian:</strong>
                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                </div>

                {{-- Dòng 3 --}}
                <div class="col-md-6">
                    <i class="fa fa-info-circle me-2 text-warning"></i>
                    <strong>Trạng thái:</strong>
                    <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                        {{ $statusTexts[$appointment->status] ?? ucfirst($appointment->status) }}
                    </span>
                </div>
                <div class="col-md-6">
                    <i class="fa fa-wallet me-2 text-info"></i>
                    <strong>Thanh toán:</strong>
                    <span class="badge bg-{{ $paymentColors[$appointment->payment_status] ?? 'secondary' }}">
                        {{ $paymentTexts[$appointment->payment_status] ?? ucfirst($appointment->payment_status) }}
                    </span>
                </div>

                {{-- Dòng 4 --}}
                <div class="col-md-6">
                    <i class="fas fa-money-check-alt me-2 text-success"></i>
                    <strong>Phương thức thanh toán:</strong>
                    {{ $appointment->payment_method === 'cash' ? 'Thanh toán tại tiệm' : 'Thanh toán VNPAY' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-percentage me-2 text-success"></i>
                    <strong>Giảm giá:</strong> {{ number_format($appointment->discount_amount, 0, ',', '.') }} VNĐ
                </div>

                {{-- Dòng 5 --}}
                <div class="col-md-6">
                    <i class="fa fa-dollar-sign me-2 text-success"></i>
                    <strong>Tổng thanh toán:</strong> {{ number_format($appointment->total_amount, 0, ',', '.') }} VNĐ
                </div>
                <div class="col-md-6">
                    <i class="fa fa-calendar-alt me-2 text-muted"></i>
                    <strong>Ngày tạo:</strong>
                    {{ $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'N/A' }}
                </div>

                {{-- Dòng 6 --}}
                <div class="col-md-6">
                    <i class="fa fa-sticky-note me-2 text-muted"></i>
                    <strong>Ghi chú:</strong> {{ $appointment->note ?? 'N/A' }}
                </div>

                <div class="col-md-6">
                    <i class="far fa-calendar me-2 text-muted"></i>
                    <strong>Thời gian:</strong> {{ $appointment->duration ?? 'N/A' }} phút
                </div>

                <div class="col-md-6">
                    <i class="fa fa-comment-dots me-2 text-primary"></i>
                    <strong>Bình luận:</strong>
                    <span class="text-muted">{{ $review->comment ?? 'Chưa có bình luận' }}</span>

                    @if (isset($review->rating))
                        <div class="mt-2">
                            <i class="fa fa-star me-2 text-warning"></i>
                            <strong>Đánh giá:</strong>
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-secondary' }}"></i>
                            @endfor
                            <span class="ms-2 text-muted">({{ $review->rating }}/5)</span>
                        </div>
                    @endif
                </div>

                {{-- Dòng 7: Dịch vụ --}}
                <div class="col-md-6">
                    <i class="fas fa-concierge-bell me-2 text-success"></i>
                    <strong>Dịch vụ:</strong> {{ $appointment->service->name ?? 'N/A' }}

                    @if ($appointment->service)
                        @if ($appointment->service->trashed())
                            <span class="badge bg-danger ms-1">Đã xoá mềm</span>
                        @endif
                    @else
                        <span class="text-muted">Dịch vụ không tồn tại</span>
                    @endif

                </div>
                <div class="col-md-6">
                    <i class="fas fa-plus-circle text-primary me-2"></i>
                    <strong>Dịch vụ thêm:</strong>
                    @if ($additionalServices->isNotEmpty())
                        <ul class="m-2 mt-1 ps-3">
                            @foreach ($additionalServices as $service)
                                <li>
                                    {{ $service->name }}
                                    @if ($service->trashed())
                                        <span class="badge bg-danger ms-1">Đã xoá mềm</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted fst-italic mt-1">Không có dịch vụ thêm</p>
                    @endif
                </div>
            </div>
        </div>
    </div>



    <!-- Card 3: Thông tin hủy (nếu có) -->
    @if ($isCancelled)
        <div class="card shadow-sm mb-4">

            <div class="card-header text-white align-items-cente">
                <h3 class="card-title">Thông tin hủy</h3>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6"><i class="fa fa-times-circle me-2 text-danger"></i><strong>Lý do hủy:</strong>
                        {{ $appointment->cancellation_reason ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fa fa-info-circle me-2 text-danger"></i><strong>Loại hủy:</strong>
                        {{ $appointment->cancellation_type ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fa fa-exclamation-triangle me-2 text-warning"></i><strong>Lý do từ
                            chối:</strong> {{ $appointment->rejection_reason ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fa fa-history me-2 text-muted"></i><strong>Trạng thái trước khi
                            hủy:</strong> {{ $appointment->status_before_cancellation ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
    @endif

    <!-- Card 4: Lịch sử trạng thái (giả sử có bảng audit) -->
    @if ($appointment->statusHistory && $appointment->statusHistory->count())
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-white">
                <h4 class="mb-0">Lịch sử trạng thái</h4>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($appointment->statusHistory as $history)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $history->status }}</td>
                                <td>{{ $history->note ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Card 5: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white align-items-center">
            <h3 class="card-title">Hành động</h3>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                {{-- nếu là trạng thái thành công hoặc huỷ thì không hiển thị nút sửa và hủy --}}
                @if (!$isCancelled && $appointment->status == 'pending')
                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-outline-primary btn-sm"><i
                            class="fa fa-edit me-1"></i> Sửa</a>
                    <button type="button" class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $appointment->id }}">
                        <i class="fas fa-trash me-2"></i> Hủy lịch
                    </button>
                @endif
                <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}"
                    class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Quay lại</a>
            </div>
        </div>
    </div>


    @if ($otherBarberAppointments->count())
        <div class="card mt-4">
            <div class="card-header text-white align-items-center">
                <h3 class="card-title">Lịch hẹn khác của thợ - {{ $appointment->barber->name }}</h3>
            </div>
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
            <div class="card-header text-white align-items-center">
                <h3 class="card-title">Lịch hẹn khác của thợ - {{ $appointment->barber->name }}</h3>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item text-muted">Thợ không có lịch hẹn khác</li>
            </ul>
        </div>
    @endif

    @if ($otherUserAppointments->count())
        <div class="card mt-4">
            <div class="card-header text-white align-items-center">
                <h3 class="card-title">Lịch hẹn khác của khách - {{ $appointment->user->name }}</h3>
            </div>
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
            <div class="card-header text-white align-items-center">
                <h3 class="card-title">Lịch hẹn khác của khách - {{ $appointment->user->name }}</h3>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item text-muted">Khách không có lịch hẹn khác</li>
            </ul>
        </div>
    @endif

@endsection

<script>
     function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false, // Thêm tùy chọn hiển thị input
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload() // Thay reload bằng callback linh hoạt
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const appointmentId = this.getAttribute('data-id');

                    const swalOptions = {
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    };

                    if (withInput) {
                        swalOptions.input = 'textarea';
                        swalOptions.inputPlaceholder = inputPlaceholder;
                        if (inputValidator) {
                            swalOptions.inputValidator = inputValidator;
                        }
                    }

                    Swal.fire(swalOptions).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup' // CSS
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ? JSON.stringify({
                                no_show_reason: result.value || 'Không có lý do'
                            }) : undefined;

                            fetch(route.replace(':id', appointmentId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) onSuccess();
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }

        handleSwalAction({
            selector: '.cancel-action',
            title: 'Huỷ lịch hẹn',
            text: 'Vui lòng nhập lý do (tùy chọn)',
            route: '{{ route('appointments.cancel', ':id') }}',
            withInput: true,
            inputPlaceholder: 'Nhập lý do cancel (tối đa 255 ký tự)...',
            inputValidator: (value) => {
                if (value && value.length > 255) return 'Lý do không được vượt quá 255 ký tự!';
            },
            onSuccess: () => window.location.href = '{{ route('appointments.index') }}'
        });

</script>
