@extends('layouts.AdminLayout')

@section('title', 'Danh sách Chi nhánh')

@php
    $currentRole = Auth::user()->role;
@endphp
@section('content')

    <div class="page-header">
        <h3 class="fw-bold mb-3">Lịch làm việc theo chi nhánh</h3>

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
                <a href="{{ url('admin/barber_schedules') }}">Quản lý lịch làm việc</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/barber_schedules') }}">Lịch làm việc</a>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center rounded-top border-bottom-0"
            style="border-radius: 16px 16px 0 0;">
            <h4 class="mb-0 fw-bold">Danh sách chi nhánh</h4>
            @if ($currentRole == 'admin')
                <a href="{{ route('barber_schedules.createHoliday') }}"
                    class="btn btn-outline-success btn-sm d-flex align-items-center">
                    <i class="fas fa-plus me-1"></i> Tạo lịch nghỉ lễ
                </a>
            @endif
        </div>
        <div class="card-body">
            {{-- Tìm kiếm chi nhánh --}}
            <form action="{{ route('barber_schedules.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên chi nhánh..."
                        value="{{ request()->get('search') }}">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>
            {{-- Thông báo lịch nghỉ lễ --}}
            @php
                $holidays = \App\Models\BarberSchedule::where('status', 'holiday')
                    ->where('holiday_end_date', '>=', now()->toDateString())
                    ->select('holiday_start_date', 'holiday_end_date', 'note')
                    ->groupBy('holiday_start_date', 'holiday_end_date', 'note')
                    ->orderBy('holiday_start_date')
                    ->get();
            @endphp

            @if ($holidays->count())
                <div class="alert alert-warning shadow-sm mb-4" style="border-left: 4px solid #f59e42;">
                    <h5 class="mb-3 text-danger"><i class="fas fa-bell"></i> Thông báo nghỉ lễ</h5>
                    <ul class="list-unstyled mb-0">
                        @foreach ($holidays as $holiday)
                            @php
                                $first = \App\Models\BarberSchedule::where(
                                    'holiday_start_date',
                                    $holiday->holiday_start_date,
                                )
                                    ->where('holiday_end_date', $holiday->holiday_end_date)
                                    ->where('note', $holiday->note)
                                    ->first();
                            @endphp
                            <li
                                class="d-flex justify-content-between align-items-center border border-warning p-2 mb-2 rounded bg-light holiday-row">
                                <span>
                                    <strong>{{ $holiday->note }}</strong>:
                                    {{ \Carbon\Carbon::parse($holiday->holiday_start_date)->format('d/m/Y') }} -
                                    {{ \Carbon\Carbon::parse($holiday->holiday_end_date)->format('d/m/Y') }}
                                </span>
                                @if ($first)
                                    <div class="action-buttons" style="display: flex; gap: 0.5rem;">
                                        <a href="{{ route('barber_schedules.editHoliday', $first->id) }}"
                                            class="btn btn-sm btn-warning text-white" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="delete-form-{{ $first->id }}"
                                            action="{{ route('barber_schedules.deleteHoliday', $first->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger btn-delete-holiday"
                                                data-id="{{ $first->id }}"
                                                data-route="{{ route('barber_schedules.deleteHoliday', ':id') }}"
                                                title="Xoá">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>

                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Danh sách chi nhánh --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="fw-semibold">Tên chi nhánh</th>
                            <th class="fw-semibold">Địa chỉ</th>
                            <th class="fw-semibold">Điện thoại</th>
                            <th class="fw-semibold text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $branch)
                            <tr>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->phone }}</td>
                                <td class="text-center align-middle" style="width: 70px;">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item d-flex align-items-center"
                                                    href="{{ route('barber_schedules.showBranch', $branch->id) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem lịch
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Không tìm thấy chi nhánh nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-3">
        {{ $branches->links() }}
    </div>
@endsection

@section('css')
    <style>
        .holiday-row .action-buttons {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s;
        }

        .holiday-row:hover .action-buttons {
            opacity: 1;
            pointer-events: auto;
        }

        .holiday-row .action-buttons .btn {
            min-width: 36px;
            height: 36px;
            padding: 6px 8px;
            font-size: 13px;
        }

        .uniform-btn {
            min-width: 110px;
        }

        .card-header.bg-white {
            background: #fff !important;
            border-radius: 16px 16px 0 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .card-header .btn {
            font-weight: 500;
            border-radius: 8px;
        }

        .card-header .btn-outline-success {
            color: #198754;
            border-color: #198754;
        }

        .card-header .btn-outline-success:hover {
            background: #198754;
            color: #fff;
        }

        /* Thêm viền cho bảng */
        .table {
            border: 1px solid #e5e7eb !important;
            border-radius: 12px;
            overflow: hidden;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb !important;
        }

        .table thead th {
            border-bottom: none;
            background: transparent;
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .page-header {
            background: transparent;
            padding: 0 0 16px 0;
        }

        .breadcrumbs {
            background: transparent;
            padding-left: 0;
            margin-bottom: 0;
        }

        .breadcrumbs li {
            display: inline-block;
            color: #232b43;
            font-size: 15px;
        }

        .breadcrumbs .icon-home,
        .breadcrumbs .icon-arrow-right {
            font-size: 16px;
            vertical-align: middle;
        }

        .breadcrumbs .separator {
            margin: 0 6px;
        }

        .custom-swal-popup {
            border-radius: 1rem !important;
            padding: 1.5rem;
            font-size: 15px;
        }
    </style>
@endsection
@section('js')
    <script>
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

                    const id = this.getAttribute('data-id');
                    const route = this.getAttribute('data-route');

                    const swalOptions = {
                        title: title,
                        text: text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Huỷ',
                        width: '420px',
                        customClass: {
                            popup: 'custom-swal-popup',
                            confirmButton: 'btn btn-danger me-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
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

                            fetch(route.replace(':id', id), {
                                    method: method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    },
                                    body: body
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
                                        },
                                        confirmButtonText: 'OK',
                                        buttonsStyling: false,
                                        customClass: {
                                            confirmButton: 'btn btn-primary'
                                        }
                                    }).then(() => {
                                        if (data.success) {
                                            onSuccess();
                                        }
                                    });
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        },
                                        confirmButtonText: 'OK',
                                        buttonsStyling: false,
                                        customClass: {
                                            confirmButton: 'btn btn-danger'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }
        handleSwalAction({
            selector: '.btn-delete-holiday',
            title: 'Bạn có chắc chắn muốn xoá lịch nghỉ lễ này?',
            text: 'Hành động này sẽ xoá toàn bộ lịch nghỉ lễ giống nhau trong hệ thống.',
            route: '{{ route('barber_schedules.deleteHoliday', ':id') }}',
            method: 'DELETE',
        });
    </script>
@endsection
