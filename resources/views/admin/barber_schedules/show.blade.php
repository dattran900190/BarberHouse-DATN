@extends('layouts.AdminLayout')

@section('title', 'Lịch làm việc đặc biệt - Chi nhánh: ' . $branch->name)

@section('content')
    @if (session('success'))
        <div class="alert border-start border-success border-4 alert-dismissible fade show shadow-sm" role="alert">
            <span class="fw-bold text-dark">{{ session('success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert border-start border-danger border-4 alert-dismissible fade show shadow-sm" role="alert">
            <span class="fw-bold text-dark">{{ session('error') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

    <div class="page-header mb-4">
        <h3 class="fw-bold">Lịch Làm Việc Đặc Biệt: {{ $branch->name }}</h3>
        <ul class="breadcrumbs">
            <li class="nav-home"><a href="{{ url('admin/dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Quản lý lịch</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi nhánh: {{ $branch->name }}</a></li>
        </ul>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end mb-4">
                <div class="col-md-6">
                    <label for="filter" class="form-label">Lọc theo loại lịch</label>
                    <select name="filter" id="filter" class="form-select p-3 bg-body" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="off" {{ request('filter') === 'off' ? 'selected' : '' }}>Nghỉ cả ngày</option>
                        <option value="custom" {{ request('filter') === 'custom' ? 'selected' : '' }}>Thay đổi giờ làm
                        </option>
                    </select>
                </div>
            </form>
            @if ($barbers->count())
                @foreach ($barbers as $barber)
                    @php
                        $schedules = $barber->schedules
                            ->filter(fn($s) => $s->status !== 'holiday')
                            ->when(request('filter'), fn($q) => $q->where('status', request('filter')))
                            ->sortBy('schedule_date');
                    @endphp

                    <div class="card mb-4 border">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center"
                            style="border-radius: 12px 12px 0 0;">
                            <h5 class="mb-0 fw-semibold">Thợ: {{ $barber->name }}</h5>
                        </div>

                        <div class="card-body p-0">
                            @if ($schedules->count())
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="stt-col">STT</th>
                                                <th>Ngày</th>
                                                <th>Giờ bắt đầu</th>
                                                <th>Giờ kết thúc</th>
                                                <th>Ghi chú</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($schedules as $index => $schedule)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}
                                                    </td>
                                                    @if ($schedule->status === 'off')
                                                        <td colspan="2" class="text-danger fw-normal">Nghỉ cả ngày</td>
                                                        <td class="text-danger fw-normal">Nghỉ phép cá nhân</td>
                                                    @else
                                                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                        </td>
                                                        <td class="text-warning fw-normal">Thay đổi giờ làm</td>
                                                    @endif
                                                    <td class="text-center align-middle" style="width: 70px;">
                                                        <div class="dropdown d-inline-block">
                                                            <button
                                                                class="btn btn-light btn-sm d-flex align-items-center justify-content-center"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false"
                                                                style="border: 1px solid #ddd; width: 38px; height: 38px; padding: 0;">
                                                                <i class="fa fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <a class="dropdown-item d-flex align-items-center"
                                                                        href="{{ route('barber_schedules.edit', $schedule->id) }}">
                                                                        <i class="fa fa-edit me-2"></i> Sửa
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <button type="button"
                                                                        class="dropdown-item d-flex align-items-center btn-delete-schedule"
                                                                        data-id="{{ $schedule->id }}"
                                                                        data-route="{{ route('barber_schedules.destroy', ':id') }}">
                                                                        <i class="fa fa-trash me-2"></i> Xoá
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="p-2 text-muted small">
                                    * Những ngày khác, {{ $barber->name }} làm việc như bình thường.
                                </div>
                            @else
                                <div class="p-3 mb-5 bg-body rounded text-muted">Không có lịch đặc biệt nào cho thợ này
                                    theo bộ lọc.</div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $barber->schedules->links() }}
                    </div>
                @endforeach

                <div class="d-flex gap-2">
                    <a href="{{ route('barber_schedules.createForBranch', $branch->id) }}"
                        class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Sửa lịch trình
                    </a>
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            @else
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Không có thợ</h5>
                    <p>Chi nhánh này hiện không có thợ nào.</p>
                </div>
                <a href="{{ route('barber_schedules.index') }}" class="btn btn-outline-secondary mt-3">← Quay lại</a>
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        .card-header.bg-white {
            background: #fff !important;
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .table thead th,
        .table td,
        .table th {
            font-weight: 400 !important;
            font-size: 16px;
            color: #232b43;
            border-color: #f0f0f0 !important;
            background: #fafbfc;
        }

        .table thead th {
            background: #f5f7fa !important;
            font-weight: 500 !important;
            font-size: 16px;
            color: #232b43;
        }

        .table-bordered> :not(caption)>*>* {
            border-width: 0 0 1px 0;
        }

        .dropdown-menu .dropdown-item {
            font-size: 15px;
        }

        .text-danger {
            color: #ff6b6b !important;
        }

        .text-warning {
            color: #f59e42 !important;
        }

        .fw-normal {
            font-weight: 400 !important;
        }

        .fw-semibold {
            font-weight: 500 !important;
        }

        .custom-swal-popup {
            border-radius: 8px !important;
            padding: 24px !important;
            font-size: 14px !important;
            width: 400px !important;
            max-width: 90vw !important;
        }
    </style>
@endsection

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to handle SweetAlert actions
            function handleSwalAction({
                selector,
                title,
                text,
                method = 'POST',
                withInput = false,
                inputPlaceholder = '',
                inputValidator = null,
                onSuccess = () => location.reload()
            }) {
                document.querySelectorAll(selector).forEach(button => {
                    button.addEventListener('click', function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        const id = this.getAttribute('data-id');
                        const route = this.getAttribute('data-route');

                        if (!id || !route) {
                            console.error('Missing data-id or data-route attributes');
                            return;
                        }

                        console.log('Delete button clicked:', {
                            id,
                            route
                        }); // Debug log

                        const swalOptions = {
                            title: title,
                            text: text,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Xác nhận',
                            cancelButtonText: 'Hủy',
                            width: '420px',
                            customClass: {
                                popup: 'custom-swal-popup',
                                confirmButton: 'btn btn-danger me-2',
                                cancelButton: 'btn btn-secondary'
                            },
                            buttonsStyling: false,
                            focusCancel: false
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
                                // Show loading
                                Swal.fire({
                                    title: 'Đang xử lý...',
                                    text: 'Vui lòng chờ trong giây lát.',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    },
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });

                                const body = withInput ? JSON.stringify({
                                    input: result.value || ''
                                }) : undefined;

                                const csrfToken = document.querySelector(
                                    'meta[name="csrf-token"]')?.getAttribute('content');

                                if (!csrfToken) {
                                    console.error('CSRF token not found');
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Không tìm thấy CSRF token. Vui lòng tải lại trang.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    return;
                                }

                                // Make request
                                fetch(route.replace(':id', id), {
                                        method: method,
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json'
                                        },
                                        body: body
                                    })
                                    .then(response => {
                                        console.log('Response status:', response
                                            .status); // Debug log

                                        if (!response.ok) {
                                            throw new Error(
                                                `HTTP error! status: ${response.status}`
                                            );
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Response data:',
                                            data); // Debug log

                                        Swal.close();
                                        Swal.fire({
                                            title: data.success ?
                                                'Thành công!' : 'Lỗi!',
                                            text: data.message || (data
                                                .success ?
                                                'Thao tác thành công!' :
                                                'Có lỗi xảy ra!'),
                                            icon: data.success ? 'success' :
                                                'error',
                                            confirmButtonText: 'OK',
                                            customClass: {
                                                popup: 'custom-swal-popup',
                                                confirmButton: data.success ?
                                                    'btn btn-success' :
                                                    'btn btn-danger'
                                            },
                                            buttonsStyling: false
                                        }).then(() => {
                                            if (data.success) {
                                                onSuccess();
                                            }
                                        });
                                    })
                                    .catch(error => {
                                        console.error('Fetch error:',
                                            error); // Debug log

                                        Swal.close();
                                        Swal.fire({
                                            title: 'Lỗi!',
                                            text: 'Đã có lỗi xảy ra: ' + error
                                                .message,
                                            icon: 'error',
                                            confirmButtonText: 'OK',
                                            customClass: {
                                                popup: 'custom-swal-popup',
                                                confirmButton: 'btn btn-danger'
                                            },
                                            buttonsStyling: false
                                        });
                                    });
                            }
                        });
                    });
                });
            }

            // Initialize delete schedule action
            handleSwalAction({
                selector: '.btn-delete-schedule',
                title: 'Xóa lịch làm việc',
                text: 'Bạn có chắc chắn muốn xóa lịch làm việc này? Hành động này không thể hoàn tác.',
                method: 'DELETE',
                onSuccess: () => {
                    setTimeout(() => location.reload(), 500);
                }
            });

            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert:not(.alert-warning)');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
@endsection
