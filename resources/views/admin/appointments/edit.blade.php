@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa lịch hẹn')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Đặt lịch cắt tóc</h3>
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
                <a href="{{ url('admin/appointments') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/appointments/' . $appointment->id . '/edit') }}">Sửa đặt lịch</a>
            </li>
        </ul>
    </div>

    @php
        $currentStatus = $appointment->status;
        $currentPaymentStatus = $appointment->payment_status;

        $statusOptions = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'progress' => 'Đang làm tóc',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];
        $paymentOptions = [
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Thanh toán thành công',
            'refunded' => 'Hoàn trả thanh toán',
            'failed' => 'Thanh toán thất bại',
        ];

        $paymentLocked = in_array($currentPaymentStatus, ['paid', 'refunded']);

        // Quy tắc chuyển đổi trạng thái lịch hẹn
        $allowedStatusTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['checked-in', 'cancelled', 'progress'],
            'progress' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        // Quy tắc chuyển đổi trạng thái thanh toán
        $allowedPaymentTransitions = [
            'unpaid' => ['paid', 'failed'],
            'paid' => ['refunded'],
            'failed' => ['paid'],
            'refunded' => [],
        ];

    @endphp

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Sửa đặt lịch {{ $appointment->appointment_code }}</div>
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

                <div class="mb-3">
                    <label for="service_id" class="form-label">Dịch vụ chính</label>
                    <select class="form-control" id="service_id" name="service_id" required>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}"
                                {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                {{ $service->name }} ({{ number_format($service->price) }} VNĐ, {{ $service->duration }}
                                phút)
                            </option>
                        @endforeach
                    </select>
                    @error('service_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="services" class="form-label">Dịch vụ bổ sung</label>

                    <div class="input-group mb-2">
                        <select class="form-control" id="additionalServiceDropdown" style="width: 70%;">
                            <option value="">Chọn dịch vụ thêm</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" data-price="{{ $service->price }}"
                                    data-duration="{{ $service->duration }}">
                                    {{ $service->name }} ({{ number_format($service->price) }} VNĐ,
                                    {{ $service->duration }} phút)
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary" id="addAdditionalServiceBtn" style="width: 30%;">Thêm
                            dịch vụ</button>
                    </div>

                    <div id="selectedAdditionalServices" class="mt-2">
                        @if ($appointment->additional_services)
                            @foreach (json_decode($appointment->additional_services, true) as $serviceId)
                                @php
                                    $service = $services->firstWhere('id', $serviceId);
                                @endphp
                                @if ($service)
                                    <div class="selected-service border rounded p-2 mb-2" data-id="{{ $service->id }}"
                                        data-price="{{ $service->price }}">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span>{{ $service->name }} ({{ number_format($service->price) }} VNĐ,
                                                    {{ $service->duration }} phút)</span>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>


                    <input type="hidden" name="additional_services" id="additionalServicesInput">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Trạng thái lịch hẹn</label>
                        <select class="form-control" id="status" name="status"
                            @if (in_array($currentStatus, ['completed', 'cancelled'])) disabled @endif>
                            @foreach ($statusOptions as $statusValue => $label)
                                <option value="{{ $statusValue }}"
                                    {{ old('status', $currentStatus) === $statusValue ? 'selected' : '' }}
                                    @if (!in_array($statusValue, $allowedStatusTransitions[$currentStatus] ?? []) && $statusValue !== $currentStatus) disabled @endif>
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
                                        !in_array($payValue, $allowedPaymentTransitions[$currentPaymentStatus] ?? []) &&
                                            $payValue !== $currentPaymentStatus) disabled @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if ($paymentLocked)
                            <input type="hidden" name="payment_status" value="{{ $currentPaymentStatus }}">
                            <small class="text-muted">
                                Thanh toán đã {{ $currentPaymentStatus === 'refunded' ? 'hoàn trả' : 'thành công' }}, không
                                thể thay đổi.
                            </small>
                        @endif
                        @error('payment_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-sm btn-outline-primary">
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
        document.addEventListener('DOMContentLoaded', function() {
            const serviceIdSelect = document.getElementById('service_id');
            const additionalServiceDropdown = document.getElementById('additionalServiceDropdown');
            const addAdditionalServiceBtn = document.getElementById('addAdditionalServiceBtn');
            const selectedAdditionalServices = document.getElementById('selectedAdditionalServices');
            const additionalServicesInput = document.getElementById('additionalServicesInput');
            const totalAmountDisplay = document.getElementById('totalAmountDisplay');
            const totalAmountInput = document.getElementById('totalAmountInput');

            // Hàm định dạng số
            function numberFormat(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' VNĐ';
            }

            // Hàm tính tổng chi phí
            function calculateTotalAmount() {
                let total = 0;
                const mainServicePrice = parseFloat(serviceIdSelect.options[serviceIdSelect.selectedIndex]
                    .getAttribute('data-price')) || 0;
                total += mainServicePrice;

                const additionalServices = Array.from(selectedAdditionalServices.querySelectorAll(
                    '.selected-service'));
                additionalServices.forEach(service => {
                    total += parseFloat(service.getAttribute('data-price')) || 0;
                });

                const discount = parseFloat(document.querySelector('input[name="discount_amount"]')?.value) || 0;
                total -= discount;

                totalAmountDisplay.value = numberFormat(total);
                totalAmountInput.value = total;
            }

            // Thêm dịch vụ thêm
            addAdditionalServiceBtn.addEventListener('click', function() {
                const selectedOption = additionalServiceDropdown.options[additionalServiceDropdown
                    .selectedIndex];

                if (selectedOption.value) {
                    const serviceId = selectedOption.value;
                    const serviceName = selectedOption.text;
                    const servicePrice = selectedOption.getAttribute('data-price');

                    const isDuplicate = Array.from(selectedAdditionalServices.querySelectorAll(
                            '.selected-service'))
                        .some(service => service.getAttribute('data-id') === serviceId);

                    if (!isDuplicate) {
                        const serviceDiv = document.createElement('div');
                        serviceDiv.className = 'selected-service border rounded p-2 mb-2';
                        serviceDiv.setAttribute('data-id', serviceId);
                        serviceDiv.setAttribute('data-price', servicePrice);

                        serviceDiv.innerHTML = `
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span>${serviceName}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-service">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

                        selectedAdditionalServices.appendChild(serviceDiv);

                        // Xoá option khỏi dropdown sau khi chọn
                        selectedOption.remove();

                        updateAdditionalServicesInput();
                        calculateTotalAmount();
                    } else {
                        alert('Dịch vụ đã được chọn!');
                    }
                }
            });


            // Xóa dịch vụ thêm
            selectedAdditionalServices.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-service')) {
                    const serviceDiv = e.target.closest('.selected-service');
                    const serviceId = serviceDiv.getAttribute('data-id');
                    serviceDiv.remove();

                    const option = document.createElement('option');
                    option.value = serviceId;
                    option.text = serviceDiv.textContent.replace(' - .*', '').trim();
                    option.setAttribute('data-price', serviceDiv.getAttribute('data-price'));
                    option.setAttribute('data-duration', serviceDiv.querySelector('input[type="hidden"]')
                        .value.match(/duration_(\d+)/)[1] || 0);
                    additionalServiceDropdown.appendChild(option);

                    updateAdditionalServicesInput();
                    calculateTotalAmount();
                }
            });

            // Cập nhật khi chọn dịch vụ chính
            serviceIdSelect.addEventListener('change', function() {
                calculateTotalAmount();
            });

            // Cập nhật input ẩn
            // function updateAdditionalServicesInput() {
            //     const serviceIds = Array.from(selectedAdditionalServices.querySelectorAll('.selected-service'))
            //         .map(service => service.getAttribute('data-id'));
            //     additionalServicesInput.value = JSON.stringify(serviceIds);
            // }

            function updateAdditionalServicesInput() {
                const serviceIds = Array.from(document.querySelectorAll(
                        '#selectedAdditionalServices .selected-service'))
                    .map(service => service.getAttribute('data-id'));
                document.getElementById('additionalServicesInput').value = JSON.stringify(serviceIds);
            }
            updateAdditionalServicesInput();

            // Khởi tạo giá trị ban đầu
            calculateTotalAmount();
        });
    </script>
@endsection
