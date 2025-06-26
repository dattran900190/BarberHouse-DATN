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

    @php
        $statusMap = [
            'completed' => ['class' => 'success', 'text' => 'Hoàn thành'],
            'pending' => ['class' => 'warning', 'text' => 'Chờ xác nhận'],
            'cancelled' => ['class' => 'danger', 'text' => 'Đã huỷ'],
            'confirmed' => ['class' => 'primary', 'text' => 'Đã xác nhận'],
            'pending_cancellation' => ['class' => 'warning', 'text' => 'Chờ huỷ'],
        ];

        $paymentMap = [
            'paid' => ['class' => 'success', 'text' => 'Thanh toán thành công'],
            'unpaid' => ['class' => 'warning', 'text' => 'Chưa thanh toán'],
            'failed' => ['class' => 'danger', 'text' => 'Thanh toán thất bại'],
            'refunded' => ['class' => 'danger', 'text' => 'Hoàn trả thanh toán'],
        ];
    @endphp

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách lịch hẹn</h3>
        </div>
        <div class="card-body">
            <!-- Form tìm kiếm -->
            <form method="GET" action="{{ route('appointments.index') }}" id="searchForm" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm lịch đặt..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <!-- Thông báo nếu không có kết quả -->
            @if ($search && trim($search) && $allAppointments->isEmpty())
                <div class="alert alert-warning">
                    Không tìm thấy lịch hẹn nào khớp với "{{ $search }}".
                </div>
            @endif

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
                    <a class="nav-link {{ $activeTab == 'pending_cancellation' ? 'active' : '' }}"
                        id="pending-cancellation-tab" data-toggle="tab" href="#pending-cancellation" role="tab">Chờ
                        hủy</a>
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

                                            $paymentClass = $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                            $paymentText = $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                        @endphp
                                        <td>
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('appointments.confirm', $appointment->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
                                            </form>
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="btn btn-info btn-sm">Xem</a>
                                            <a href="{{ route('appointments.edit', $appointment->id) }}"
                                                class="btn btn-warning btn-sm">Sửa</a>
                                            {{-- <form action="{{ route('appointments.destroy', $appointment->id) }}"
                                                method="POST" style="display:inline-block;"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn huỷ lịch hẹn này không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Huỷ</button>
                                            </form> --}}
                                            @if (in_array($appointment->status, ['pending', 'confirmed']))
                                                <form action="{{ route('appointments.cancel', $appointment->id) }}"
                                                    method="POST" style="display:inline-block;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?');">
                                                    @csrf

                                                    <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                                                </form>
                                            @endif
                                        </td>
                                        {{-- <td class="text-center">
                                            <form action="{{ route('appointments.confirm', $appointment->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Xác nhận</button>
                                            </form>
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="btn btn-info btn-sm">Xem</a>
                                            <a href="{{ route('appointments.edit', $appointment->id) }}"
                                                class="btn btn-warning btn-sm">Sửa</a>
                                            <!-- Nút kích hoạt modal hủy -->
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                                data-target="#cancelModal{{ $appointment->id }}">Huỷ</button>

                                            <!-- Modal yêu cầu lý do hủy -->
                                            <div class="modal fade" id="cancelModal{{ $appointment->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="cancelModalLabel{{ $appointment->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="cancelModalLabel{{ $appointment->id }}">Xác nhận hủy
                                                                lịch hẹn</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form
                                                            action="{{ route('appointments.cancel', $appointment->id) }}"
                                                            method="POST">
                                                            @csrf

                                                            <div class="modal-body">
                                                                <p>Bạn có chắc chắn muốn hủy lịch hẹn
                                                                    <strong>{{ $appointment->appointment_code }}</strong>
                                                                    không?
                                                                </p>
                                                                <div class="form-group">
                                                                    <label for="cancellation_reason">Lý do hủy <span
                                                                            class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="4" required
                                                                        placeholder="Vui lòng nhập lý do hủy..."></textarea>
                                                                    @error('cancellation_reason')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-danger">Xác nhận
                                                                    hủy</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td> --}}
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Không có lịch hẹn chưa xác nhận.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pendingAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã xác nhận -->
                <div class="tab-pane fade {{ $activeTab == 'confirmed' ? 'show active' : '' }}" id="confirmed"
                    role="tabpanel">
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

                                            $paymentClass = $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                            $paymentText = $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                        @endphp
                                        <td>
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('appointments.completed', $appointment->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Hoàn
                                                    thành</button>
                                            </form>
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="btn btn-info btn-sm">Xem</a>
                                            <a href="{{ route('appointments.edit', $appointment->id) }}"
                                                class="btn btn-warning btn-sm">Sửa</a>
                                           
                                            @if (in_array($appointment->status, ['pending', 'confirmed']))
                                                <form action="{{ route('appointments.cancel', $appointment->id) }}"
                                                    method="POST" style="display:inline-block;"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch hẹn này không?');">
                                                    @csrf

                                                    <button type="submit" class="btn btn-danger btn-sm">Hủy</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Không có lịch hẹn đã xác nhận.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $confirmedAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hoàn thành -->
                <div class="tab-pane fade {{ $activeTab == 'completed' ? 'show active' : '' }}" id="completed"
                    role="tabpanel">
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

                                            $paymentClass = $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                            $paymentText = $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                        @endphp
                                        <td>
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="btn btn-info btn-sm">Xem</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Không có lịch hẹn đã hoàn thành.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $completedAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab chờ huỷ -->
                <div class="tab-pane fade {{ $activeTab == 'pending_cancellation' ? 'show active' : '' }}"
                    id="pending-cancellation" role="tabpanel">
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
                                <th>Thời gian</th>
                                <th>Lý do hủy</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($pending_cancellationAppointments->count())
                                @foreach ($pending_cancellationAppointments as $index => $appointment)
                                    <tr>
                                        <td>{{ $pending_cancellationAppointments->firstItem() + $index }}</td>
                                        <td>{{ $appointment->appointment_code }}</td>
                                        <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                                        <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                                        <td>{{ $appointment->email ?? ($appointment->user?->email ?? 'N/A') }}</td>
                                        <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                                        <td>{{ $appointment->service?->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>{{ $appointment->cancellation_reason ?? 'N/A' }}</td>
                                        <td class="text-center">
                                             {{-- <form action="{{ route('appointments.destroy', $appointment->id) }}"
                                                method="POST" style="display:inline-block;"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn huỷ lịch hẹn này không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">Huỷ</button>
                                            </form> --}}
                                            <form action="{{ route('appointments.approve-cancel', $appointment->id) }}"
                                                method="POST" style="display:inline-block;"
                                                onsubmit="return confirm('Bạn có muốn xác nhận huỷ lịch hẹn này không?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">Chấp nhận</button>
                                            </form>

                                            <form action="{{ route('appointments.reject-cancel', $appointment->id) }}"
                                                method="POST" style="display:inline-block;"
                                                onsubmit="return confirm('Bạn có muốn xác nhận huỷ lịch hẹn này không?');">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">Từ chối</button>
                                            </form>
                                            <!-- Nút mở modal từ chối -->
                                            {{-- <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#rejectModal{{ $appointment->id }}">Từ chối
                                            </button> --}}
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="btn btn-info btn-sm">Xem
                                            </a>

                                            <!-- Modal yêu cầu lý do từ chối -->
                                            {{-- <div class="modal fade" id="rejectModal{{ $appointment->id }}"
                                                tabindex="-1" aria-labelledby="rejectModalLabel{{ $appointment->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="rejectModalLabel{{ $appointment->id }}">Từ chối yêu
                                                                cầu hủy</h5>
                                                            <button type="button" class="btn btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('appointments.reject-cancel', $appointment->id) }}"
                                                            method="POST">
                                                            @csrf

                                                            <div class="modal-body">
                                                                <p>Bạn có muốn từ chối yêu cầu hủy lịch hẹn
                                                                    <strong>{{ $appointment->appointment_code }}</strong>
                                                                    không?
                                                                </p>
                                                                <div class="form-group">
                                                                    <label for="rejection_reason">Lý do từ chối (tùy
                                                                        chọn)</label>
                                                                    <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                                                        placeholder="Vui lòng nhập lý do từ chối..."></textarea>
                                                                    @error('rejection_reason')
                                                                        <span class="text-danger">{{ $message }}</span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Đóng</button>
                                                                <button type="submit" class="btn btn-warning">Từ
                                                                    chối</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center text-muted">Không có yêu cầu hủy nào.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pending_cancellationAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>

                <!-- Tab Đã hủy -->
                <div class="tab-pane fade {{ $activeTab == 'cancelled' ? 'show active' : '' }}" id="cancelled"
                    role="tabpanel">
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
                                        </td>
                                        @php
                                            $statusClass = $statusMap[$appointment->status]['class'] ?? 'secondary';
                                            $statusText = $statusMap[$appointment->status]['text'] ?? 'Không rõ';

                                            $paymentClass = $paymentMap[$appointment->payment_status]['class'] ?? 'secondary';
                                            $paymentText = $paymentMap[$appointment->payment_status]['text'] ?? 'Không rõ';
                                        @endphp
                                        <td>
                                            <span class="badge bg-{{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $paymentClass }}">{{ $paymentText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('appointments.show', $appointment->id) }}"
                                                class="btn btn-info btn-sm">Xem</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Không có lịch hẹn đã hủy.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $cancelledAppointments->appends(['search' => request('search')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
@endsection
