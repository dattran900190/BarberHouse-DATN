@extends('adminlte::page')

@section('title', 'Quản lý lịch hẹn')

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
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách lịch hẹn</h3>
        </div>

        <div class="card-body">
<<<<<<< HEAD
            <form action="#" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên thanh toán...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Stt</th>
                            <th>Mã lịch hẹn</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Thợ</th>
                            <th>Dịch vụ</th>
                            <th>Chi nhánh</th>
                            <th>Thời gian</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái lịch hẹn</th>
                            <th>Trạng thái thanh toán</th>
                            <th class="text-center">Hành động</th>
=======
            <h4>Lịch hẹn chưa xác nhận</h4>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Stt</th>
                        <th>Mã lịch hẹn</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Thợ</th>
                        <th>Dịch vụ</th>
                        <th>Chi nhánh</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái lịch hẹn</th>
                        <th>Trạng thái thanh toán</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($pendingAppointments->count())
                        @foreach ($pendingAppointments as $index => $appointment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $appointment->appointment_code }}</td>
                                <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                <td>{{ $appointment->email ?? ($appointment->user?->email ?? 'N/A') }}</td>
                                <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->branch?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->appointment_time }}</td>
                                <td>{{ $appointment->total_amount }}</td>
                                <td><span class="badge bg-warning">Chờ xác nhận</span></td>
                                <td>
                                    <span class="badge bg-{{ $appointment->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ $appointment->payment_status == 'paid' ? 'Thanh toán thành công' : 'Chưa thanh toán' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('appointments.confirm', $appointment->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
                                    </form>
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info btn-sm">Xem</a>
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn huỷ lịch hẹn này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Huỷ</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="13" class="text-center text-muted">Không có lịch hẹn chưa xác nhận.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <h4>Lịch hẹn đã xác nhận</h4>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Stt</th>
                        <th>Mã lịch hẹn</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Thợ</th>
                        <th>Dịch vụ</th>
                        <th>Chi nhánh</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái lịch hẹn</th>
                        <th>Trạng thái thanh toán</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($confirmedAppointments->count())
                        @foreach ($confirmedAppointments as $index => $appointment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $appointment->appointment_code }}</td>
                                <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                <td>{{ $appointment->email ?? ($appointment->user?->email ?? 'N/A') }}</td>
                                <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->branch?->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->appointment_time }}</td>
                                <td>{{ $appointment->total_amount }}</td>
                                <td><span class="badge bg-primary">Đã xác nhận</span></td>
                                <td>
                                    <span class="badge bg-{{ $appointment->payment_status == 'paid' ? 'success' : 'warning' }}">
                                        {{ $appointment->payment_status == 'paid' ? 'Thanh toán thành công' : 'Chưa thanh toán' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-info btn-sm">Xem</a>
                                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc chắn muốn huỷ lịch hẹn này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Huỷ</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="13" class="text-center text-muted">Không có lịch hẹn đã xác nhận.</td>
>>>>>>> 271ee34643d5e9875a504cdc7abb876c27291375
                        </tr>
                    </thead>
                    <tbody>
                        @if ($appointments->count())
                            @foreach ($appointments as $index => $appointment)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $appointment->appointment_code }}</td>
                                    <td>
                                        {{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}
                                        @if ($appointment->name && $appointment->name !== $appointment->user?->name)
                                            <br>
                                            <small class="text-muted">(Đặt bởi:
                                                {{ $appointment->user?->name ?? 'N/A' }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}
                                        @if ($appointment->phone && $appointment->phone !== $appointment->user?->phone)
                                        @endif
                                    <td>
                                        {{ $appointment->email ?? ($appointment->user?->email ?? 'N/A') }}
                                        @if ($appointment->email && $appointment->email !== $appointment->user?->email)
                                        @endif
                                    <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                    <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->branch?->name ?? 'N/A' }}</td>
                                    <td>{{ $appointment->appointment_time }}</td>
                                    <td>{{ $appointment->total_amount }}</td>
                                    <td>
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
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                                            {{ $statusTexts[$appointment->status] ?? 'Không xác định' }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $paymentColors = [
                                                'unpaid' => 'warning',
                                                'paid' => 'success',
                                                'refunded' => 'info',
                                                'failed' => 'danger',
                                            ];
                                            $statusTexts = [
                                                'unpaid' => 'Chưa thanh toán',
                                                'paid' => 'Thanh toán thành công',
                                                'refunded' => 'Hoàn trả thanh toán',
                                                'failed' => 'Thanh toán thất bại',
                                            ];
                                        @endphp
                                        <span
                                            class="badge bg-{{ $paymentColors[$appointment->payment_status] ?? 'secondary' }}">
                                            {{ $statusTexts[$appointment->payment_status] ?? 'Không xác định' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('appointments.show', ['appointment' => $appointment->id, 'page' => request('page', 1)]) }}"
                                            class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                        <a href="{{ route('appointments.edit', ['appointment' => $appointment->id, 'page' => request('page', 1)]) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn huỷ lịch hẹn này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="fas fa-ban"></i> Huỷ
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
    </div>
@endsection