@extends('layouts.AdminLayout')

@section('title', 'Quản lý lịch hẹn')

@section('content')
    @if (session('success'))
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    @endif

    @if (session('error'))
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    @endif

    @php
        $statusMap = [
            'completed' => ['class' => 'success', 'text' => 'Hoàn thành'],
            'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
            'cancelled' => ['class' => 'danger', 'text' => 'Đã huỷ'],
            'confirmed' => ['class' => 'primary', 'text' => 'Đã xác nhận'],
        ];

        $paymentMap = [
            'paid' => ['class' => 'success', 'text' => 'Thanh toán thành công'],
            'unpaid' => ['class' => 'warning', 'text' => 'Chưa thanh toán'],
            'failed' => ['class' => 'danger', 'text' => 'Thanh toán thất bại'],
            'refunded' => ['class' => 'danger', 'text' => 'Hoàn trả thanh toán'],
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
                <a href="{{ url('admin/appointments') }}">Đặt lịch</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách đặt lịch</div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('appointments.index') }}" id="searchForm" class="mb-3">
                <div class="position-relative">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên dịch vụ..."
                        class="form-control pe-5" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Tabs -->
            <ul class="nav nav-tabs" id="appointmentTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'pending' ? 'active' : '' }}" id="pending-tab" data-toggle="tab"
                        href="#pending" role="tab">Chưa xác nhận</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'confirmed' ? 'active' : '' }}" id="confirmed-tab" data-toggle="tab"
                        href="#confirmed" role="tab">Đã xác nhận</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'completed' ? 'active' : '' }}" id="completed-tab" data-toggle="tab"
                        href="#completed" role="tab">Đã hoàn thành</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $activeTab == 'cancelled' ? 'active' : '' }}" id="cancelled-tab" data-toggle="tab"
                        href="#cancelled" role="tab">Đã hủy</a>
                </li>
            </ul>

            <div class="tab-content" id="appointmentTabsContent">
                <!-- Tab Chưa xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'pending' ? 'show active' : '' }}" id="pending"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Stt</th>
                                    <th>Mã lịch hẹn</th>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Thợ</th>
                                    <th>Dịch vụ</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái lịch hẹn</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($pendingAppointments->count())
                                    @foreach ($pendingAppointments as $index => $appointment)
                                        <tr>
                                            <td>{{ $pendingAppointments->firstItem() + $index }}</td>
                                            <td>{{ $appointment->appointment_code }}</td>
                                            <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                            <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                            <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                            <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                                            </td>
                                            @php
                                                $statusClass = $statusMap[$appointment->status]['class'] ?? 'secondary';
                                                $statusText = $statusMap[$appointment->status]['text'] ?? 'Không rõ';

                                                $paymentClass =
                                                    $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                                $paymentText =
                                                    $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                            @endphp
                                            <td>
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenu{{ $appointment->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="actionMenu{{ $appointment->id }}">
                                                        <li>
                                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('appointments.edit', $appointment->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-edit me-2"></i> Sửa
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item text-success confirm-btn"
                                                                data-id="{{ $appointment->id }}">
                                                                <i class="fas fa-check me-2"></i> Xác nhận
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có lịch hẹn nào phù hợp.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pendingAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'confirmed' ? 'show active' : '' }}" id="confirmed"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Stt</th>
                                    <th>Mã lịch hẹn</th>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Thợ</th>
                                    <th>Dịch vụ</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái lịch hẹn</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($confirmedAppointments->count())
                                    @foreach ($confirmedAppointments as $index => $appointment)
                                        <tr>
                                            <td>{{ $confirmedAppointments->firstItem() + $index }}</td>
                                            <td>{{ $appointment->appointment_code }}</td>
                                            <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                            <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                            <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                            <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                                            </td>
                                            @php
                                                $statusClass = $statusMap[$appointment->status]['class'] ?? 'secondary';
                                                $statusText = $statusMap[$appointment->status]['text'] ?? 'Không rõ';

                                                $paymentClass =
                                                    $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                                $paymentText =
                                                    $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                            @endphp
                                            <td>
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenuConfirmed{{ $appointment->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="actionMenuConfirmed{{ $appointment->id }}">

                                                        <li>
                                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('appointments.edit', $appointment->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-edit me-2"></i> Sửa
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item text-success complete-btn"
                                                                data-id="{{ $appointment->id }}">
                                                                <i class="fas fa-check-circle me-2"></i> Hoàn thành
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item text-primary no-show-btn"
                                                                data-id="{{ $appointment->id }}">
                                                                <i class="fas fa-user-times me-2"></i> Không đến
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item text-danger cancel-btn"
                                                                data-id="{{ $appointment->id }}">
                                                                <i class="fas fa-times-circle me-2"></i> Hủy
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có lịch hẹn nào phù hợp.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $confirmedAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hoàn thành -->
                <div class="tab-pane fade {{ $activeTab == 'completed' ? 'show active' : '' }}" id="completed"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Stt</th>
                                    <th>Mã lịch hẹn</th>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Thợ</th>
                                    <th>Dịch vụ</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái lịch hẹn</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($completedAppointments->count())
                                    @foreach ($completedAppointments as $index => $appointment)
                                        <tr>
                                            <td>{{ $completedAppointments->firstItem() + $index }}</td>
                                            <td>{{ $appointment->appointment_code }}</td>
                                            <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                            <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                            <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                            <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                                            </td>
                                            @php
                                                $statusClass = $statusMap[$appointment->status]['class'] ?? 'secondary';
                                                $statusText = $statusMap[$appointment->status]['text'] ?? 'Không rõ';

                                                $paymentClass =
                                                    $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                                $paymentText =
                                                    $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                            @endphp
                                            <td>
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenuConfirmed{{ $appointment->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="actionMenuConfirmed{{ $appointment->id }}">

                                                        <li>
                                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có lịch hẹn nào phù hợp.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $completedAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hủy -->
                <div class="tab-pane fade {{ $activeTab == 'cancelled' ? 'show active' : '' }}" id="cancelled"
                    role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Stt</th>
                                    <th>Mã lịch hẹn</th>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th>Thợ</th>
                                    <th>Dịch vụ</th>
                                    <th>Thời gian</th>
                                    <th>Lý do</th>
                                    <th>Trạng thái lịch hẹn</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($cancelledAppointments->count())
                                    @foreach ($cancelledAppointments as $index => $appointment)
                                        <tr>
                                            <td>{{ $cancelledAppointments->firstItem() + $index }}</td>
                                            <td>{{ $appointment->appointment_code }}</td>
                                            <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                            <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                            <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                            <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                                            <td>{{ $appointment->cancellation_reason ?? 'N/A' }}</td>
                                            </td>
                                            @php
                                                $statusClass = $statusMap[$appointment->status]['class'] ?? 'secondary';
                                                $statusText = $statusMap[$appointment->status]['text'] ?? 'Không rõ';

                                                $paymentClass =
                                                    $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                                $paymentText =
                                                    $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                            @endphp
                                            <td>
                                                <span class="badge bg-{{ $statusClass }}">{{ $statusText }}
                                                </span>
                                                {{-- {{ $appointment->cancellation_type == 'no-show' ? 'Không đến' : 'Hủy' }} --}}

                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenuConfirmed{{ $appointment->id }}"
                                                        data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>

                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="actionMenuConfirmed{{ $appointment->id }}">

                                                        <li>
                                                            <a href="{{ route('appointments.show_cancelled', $appointment->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="11" class="text-center text-muted">Không có lịch hẹn nào phù hợp.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $cancelledAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-swal-popup {
            width: 550px !important;
            max-width: 550px !important;
            max-height: 450px !important;
            height: 450px !important;


        }

        /* Tăng kích thước vòng xoáy loading */
        .swal2-loading {
            font-size: 1.5rem !important;
        }

        .custom-swal-popup .swal2-title {
            margin-top: 15px !important;
            font-size: 1.5rem !important;

        }
    </style>

@endsection
@section('js')
    <script>
        document.querySelectorAll('#appointmentTabs a[data-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                const tabName = event.target.getAttribute('data-tab');
                console.log('Tab selected:', tabName);
                document.getElementById('activeTabInput').value = tabName;
            });
        });

        document.getElementById('searchForm').addEventListener('submit', function(event) {
            const tabValue = document.getElementById('activeTabInput').value;
            console.log('Tab value before submit:', tabValue);
        });
    </script>

    <script>
        // Xử lý nút "Xác nhận lịch"
        document.querySelectorAll('.confirm-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Ngăn hành vi mặc định
                const appointmentId = this.getAttribute('data-id');

                // Cửa sổ xác nhận
                Swal.fire({
                    title: 'Xác nhận lịch hẹn',
                    text: 'Bạn có chắc chắn muốn xác nhận lịch hẹn này?',
                    icon: 'question',
                    customClass: {
                        popup: 'custom-swal-popup' // CSS
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy'
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

                        // Gửi yêu cầu AJAX
                        fetch('{{ route('appointments.confirm', ':id') }}'.replace(':id',
                                appointmentId), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
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
                                // Đóng cửa sổ loading
                                Swal.close();

                                if (data.success) {
                                    // Cửa sổ thành công
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup' // CSS
                                        }
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    // Cửa sổ lỗi
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        // width: '400px', // Cùng chiều rộng
                                        customClass: {
                                            popup: 'custom-swal-popup' // CSS
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                // Đóng cửa sổ loading
                                Swal.close();
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

        // Xử lý nút "Hoàn thành"
        document.querySelectorAll('.complete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault(); // Ngăn hành vi mặc định
                const appointmentId = this.getAttribute('data-id');

                Swal.fire({
                    title: 'Hoàn thành lịch hẹn',
                    text: 'Bạn có chắc chắn muốn đánh dấu lịch hẹn này là HOÀN THÀNH?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Hoàn thành',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route('appointments.completed', ':id') }}'.replace(':id',
                                appointmentId), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error(
                                    `HTTP error! Status: ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                Swal.close();
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => location.reload());
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
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

        // Xử lý nút "Đánh dấu No-show"
        document.querySelectorAll('.no-show-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const appointmentId = this.getAttribute('data-id');

                // Cửa sổ nhập lý do no-show (tùy chọn)
                Swal.fire({
                    title: 'Đánh dấu lịch hẹn là No-show',
                    text: 'Vui lòng nhập lý do (tùy chọn)',
                    input: 'textarea',
                    inputPlaceholder: 'Nhập lý do no-show (tối đa 255 ký tự)...',
                    inputAttributes: {
                        'rows': 4
                    },
                    icon: 'warning',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    inputValidator: (value) => {
                        if (value && value.length > 255) {
                            return 'Lý do không được vượt quá 255 ký tự!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Cửa sổ loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            width: '400px',
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Gửi yêu cầu AJAX
                        fetch('{{ route('appointments.no-show', ':id') }}'.replace(':id',
                                appointmentId), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    no_show_reason: result.value ||
                                        'Khách hàng không đến'
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Đóng cửa sổ loading
                                Swal.close();

                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        window.location.href =
                                            '{{ route('appointments.index') }}';
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                // Đóng cửa sổ loading
                                Swal.close();
                                console.error('Lỗi AJAX:', error);
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Đã có lỗi xảy ra: ' + error.message,
                                    icon: 'error',
                                    width: '400px',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            });
                    }
                });
            });
        });

        // Xử lý nút "Đánh dấu cancel"
        document.querySelectorAll('.cancel-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const appointmentId = this.getAttribute('data-id');

                // Cửa sổ nhập lý do cancel (tùy chọn)
                Swal.fire({
                    title: 'Huỷ lịch hẹn',
                    text: 'Vui lòng nhập lý do (tùy chọn)',
                    input: 'textarea',
                    inputPlaceholder: 'Nhập lý do cancel (tối đa 255 ký tự)...',
                    inputAttributes: {
                        'rows': 4
                    },
                    icon: 'warning',
                    width: '400px',
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    inputValidator: (value) => {
                        if (value && value.length > 255) {
                            return 'Lý do không được vượt quá 255 ký tự!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Cửa sổ loading
                        Swal.fire({
                            title: 'Đang xử lý...',
                            text: 'Vui lòng chờ trong giây lát.',
                            allowOutsideClick: false,
                            width: '400px',
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Gửi yêu cầu AJAX
                        fetch('{{ route('appointments.cancel', ':id') }}'.replace(':id',
                                appointmentId), {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    no_show_reason: result.value ||
                                        'Khách hàng không đến'
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Đóng cửa sổ loading
                                Swal.close();

                                if (data.success) {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        window.location.href =
                                            '{{ route('appointments.index') }}';
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: data.message,
                                        icon: 'error',
                                        width: '400px',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                // Đóng cửa sổ loading
                                Swal.close();
                                console.error('Lỗi AJAX:', error);
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: 'Đã có lỗi xảy ra: ' + error.message,
                                    icon: 'error',
                                    width: '400px',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                });
                            });
                    }
                });
            });
        });
    </script>

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hàm chung để xử lý các hành động với SweetAlert và AJAX
            function handleAction(buttonSelector, title, inputPlaceholder, routeName, reasonKey,
                defaultReason, isReasonRequired = false) {
                document.querySelectorAll(buttonSelector).forEach(button => {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        const appointmentId = this.getAttribute('data-id');

                        // Hiển thị cửa sổ SweetAlert để nhập lý do
                        Swal.fire({
                            title: title,
                            text: 'Vui lòng nhập lý do' + (isReasonRequired ?
                                '' : ' (tùy chọn)'),
                            input: 'textarea',
                            inputPlaceholder: inputPlaceholder,
                            inputAttributes: {
                                'rows': 4
                            },
                            icon: 'warning',
                            width: '400px',
                            customClass: {
                                popup: 'custom-swal-popup'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Xác nhận',
                            cancelButtonText: 'Hủy',
                            inputValidator: (value) => {
                                if (isReasonRequired && !value) {
                                    return 'Lý do không được để trống!';
                                }
                                if (value && value.length > 255) {
                                    return 'Lý do không được vượt quá 255 ký tự!';
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Hiển thị cửa sổ loading
                                Swal.fire({
                                    title: 'Đang xử lý...',
                                    text: 'Vui lòng chờ trong giây lát.',
                                    allowOutsideClick: false,
                                    width: '400px',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    },
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                // Gửi yêu cầu AJAX
                                const reason = result.value || defaultReason;
                                fetch('{{ route("' + routeName + '", ':id') }}'
                                        .replace(':id', appointmentId), {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({
                                                [reasonKey]: reason
                                            })
                                        })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(
                                                `HTTP error! Status: ${response.status}`
                                            );
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        Swal.close();
                                        if (data.success) {
                                            Swal.fire({
                                                title: 'Thành công!',
                                                text: data.message,
                                                icon: 'success',
                                                width: '400px',
                                                customClass: {
                                                    popup: 'custom-swal-popup'
                                                }
                                            }).then(() => {
                                                window.location
                                                    .href =
                                                    '{{ route('appointments.index') }}';
                                            });
                                        } else {
                                            Swal.fire({
                                                title: 'Lỗi!',
                                                text: data.message,
                                                icon: 'error',
                                                width: '400px',
                                                customClass: {
                                                    popup: 'custom-swal-popup'
                                                }
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        Swal.close();
                                        console.error('Lỗi AJAX:', error);
                                        Swal.fire({
                                            title: 'Lỗi!',
                                            text: 'Đã có lỗi xảy ra: ' +
                                                error.message,
                                            icon: 'error',
                                            width: '400px',
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

            // Áp dụng hàm cho từng nút
            handleAction('.no-show-btn', 'Đánh dấu lịch hẹn là No-show',
                'Nhập lý do no-show (tối đa 255 ký tự)...', 'appointments.no-show',
                'no_show_reason', 'Khách hàng không đến', false);
            handleAction('.cancel-btn', 'Hủy lịch hẹn', 'Nhập lý do hủy (tối đa 255 ký tự)...',
                'appointments.cancel', 'cancellation_reason', 'Hủy bởi admin', false);
        });
    </script> --}}
@endsection
