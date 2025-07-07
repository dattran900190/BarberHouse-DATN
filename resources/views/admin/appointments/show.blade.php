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
                <a href="{{ url('admin/dashboard') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/appointments' . $appointment->id) }}">Chi tiết đặt lịch</a>
            </li>
        </ul>
    </div>


    {{-- <div class="card shadow-sm rounded-2"> --}}
        {{-- <div class="card-header text-white align-items-center">
            <h3 class="card-title">Chi tiết đặt lịch</h3>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <i class="fa fa-user me-2 text-primary"></i>
                    <span class="fw-semibold">Khách hàng:</span> {{ $appointment->user->name ?? 'Không xác định' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-cut me-2 text-primary"></i>
                    <span class="fw-semibold">Thợ:</span> {{ $appointment->barber->name ?? 'Không xác định' }}
                </div>

                <div class="col-md-6">
                    <i class="fas fa-concierge-bell me-2 text-success"></i>
                    <span class="fw-semibold">Dịch vụ:</span> {{ $appointment->service->name ?? 'Không xác định' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-map-marker-alt me-2 text-success"></i>
                    <span class="fw-semibold">Chi nhánh:</span> {{ $appointment->branch->name ?? 'Không xác định' }}
                </div>

                <div class="col-md-6">
                    <i class="fa fa-clock me-2 text-warning"></i>
                    <span class="fw-semibold">Thời gian:</span>
                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-info-circle me-2 text-warning"></i>
                    <span class="fw-semibold">Trạng thái:</span>
                    <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                        {{ $statusTexts[$appointment->status] ?? ucfirst($appointment->status) }}
                    </span>
                </div>

                <div class="col-md-6">
                    <i class="fa fa-wallet me-2 text-info"></i>
                    <span class="fw-semibold">Thanh toán:</span>
                    <span class="badge bg-{{ $paymentColors[$appointment->payment_status] ?? 'secondary' }}">
                        {{ $paymentTexts[$appointment->payment_status] ?? ucfirst($appointment->payment_status) }}
                    </span>
                </div>
                <div class="col-md-6">
                    <i class="fa fa-tag me-2 text-info"></i>
                    <span class="fw-semibold">Khuyến mãi:</span>
                    {{ $appointment->promotion->code ?? 'Không áp dụng' }}
                </div>

                <div class="col-md-6">
                    <i class="fa fa-dollar-sign me-2 text-success"></i>
                    <span class="fw-semibold">Tổng thanh toán:</span>
                    <span class="text-success fw-bold">{{ number_format($appointment->total_amount, 0, ',', '.') }}
                        VNĐ</span>
                </div>
                <div class="col-md-6">
                    <i class="fa fa-percentage me-2 text-success"></i>
                    <span class="fw-semibold">Giảm giá:</span>
                    <span class="text-success">{{ number_format($appointment->discount_amount, 0, ',', '.') }} VNĐ</span>
                </div>

                <div class="col-md-6">
                    <i class="fa fa-calendar-alt me-2 text-muted"></i>
                    <span class="fw-semibold">Ngày tạo:</span>
                    {{ $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'Không xác định' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-barcode me-2 text-muted"></i>
                    <span class="fw-semibold">Mã lịch hẹn:</span> {{ $appointment->appointment_code ?? 'Không có' }}
                </div>

                <div class="col-md-6">
                    <i class="fa fa-sticky-note me-2 text-muted"></i>
                    <span class="fw-semibold">Ghi chú:</span> {{ $appointment->note ?? 'Không có' }}
                </div>

                <!-- Bổ sung các trường cho lịch hủy -->
                @if ($isCancelled)
                    <div class="col-md-6">
                        <i class="fa fa-times-circle me-2 text-danger"></i>
                        <span class="fw-semibold">Lý do hủy:</span> {{ $appointment->cancellation_reason ?? 'Không có' }}
                    </div>
                    <div class="col-md-6">
                        <i class="fa fa-info-circle me-2 text-danger"></i>
                        <span class="fw-semibold">Loại hủy:</span> {{ $appointment->cancellation_type ?? 'Không có' }}
                    </div>
                    <div class="col-md-6">
                        <i class="fa fa-history me-2 text-muted"></i>
                        Trạng thái trước khi hủy: <span
                            class="badge bg-{{ $statusColors[$appointment->status_before_cancellation] ?? 'secondary' }}">
                            {{ $statusTexts[$appointment->status_before_cancellation] ?? ucfirst($appointment->status) }}
                        </span>
                    </div>
                @endif
            </div>

            <div class="mt-4 d-flex">
                <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div> --}}
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
                    <div class="col-md-6"><i class="fa fa-barcode me-2 text-muted"></i><strong>Mã lịch hẹn:</strong>
                        {{ $appointment->appointment_code ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fa fa-cut me-2 text-success"></i><strong>Thợ:</strong>
                        {{ $appointment->barber->name ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fas fa-concierge-bell me-2 text-success"></i><strong>Dịch vụ:</strong>
                        {{ $appointment->service->name ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fa fa-map-marker-alt me-2 text-success"></i><strong>Chi nhánh:</strong>
                        {{ $appointment->branch->name ?? 'N/A' }}</div>
                    <div class="col-md-6"><i class="fa fa-clock me-2 text-warning"></i><strong>Thời gian:</strong>
                        {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</div>
                    <div class="col-md-6"><i class="fa fa-info-circle me-2 text-warning"></i><strong>Trạng thái:</strong>
                        <span
                            class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">{{ $statusTexts[$appointment->status] ?? ucfirst($appointment->status) }}</span>
                    </div>
                    <div class="col-md-6"><i class="fa fa-wallet me-2 text-info"></i><strong>Thanh toán:</strong>
                        <span
                            class="badge bg-{{ $paymentColors[$appointment->payment_status] ?? 'secondary' }}">{{ $paymentTexts[$appointment->payment_status] ?? ucfirst($通告->payment_status) }}</span>
                    </div>
                    <div class="col-md-6"><i class="fa fa-dollar-sign me-2 text-success"></i><strong>Tổng thanh
                            toán:</strong> {{ number_format($appointment->total_amount, 0, ',', '.') }} VNĐ</div>
                    <div class="col-md-6"><i class="fa fa-percentage me-2 text-success"></i><strong>Giảm giá:</strong>
                        {{ number_format($appointment->discount_amount, 0, ',', '.') }} VNĐ</div>
                    <div class="col-md-6"><i class="fa fa-calendar-alt me-2 text-muted"></i><strong>Ngày tạo:</strong>
                        {{ $appointment->created_at ? $appointment->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
                    <div class="col-md-12"><i class="fa fa-sticky-note me-2 text-muted"></i><strong>Ghi chú:</strong>
                        {{ $appointment->note ?? 'N/A' }}</div>
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
                    @if (!$isCancelled)
                        <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-outline-primary btn-sm"><i
                                class="fa fa-edit me-1"></i> Sửa</a>
                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal"><i
                                class="fa fa-times me-1"></i> Hủy lịch</button>
                    @endif
                    <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-outline-secondary btn-sm"><i class="fa fa-arrow-left me-1"></i> Quay lại</a>
                    <button class="btn btn-outline-success btn-sm" onclick="exportPDF()"><i class="fa fa-file-pdf me-1"></i> Xuất
                        PDF</button>
                    <button class="btn btn-outline-info btn-sm" onclick="resendEmail()"><i class="fa fa-envelope me-1"></i> Gửi lại
                        email</button>
                </div>
            </div>
        </div>

        <!-- Modal cho hủy lịch -->
        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">Xác nhận hủy lịch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc muốn hủy lịch hẹn <strong>{{ $appointment->appointment_code }}</strong>?</p>
                        <div class="mb-3">
                            <label for="cancelReason" class="form-label">Lý do hủy</label>
                            <textarea class="form-control" id="cancelReason" name="no_show_reason" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-outline-danger" onclick="submitCancel()">Xác nhận</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- </div> --}}


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
    function exportPDF() {
    // Logic để xuất PDF, sử dụng thư viện như dompdf hoặc jsPDF
    alert('Chức năng xuất PDF đang được phát triển!');
}

function resendEmail() {
    // Gửi AJAX request để gửi lại email
    fetch('{{ route('appointments.resendEmail', $appointment->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => alert(data.message))
      .catch(error => alert('Lỗi: ' + error.message));
}

function submitCancel() {
    const reason = document.getElementById('cancelReason').value;
    fetch('{{ route('appointments.cancel', $appointment->id) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ no_show_reason: reason })
    }).then(response => response.json())
      .then(data => {
          alert(data.message);
          if (data.success) {
              window.location.reload();
          }
      })
      .catch(error => alert('Lỗi: ' + error.message));
}
</script>