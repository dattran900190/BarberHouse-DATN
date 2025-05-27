@extends('adminlte::page')

@section('title', 'Quản lý thanh toán')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách thanh toán</h3>
            <a href="{{ route('appointments.create') }}"
                class="btn btn-success btn-icon-toggle d-flex align-items-center ml-auto">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm thanh toán</span>
            </a>
        </div>

        <div class="card-body">
            <form action="#" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên thanh toán...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Stt</th>
                        <th>Khách hàng</th>
                        <th>Thợ</th>
                        <th>Dịch vụ</th>
                        <th>Chi nhánh</th>
                        <th>Thời gian</th>
                        <th>Trạng thái đặt lịch</th>
                        <th>Trạng thái thanh toán</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($appointments->count())
                        @foreach ($appointments as $index => $appointment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $appointment->user?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->branch?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->appointment_time }}</td>
                                <td>
                                    <span
                                        class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge bg-{{ $appointment->payment_status === 'paid' ? 'success' : 'warning' }}">
                                        {{ ucfirst($appointment->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('appointments.show', $appointment->id) }}"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('appointments.edit', $appointment->id) }}"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá lịch hẹn này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i> Xoá
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không tìm thấy lịch hẹn nào phù hợp.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    {{ $appointments->links() }}
@endsection

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection