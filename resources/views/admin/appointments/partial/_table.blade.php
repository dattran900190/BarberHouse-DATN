<table class="table table-bordered table-hover">
    <thead class="thead-light">
        <tr>
            <th>Stt</th>
            <th>Mã lịch hẹn</th>
            <th>Khách hàng</th>
            <th>Số điện thoại</th>
            <th>Thợ</th>
            <th>Dịch vụ</th>
            <th>Phương thức</th>
            <th>Thời gian</th>
            @if ($type === 'cancelled')
                <th>Lý do</th>
            @endif
            <th>Trạng thái</th>
            <th>Trạng thái thanh toán</th>
            <th class="text-center">Hành động</th>
        </tr>
    </thead>
    <tbody>
        @if ($appointments->count())
            @foreach ($appointments as $index => $appointment)
                <tr>
                    <td>{{ $appointments->firstItem() + $index }}</td>
                    <td>{{ $appointment->appointment_code }}</td>
                    <td>{{ $appointment->name ?? ($appointment->user?->name ?? 'N/A') }}</td>
                    <td>{{ $appointment->phone ?? ($appointment->user?->phone ?? 'N/A') }}</td>
                    <td>{{ $appointment->barber?->name ?? 'Thợ đã nghỉ' }}</td>
                    <td>
                        {{ $appointment->service?->name ?? 'N/A' }}

                        @if ($appointment->additional_service_objects && $appointment->additional_service_objects->isNotEmpty())
                            <div class="mt-2">
                                <strong class="text-muted">Dịch vụ thêm:</strong>
                                <ul class="mb-0 mt-1 ps-3 text-muted">
                                    @foreach ($appointment->additional_service_objects as $service)
                                        <li>{{ $service->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </td>
                    <td>{{ $appointment->payment_method === 'cash' ? 'Thanh toán tại tiệm' : 'Thanh toán VNPAY' }}</td>
                    <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                    @if ($type === 'cancelled')
                        <td>{{ $appointment->cancellation_reason ?? 'N/A' }}</td>
                    @endif
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

                        @include('admin.appointments.partial._actions', [
                            'appointment' => $appointment,
                            'type' => $type,
                        ])
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{ $type === 'cancelled' ? 11 : 10 }}" class="text-center text-muted">Không có lịch hẹn
                    nào
                    phù hợp.</td>
            </tr>
        @endif
    </tbody>
</table>
