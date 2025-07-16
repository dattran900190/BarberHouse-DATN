@extends('layouts.AdminLayout')

@section('title', 'Lịch làm việc đặc biệt - Chi nhánh: ' . $branch->name)

@section('content')
    <div class="page-header mb-4">
        <h3 class="fw-bold">Lịch Làm Việc Đặc Biệt - Chi Nhánh: {{ $branch->name }}</h3>
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
                <div class="col-md-4">
                    <label for="filter" class="form-label">Lọc theo loại lịch</label>
                    <select name="filter" id="filter" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="off" {{ request('filter') === 'off' ? 'selected' : '' }}>Nghỉ cả ngày</option>
                        <option value="custom" {{ request('filter') === 'custom' ? 'selected' : '' }}>Thay đổi giờ làm
                        </option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Lọc</button>
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
                            <h5 class="mb-0 fw-semibold" style="color: #232b43;">Thợ: {{ $barber->name }}</h5>
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
                                                                    <form
                                                                        action="{{ route('barber_schedules.destroy', $schedule->id) }}"
                                                                        method="POST"
                                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá lịch này?')">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="dropdown-item d-flex align-items-center">
                                                                            <i class="fa fa-trash me-2"></i> Xoá
                                                                        </button>
                                                                    </form>
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
                                <div class="alert alert-secondary mb-0 p-3">Không có lịch đặc biệt nào cho thợ này theo bộ
                                    lọc.</div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-between">
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-outline-secondary ">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                    <a href="{{ route('barber_schedules.createForBranch', $branch->id) }}"
                        class="btn btn-sm btn-outline-success">
                        <i class="fas fa-plus me-1"></i> Chỉnh sửa lịch trình
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
    </style>
@endsection
