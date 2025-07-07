@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa lịch hẹn')

@section('content')
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
                <a href="{{ url('admin/dashboard') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/appointments/' . $appointment->id . '/edit') }}">Sửa đặt lịch</a>
            </li>
        </ul>
    </div>


    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Sửa đặt lịch</div>
        </div>

        <div class="card-body">
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="mb-3">
                    <label for="appointment_time" class="form-label">Thời gian hẹn</label>
                    <input type="datetime-local" id="appointment_time" name="appointment_time" class="form-control"
                        value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d\TH:i')) }}">
                    @error('appointment_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                @php
                    $currentStatus = $appointment->status;
                    $currentPaymentStatus = $appointment->payment_status;

                    $statusOptions = [
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ];
                    $paymentOptions = [
                        'unpaid' => 'Chưa thanh toán',
                        'paid' => 'Thanh toán thành công',
                        'refunded' => 'Hoàn trả thanh toán',
                        'failed' => 'Thanh toán thất bại',
                    ];

                    // Khi payment đã 'paid' hoặc 'refunded' hoặc 'failed', khóa select này
                    $paymentLocked = in_array($currentPaymentStatus, ['paid', 'refunded']);
                    // (Nếu bạn vẫn muốn cho ‘failed’ → ‘paid’, thì hãy loại bỏ 'failed' khỏi mảng trên)
                @endphp

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Trạng thái lịch hẹn</label>
                        <select class="form-control" id="status" name="status" {{-- Nếu đã cancelled hoặc completed thì disable toàn bộ --}}
                            @if (in_array($currentStatus, ['completed', 'cancelled'])) disabled @endif>
                            @foreach ($statusOptions as $statusValue => $label)
                                <option value="{{ $statusValue }}"
                                    {{ old('status', $currentStatus) === $statusValue ? 'selected' : '' }}
                                    {{-- Disabled từng option khi cần --}} @if (
                                        // Nếu đang cancelled ⇒ disable mọi option khác
                                        ($currentStatus === 'cancelled' && $statusValue !== 'cancelled') ||
                                            // Nếu đang completed ⇒ disable mọi option khác
                                            ($currentStatus === 'completed' && $statusValue !== 'completed') ||
                                            // Nếu đang confirmed ⇒ disable option 'pending'
                                            ($currentStatus === 'confirmed' && $statusValue === 'pending') ||
                                            // Nếu đang completed/cancelled ⇒ disable 'pending','confirmed'
                                            (in_array($currentStatus, ['completed', 'cancelled']) && in_array($statusValue, ['pending', 'confirmed']))) disabled @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_status" class="form-label">Trạng thái thanh toán</label>
                        <select class="form-control" id="payment_status" name="payment_status"
                            {{ $paymentLocked ? 'disabled' : '' }}>
                            @foreach ($paymentOptions as $payValue => $label)
                                <option value="{{ $payValue }}"
                                    {{ old('payment_status', $currentPaymentStatus) === $payValue ? 'selected' : '' }}
                                    @if (
                                        // Nếu đã refunded ⇒ disable mọi option khác
                                        ($currentPaymentStatus === 'refunded' && $payValue !== 'refunded') ||
                                            // Nếu đã paid ⇒ disable 'unpaid'
                                            ($currentPaymentStatus === 'paid' && $payValue === 'unpaid') ||
                                            // Nếu đã failed ⇒ disable 'unpaid'
                                            ($currentPaymentStatus === 'failed' && $payValue === 'unpaid')) disabled @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @if ($paymentLocked)
                            <input type="hidden" name="payment_status" value="{{ $currentPaymentStatus }}">
                            <small class="text-muted">
                                Thanh toán đã {{ $currentPaymentStatus === 'refunded' ? 'hoàn trả' : 'thành công' }}, không
                                thể
                                thay đổi.
                            </small>
                        @endif

                        @error('payment_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- <button type="submit" class="btn btn-warning update-appointment-btn" data-id="{{ $appointment->id }}">Cập
                    nhật</button>
                <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a> --}}

                <button type="submit" class="btn btn-sm btn-outline-primary update-appointment-btn"
                    data-id="{{ $appointment->id }}">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>

        </div>
    </div>
@endsection

@section('js')
    <script>
        // Xử lý nút "Cập nhật"
        document.querySelectorAll('.update-appointment-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Ngăn hành vi mặc định của form
                const appointmentId = this.getAttribute('data-id');
                const form = this.closest('form');

                // Cửa sổ xác nhận
                Swal.fire({
                    title: 'Xác nhận cập nhật',
                    text: 'Bạn có chắc chắn muốn cập nhật lịch hẹn này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Cập nhật',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'custom-swal-popup' // CSS
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Cửa sổ loading
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

                        // Thu thập dữ liệu từ form
                        const formData = new FormData(form);

                        // Gửi yêu cầu AJAX
                        fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                Swal.close(); // Đóng cửa sổ loading

                                if (data.success) {
                                    // Cửa sổ thành công
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup' // CSS
                                        },
                                    }).then(() => {
                                        window.location.href =
                                            '{{ route('appointments.index') }}?page=' +
                                            formData.get('page');
                                    });
                                } else {
                                    // Cửa sổ lỗi
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup' // CSS
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.close(); // Đóng cửa sổ loading
                                console.error('Lỗi AJAX:', error);
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Đã có lỗi xảy ra: ' + error.message,
                                    icon: 'error',
                                    customClass: {
                                        popup: 'custom-swal-popup' // CSS
                                    }
                                });
                            });
                    }
                });
            });
        });
    </script>
@endsection
