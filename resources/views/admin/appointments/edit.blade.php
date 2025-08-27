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
            'confirmed' => ['checked-in', 'cancelled'],
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
            <form id="editBookingForm" method="POST" action="{{ route('appointments.update', $appointment->id) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="row g-3 form-group">
                    <div class="col-md-6">
                        <label class="form-label">Họ và tên <span class="required">*</span></label>
                        <input id="name" name="name" class="form-control" type="text"
                            placeholder="Nhập họ và tên" value="{{ old('name', $appointment->name) }}" readonly>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ngày đặt lịch <span class="required">*</span></label>
                        <div class="date-input">
                            <input type="text" class="form-control" id="appointment_date" name="appointment_date"
                                placeholder="Chọn thời điểm" style="background-color: #fff !important;" readonly
                                value="{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d')) }}">
                        </div>
                        @error('appointment_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Chọn chi nhánh <span class="required">*</span></label>
                    <div class="position-relative">
                        <input type="hidden" id="branch_input" name="branch_id"
                            value="{{ old('branch_id', $appointment->branch_id) }}"
                            {{ in_array($appointment->status, ['completed', 'cancelled']) ? 'readonly' : '' }}>
                        <div class="branch-cards-container" id="branchCards">
                            @foreach ($branches as $branch)
                                <div class="branch-card {{ $branch->id == $appointment->branch_id ? 'selected' : '' }}"
                                    data-branch-id="{{ $branch->id }}" data-branch-name="{{ $branch->name }}"
                                    {{ in_array($appointment->status, ['completed', 'cancelled']) ? 'style=cursor:not-allowed;opacity:0.7;' : '' }}>
                                    <div class="branch-icon-wrapper">
                                        <i class="fas fa-map-marker-alt branch-icon"
                                            data-value="{{ $branch->google_map_url }}"></i>
                                    </div>
                                    <div class="branch-info">
                                        <h6 class="branch-name">{{ $branch->name }}</h6>
                                        <div class="branch-address">
                                            <i class="fas fa-map-pin"></i>
                                            <span>{{ $branch->address ?? 'Địa chỉ chi nhánh' }}</span>
                                        </div>
                                        <div class="branch-hours">
                                            <i class="fas fa-clock"></i>
                                            <span>08:00 - 19:30</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('branch_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 form-group">
                    <div class="col-md-6">
                        <label class="form-label">Chọn khung giờ dịch vụ <span class="required">*</span></label>
                        <input type="time" name="appointment_time" id="appointment_time" class="form-control"
                            value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i')) }}">
                        @error('appointment_time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Khuyến mãi</label>
                        <select name="voucher_code" id="voucher_id" class="form-control">
                            <option value="">Không sử dụng mã giảm giá</option>
                            @foreach ($allPromotions as $promotion)
                                <option value="{{ $promotion['code'] }}"
                                    data-discount-type="{{ $promotion['discount_type'] }}"
                                    data-discount-value="{{ $promotion['discount_value'] }}"
                                    data-expired-at="{{ $promotion['end_date'] }}"
                                    data-promotion-id="{{ $promotion['id'] }}"
                                    data-max-discount="{{ $promotion['max_discount_amount'] }}"
                                    data-min-order-value="{{ $promotion['min_order_value'] }}"
                                    {{ old('voucher_code', $appointment->promotion ? $appointment->promotion->code : '') == $promotion['code'] ? 'selected' : '' }}>
                                    {{ $promotion['code'] }}
                                    ({{ $promotion['discount_type'] === 'fixed' ? number_format($promotion['discount_value']) . ' VNĐ' : $promotion['discount_value'] . '%' }}
                                    {{ $promotion['type'] === 'user_voucher' ? ' - Voucher cá nhân' : ' - Voucher công khai' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dịch vụ <span class="required">*</span></label>
                    <div id="servicesList">
                        <div class="service-item" data-service-index="0">
                            <div class="d-flex align-items-start gap-2">
                                <div class="flex-grow-1 position-relative col-md-8">
                                    <select id="service" name="service_id" class="form-control service-select"
                                        data-index="0">
                                        <option value="">Chọn dịch vụ</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" data-name="{{ $service->name }}"
                                                data-price="{{ $service->price }}"
                                                data-duration="{{ $service->duration }}"
                                                data-is-combo="{{ $service->is_combo ? '1' : '0' }}"
                                                {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                                                {{ $service->name }} – ({{ number_format($service->price) }}đ)
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button class="add-service-btn btn btn-outline-primary col-md-4" type="button"
                                    id="addServiceBtn">Thêm dịch vụ</button>
                            </div>
                        </div>
                    </div>
                    <div id="additionalServicesContainer" class="mt-2">
                        @if ($appointment->additional_services)
                            @foreach (json_decode($appointment->additional_services, true) as $serviceId)
                                @php
                                    $service = $services->firstWhere('id', $serviceId);
                                @endphp
                                @if ($service)
                                    <div class="service-wrapper mt-2 d-flex align-items-center">
                                        <select class="form-control additional-service-select"
                                            name="additional_services[]">
                                            <option value="">Chọn dịch vụ bổ xung</option>
                                            @foreach ($services as $s)
                                                @if (!$s->is_combo && $s->id != $appointment->service_id)
                                                    <option value="{{ $s->id }}" data-name="{{ $s->name }}"
                                                        data-price="{{ $s->price }}"
                                                        data-duration="{{ $s->duration }}"
                                                        data-is-combo="{{ $s->is_combo ? '1' : '0' }}"
                                                        {{ $s->id == $serviceId ? 'selected' : '' }}>
                                                        {{ $s->name }} – ({{ number_format($s->price) }}đ)
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <button type="button"
                                            class="btn btn-outline-danger btn-sm ms-2 col-md-1 remove-service-btn"
                                            style="padding: 10px 0.5rem; border: 1px solid #ccc;">
                                            <i class="fa fa-times"></i> Xóa
                                        </button>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <input type="hidden" name="additional_services" id="additionalServicesInput">
                </div>

                <div class="form-group">
                    <label class="form-label">Yêu cầu kĩ thuật viên <span class="required">*</span></label>
                    <div class="position-relative">
                        <select class="form-select d-none" id="barber" name="barber_id"
                            {{ in_array($appointment->status, ['completed', 'cancelled']) ? 'disabled' : '' }}>
                            <option value="">Chọn kĩ thuật viên</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}"
                                    {{ old('barber_id', $appointment->barber_id) == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="barber-cards-wrapper">
                            <div class="barber-cards-container" id="barberCards">
                                @foreach ($barbers as $barber)
                                    <div class="barber-card {{ $barber->id == $appointment->barber_id ? 'selected' : '' }}"
                                        data-barber-id="{{ $barber->id }}" data-barber-name="{{ $barber->name }}"
                                        {{ in_array($appointment->status, ['completed', 'cancelled']) ? 'style=cursor:not-allowed;opacity:0.7;' : '' }}>
                                        <div class="barber-avatar">
                                            @if ($barber->avatar)
                                                <img src="{{ asset('storage/' . $barber->avatar) }}"
                                                    alt="{{ $barber->name }}" class="barber-img">
                                            @else
                                                <div class="barber-img-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="barber-info">
                                            <h6 class="barber-name">{{ $barber->name }}</h6>
                                            <div class="barber-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $barber->rating_avg)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span
                                                    class="rating-text">({{ number_format($barber->rating_avg, 1) }})</span>
                                            </div>
                                            <div class="barber-skill">
                                                @php
                                                    $skillLevels = [
                                                        'assistant' => 'Thử việc',
                                                        'junior' => 'Sơ cấp',
                                                        'senior' => 'Chuyên nghiệp',
                                                        'master' => 'Bậc thầy',
                                                        'expert' => 'Chuyên gia',
                                                    ];
                                                    $skillLevelColors = [
                                                        'assistant' => 'secondary',
                                                        'junior' => 'info',
                                                        'senior' => 'primary',
                                                        'master' => 'success',
                                                        'expert' => 'warning',
                                                    ];
                                                    $levelKey = $barber->skill_level;
                                                    $levelText = $skillLevels[$levelKey] ?? 'Chuyên nghiệp';
                                                    $levelColor = $skillLevelColors[$levelKey] ?? 'dark';
                                                @endphp
                                                <span
                                                    class="skill-badge bg-{{ $levelColor }}">{{ $levelText }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if (count($barbers) > 12)
                                <div class="scroll-indicator">
                                    <i class="fas fa-chevron-down"></i>
                                    <span>Cuộn để xem thêm thợ</span>
                                </div>
                            @endif
                        </div>
                        @error('barber_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ghi chú</label>
                    <textarea class="form-control notes-textarea" name="note" rows="4"
                        placeholder="ghi chú có thể bỏ trống ..." readonly>{{ old('note', $appointment->note) }}</textarea>
                </div>

                <div class="row g-3 form-group">
                    <div class="col-md-6">
                        <label class="form-label">Trạng thái lịch hẹn <span class="required">*</span></label>
                        <select class="form-control" id="status" name="status"
                            @if (in_array($appointment->status, ['completed', 'cancelled'])) disabled @endif>
                            @foreach ($statusOptions as $statusValue => $label)
                                <option value="{{ $statusValue }}"
                                    {{ old('status', $appointment->status) === $statusValue ? 'selected' : '' }}
                                    @if (
                                        !in_array($statusValue, $allowedStatusTransitions[$appointment->status] ?? []) &&
                                            $statusValue !== $appointment->status) disabled @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Trạng thái thanh toán <span class="required">*</span></label>
                        <select class="form-control" id="payment_status" name="payment_status"
                            {{ $paymentLocked ? 'disabled' : '' }}>
                            @foreach ($paymentOptions as $payValue => $label)
                                <option value="{{ $payValue }}"
                                    {{ old('payment_status', $appointment->payment_status) === $payValue ? 'selected' : '' }}
                                    @if (
                                        !in_array($payValue, $allowedPaymentTransitions[$appointment->payment_status] ?? []) &&
                                            $payValue !== $appointment->payment_status) disabled @endif>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if ($paymentLocked)
                            <input type="hidden" name="payment_status" value="{{ $appointment->payment_status }}">
                            <small class="text-muted">
                                Thanh toán đã
                                {{ $appointment->payment_status === 'refunded' ? 'hoàn trả' : 'thành công' }}, không
                                thể thay đổi.
                            </small>
                        @endif
                        @error('payment_status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="service-info form-group">
                    <p>Tổng tiền: <strong id="totalPrice">{{ number_format($appointment->total_amount) }} VNĐ</strong></p>
                    <span id="total_after_discount">
                        @if ($appointment->discount_amount > 0)
                            <span class="text-success">
                                Đã giảm:
                                {{ $appointment->promotion->discount_type === 'fixed' ? number_format($appointment->discount_amount) . ' VNĐ' : $appointment->promotion->discount_value . '% (' . number_format($appointment->discount_amount) . ' VNĐ)' }}
                            </span>
                        @endif
                    </span>
                    <p>Thời lượng dự kiến: <strong id="totalDuration">{{ $appointment->duration }} Phút</strong></p>
                </div>

                <div class="form-btn mt-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-edit me-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </form>

        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize flatpickr
            flatpickr('#appointment_date', {
                locale: 'vn',
                minDate: 'today',
                maxDate: new Date().fp_incr(90),
                dateFormat: 'Y-m-d',
                defaultDate: '{{ old('appointment_date', \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d')) }}',
                onChange: function(selectedDates, dateStr) {
                    document.getElementById('appointment_date').value = dateStr;
                    filterVouchersByDate();
                    checkVoucherValidity();
                }
            });

            // Initialize Select2 for voucher
            $('#voucher_id').select2({
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Không tìm thấy mã phù hợp";
                    }
                }
            });

            // Thiết lập giá trị ban đầu cho Select2
            const currentVoucherCode =
                '{{ old('voucher_code', $appointment->promotion ? $appointment->promotion->code : '') }}';
            if (currentVoucherCode) {
                $('#voucher_id').val(currentVoucherCode).trigger('change.select2');
            }

            // Additional services handling
            setupServiceManagement();

            // Update total initially
            updateTotal();

            // Handle session messages
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: '{{ session('success') }}',
                    confirmButtonText: 'OK'
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Thử lại'
                });
            @endif

            // Gửi AJAX kiểm tra voucher khi thay đổi dịch vụ
            $('#service, #additionalServicesContainer').on('change', function(e) {
                if (e.target.classList.contains('service-select') || e.target.classList.contains(
                        'additional-service-select')) {
                    checkVoucherValidity();
                }
            });

            $('#voucher_id').on('select2:select change', checkVoucherValidity);

            // Hàm kiểm tra tính hợp lệ của voucher
            function checkVoucherValidity() {
                const serviceId = $('#service').val();
                const additionalServices = Array.from(document.querySelectorAll('.additional-service-select'))
                    .map(select => select.value)
                    .filter(value => value !== '');
                const voucherCode = $('#voucher_id').val();
                const appointmentId = '{{ $appointment->id }}'; // Lấy appointment_id từ Blade

                if (!serviceId) return; // Không kiểm tra nếu chưa chọn dịch vụ chính

                const formData = new FormData();
                formData.append('service_id', serviceId);
                formData.append('additional_services', JSON.stringify(additionalServices));
                formData.append('voucher_code', voucherCode || '');
                formData.append('appointment_id', appointmentId); // Thêm appointment_id
                formData.append('_token', '{{ csrf_token() }}');
            }
        });

        // Setup service management
        function setupServiceManagement() {
            const addServiceBtn = document.getElementById('addServiceBtn');
            const additionalServicesContainer = document.getElementById('additionalServicesContainer');
            const additionalServicesInput = document.getElementById('additionalServicesInput');
            const serviceSelect = document.getElementById('service');

            function addAdditionalService() {
                const serviceWrapper = document.createElement('div');
                serviceWrapper.className = 'service-wrapper mt-2 d-flex align-items-center';
                const serviceSelectElement = document.createElement('select');
                serviceSelectElement.className = 'form-control additional-service-select';
                serviceSelectElement.name = 'additional_services[]';
                const mainServiceOption = serviceSelect.options[serviceSelect.selectedIndex];
                const isMainServiceCombo = mainServiceOption.getAttribute('data-is-combo') === '1';
                let options = '';
                if (!isMainServiceCombo) {
                    const allServiceOptions = serviceSelect.querySelectorAll('option[value]');
                    allServiceOptions.forEach(option => {
                        const serviceId = option.value;
                        const serviceName = option.textContent;
                        const isServiceCombo = option.getAttribute('data-is-combo') === '1';
                        const selectedServices = getSelectedAdditionalServices();
                        const isAlreadySelected = selectedServices.includes(serviceId);
                        if (!isServiceCombo && serviceId != serviceSelect.value && !isAlreadySelected) {
                            const price = option.getAttribute('data-price');
                            const duration = option.getAttribute('data-duration');
                            const is_combo = option.getAttribute('data-is-combo');
                            options += `
                                <option value="${serviceId}" data-name="${option.getAttribute('data-name')}"
                                    data-price="${price}" data-duration="${duration}" data-is-combo="${is_combo}">
                                    ${serviceName}
                                </option>
                            `;
                        }
                    });
                }
                serviceSelectElement.innerHTML = options;
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-outline-danger btn-sm ms-2 col-md-1 remove-service-btn';
                removeBtn.style.padding = '10px 0.5rem';
                removeBtn.style.border = '1px solid #ccc';
                removeBtn.innerHTML = '<i class="fa fa-times"></i> Xóa';
                serviceWrapper.appendChild(serviceSelectElement);
                serviceWrapper.appendChild(removeBtn);
                additionalServicesContainer.appendChild(serviceWrapper);
                updateAdditionalServicesInput();
                updateTotal();
                setupRemoveButtons();
            }

            function updateAdditionalServicesDropdowns() {
                const mainServiceOption = serviceSelect.options[serviceSelect.selectedIndex];
                const isMainServiceCombo = mainServiceOption.getAttribute('data-is-combo') === '1';
                if (isMainServiceCombo) {
                    additionalServicesContainer.innerHTML = '';
                    updateAdditionalServicesInput();
                    return;
                }
                const additionalSelects = additionalServicesContainer.querySelectorAll('.additional-service-select');
                additionalSelects.forEach((select, index) => {
                    const currentValue = select.value;
                    let options = '';
                    const allServiceOptions = serviceSelect.querySelectorAll('option[value]');
                    allServiceOptions.forEach(option => {
                        const serviceId = option.value;
                        const serviceName = option.textContent;
                        const isServiceCombo = option.getAttribute('data-is-combo') === '1';
                        const selectedServices = getSelectedAdditionalServices().filter(id => id !==
                            currentValue);
                        const isAlreadySelected = selectedServices.includes(serviceId);
                        if (!isServiceCombo && serviceId != parseInt(serviceSelect.value) && !
                            isAlreadySelected) {
                            const selected = serviceId.toString() === currentValue ? 'selected' : '';
                            options += `
                                <option value="${serviceId}" data-name="${serviceName}"
                                    data-price="${option.getAttribute('data-price')}" data-duration="${option.getAttribute('data-duration')}" ${selected}>
                                    ${serviceName}
                                </option>
                            `;
                        }
                    });
                    select.innerHTML = options;
                });
            }

            function getSelectedAdditionalServices() {
                return Array.from(additionalServicesContainer.querySelectorAll('.additional-service-select'))
                    .map(select => select.value)
                    .filter(value => value !== '');
            }

            function updateAdditionalServicesInput() {
                const additionalServices = getSelectedAdditionalServices();
                additionalServicesInput.value = JSON.stringify(additionalServices);
            }

            function setupRemoveButtons() {
                const removeButtons = additionalServicesContainer.querySelectorAll('.remove-service-btn');
                removeButtons.forEach(button => {
                    button.removeEventListener('click', handleRemoveButtonClick);
                    button.addEventListener('click', handleRemoveButtonClick);
                });
            }

            function handleRemoveButtonClick() {
                const serviceWrapper = this.closest('.service-wrapper');
                if (serviceWrapper) {
                    additionalServicesContainer.removeChild(serviceWrapper);
                    updateAdditionalServicesInput();
                    updateTotal();
                    checkVoucherValidity(); // Kiểm tra lại voucher khi xóa dịch vụ
                }
            }

            addServiceBtn.addEventListener('click', function() {
                const selectedServiceId = serviceSelect.value;
                if (!selectedServiceId) {
                    const existingNotices = additionalServicesContainer.querySelectorAll('.service-notice');
                    existingNotices.forEach(notice => notice.remove());
                    const noticeNoSelectMainService = document.createElement('div');
                    noticeNoSelectMainService.className = 'service-notice notice-warning';
                    noticeNoSelectMainService.innerHTML = `
                        <div class="notice-content">
                            <div class="notice-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="notice-text">
                                <p>Vui lòng chọn dịch vụ. Bạn cần chọn dịch vụ trước khi thêm dịch vụ bổ sung.</p>
                            </div>
                        </div>
                    `;
                    additionalServicesContainer.appendChild(noticeNoSelectMainService);
                    setTimeout(() => {
                        if (noticeNoSelectMainService.parentNode) {
                            noticeNoSelectMainService.remove();
                        }
                    }, 5000);
                    return;
                }
                const mainServiceOption = serviceSelect.options[serviceSelect.selectedIndex];
                const isMainServiceCombo = mainServiceOption.getAttribute('data-is-combo') === '1';
                if (isMainServiceCombo) {
                    const existingNotices = additionalServicesContainer.querySelectorAll('.service-notice');
                    existingNotices.forEach(notice => notice.remove());
                    const noticeNoAddAdditionalService = document.createElement('div');
                    noticeNoAddAdditionalService.className = 'service-notice notice-info';
                    noticeNoAddAdditionalService.innerHTML = `
                        <div class="notice-content">
                            <div class="notice-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="notice-text">
                                <p>Dịch vụ combo đã bao gồm tất cả dịch vụ cần thiết. Không cần thêm dịch vụ bổ sung.</p>
                            </div>
                        </div>
                    `;
                    additionalServicesContainer.appendChild(noticeNoAddAdditionalService);
                    setTimeout(() => {
                        if (noticeNoAddAdditionalService.parentNode) {
                            noticeNoAddAdditionalService.remove();
                        }
                    }, 5000);
                    return;
                }
                addAdditionalService();
            });

            additionalServicesContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('additional-service-select')) {
                    updateAdditionalServicesInput();
                    updateTotal();
                }
            });

            setupRemoveButtons();
            updateAdditionalServicesInput();
        }

        // Event listeners for updates
        // $('#service').on('change', function() {
        //     checkVoucherValidity();
        //     updateAdditionalServicesDropdowns();
        //     updateAdditionalServicesInput();
        //     updateTotal();
        //     setupServiceManagement();
        // });
        // $('#voucher_id').on('select2:select change', updateTotal);
        // $('#appointment_time').on('change', updateTotal);
        // $('#status').on('change', updateTotal);
        // $('#payment_status').on('change', updateTotal);
        // new MutationObserver(updateTotal).observe(document.getElementById('additionalServicesContainer'), {
        //     childList: true,
        //     subtree: true
        // });
        $('#service').on('change', function() {
            document.getElementById('additionalServicesContainer').innerHTML = '';
            // updateAdditionalServicesInput();
            // updateTotal();
            // setupServiceManagement();
            checkVoucherValidity();
            updateAdditionalServicesDropdowns();
            updateAdditionalServicesInput();
            updateTotal();
            setupServiceManagement();
        });
        $('#voucher_id').on('select2:select', updateTotal);
        $('#voucher_id').on('change', updateTotal);
        $('#additionalServicesContainer').on('change', '.additional-service-select', updateTotal);
        $('#appointment_time').on('change', updateTotal);
        $('#status').on('change', updateTotal);
        $('#payment_status').on('change', updateTotal);
        new MutationObserver(updateTotal).observe(document.getElementById('additionalServicesContainer'), {
            childList: true,
            subtree: true
        });

        function getServiceInfo(opt) {
            if (!opt.length) return {
                price: 0,
                duration: 0
            };
            return {
                price: parseFloat(opt.data('price')) || 0,
                duration: parseInt(opt.data('duration')) || 0
            };
        }

        function getAdditionalServicesInfo() {
            let totalPrice = 0;
            let totalDuration = 0;
            $('.additional-service-select').each(function() {
                const opt = $(this).find('option:selected');
                if (opt.val()) {
                    const info = getServiceInfo(opt);
                    totalPrice += info.price;
                    totalDuration += info.duration;
                }
            });
            return {
                totalPrice,
                totalDuration
            };
        }

        function updateTotal() {
            const mainOpt = $('#service option:selected');
            const mainInfo = getServiceInfo(mainOpt);
            const addInfo = getAdditionalServicesInfo();
            const totalPrice = mainInfo.price + addInfo.totalPrice;
            const totalDuration = mainInfo.duration + addInfo.totalDuration;
            const voucherOpt = $('#voucher_id option:selected');
            const discountType = voucherOpt.data('discount-type') || '';
            const discountValue = parseFloat(voucherOpt.data('discount-value')) || 0;
            const maxDiscount = parseFloat(voucherOpt.data('max-discount')) || 0;
            const minOrderValue = parseFloat(voucherOpt.data('min-order-value')) || 0;

            let discount = 0;
            let discountText = '';
            if ($('#voucher_id').val() && totalPrice > 0 && discountType) {
                if (totalPrice < minOrderValue) {
                    $('#total_after_discount').html(
                        `<span class="text-danger">Giá trị lịch hẹn phải đạt tối thiểu ${minOrderValue.toLocaleString('vi-VN')} VNĐ để áp dụng voucher này.</span>`
                    )
                    $('#totalPrice').text(totalPrice.toLocaleString('vi-VN') + ' VNĐ');
                    $('#totalDuration').text(totalDuration + ' Phút');
                    return;
                }
                if (discountType === 'fixed') {
                    discount = discountValue;
                    discountText = '- ' + discount.toLocaleString('vi-VN') + ' VNĐ';
                } else if (discountType === 'percent') {
                    discount = Math.round(totalPrice * discountValue / 100);
                    if (maxDiscount > 0 && discount > maxDiscount) {
                        discount = maxDiscount;
                        discountText = '- ' + maxDiscount.toLocaleString('vi-VN') + ' VNĐ (Tối đa)';
                    } else {
                        discountText = '- ' + discountValue + '% (' + discount.toLocaleString('vi-VN') + ' VNĐ)';
                    }
                }
            }

            let total = totalPrice - discount;
            if (total < 0) total = 0;

            $('#totalPrice').text(total.toLocaleString('vi-VN') + ' VNĐ');
            $('#total_after_discount').html(discount > 0 ?
                '<span class="text-success">Đã giảm: ' + discountText + '</span>' :
                '');
            $('#totalDuration').text(totalDuration + ' Phút');
        }

        function filterVouchersByDate() {
            const appointmentDateInput = document.getElementById('appointment_date');
            const voucherSelect = document.getElementById('voucher_id');
            const selectedDate = appointmentDateInput.value;
            if (!selectedDate) return;

            Array.from(voucherSelect.options).forEach((option, idx) => {
                if (idx === 0) return;
                const expiredAt = option.getAttribute('data-expired-at');
                if (expiredAt && selectedDate > expiredAt) {
                    option.style.display = 'none';
                } else {
                    option.style.display = '';
                }
            });

            if (voucherSelect.selectedIndex > 0 && voucherSelect.options[voucherSelect.selectedIndex].style.display ===
                'none') {
                voucherSelect.selectedIndex = 0;
                $('#voucher_id').trigger('change.select2');
            }
        }

        // Submit form handling
        document.querySelector('#editBookingForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Xác nhận cập nhật',
                text: 'Bạn có chắc chắn muốn cập nhật lịch hẹn này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Cập nhật',
                cancelButtonText: 'Hủy',
                customClass: {
                    popup: 'custom-swal-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ trong giây lát.',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData(form);
                    if (!formData.get('voucher_code')) {
                        formData.set('ignore_voucher_error',
                            '1'); // Đảm bảo bỏ voucher nếu voucher_code rỗng
                    }
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json().then(data => ({
                            status: response.status,
                            data
                        })))
                        .then(({
                            status,
                            data
                        }) => {
                            Swal.close();
                            if (status !== 200) {
                                if (data.allow_ignore) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Voucher không hợp lệ',
                                        html: data.message,
                                        showCancelButton: true,
                                        confirmButtonText: 'Tiếp tục (Bỏ voucher)',
                                        cancelButtonText: 'Hủy',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(result => {
                                        if (result.isConfirmed) {
                                            // Thêm flag để bỏ voucher

                                            formData.set('voucher_code', '');
                                            formData.set('ignore_voucher_error', '1');
                                            fetch(form.action, {
                                                    method: 'POST',
                                                    body: formData,
                                                    headers: {
                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                        'Accept': 'application/json'
                                                    }
                                                })
                                                .then(response => response.json().then(data =>
                                                    ({
                                                        status: response.status,
                                                        data
                                                    })))
                                                .then(({
                                                    status,
                                                    data
                                                }) => {
                                                    Swal.close();
                                                    if (status !== 200) {
                                                        throw data;
                                                    }
                                                    if (data.success) {
                                                        Swal.fire({
                                                            title: 'Thành công!',
                                                            text: data.message,
                                                            icon: 'success',
                                                            customClass: {
                                                                popup: 'custom-swal-popup'
                                                            }
                                                        }).then(() => {
                                                            window.location.href =
                                                                data.redirect_url;
                                                        });
                                                    }
                                                })
                                                .catch(error => {
                                                    Swal.close();
                                                    let errorMessage = 'Đã có lỗi xảy ra.';
                                                    if (error.message) {
                                                        errorMessage = error.message;
                                                    }
                                                    Swal.fire({
                                                        title: 'Lỗi!',
                                                        html: errorMessage,
                                                        icon: 'error',
                                                        customClass: {
                                                            popup: 'custom-swal-popup'
                                                        }
                                                    });
                                                });
                                        } else {
                                            Swal.close();
                                            Swal.fire({
                                                icon: 'info',
                                                title: 'Đã hủy',
                                                text: 'Vui lòng thông báo lại cho khách hàng về việc voucher không hợp lệ.'
                                            });
                                        }
                                    });
                                } else {
                                    throw data;
                                }
                            } else if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                }).then(() => {
                                    onSuccess()
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            let errorMessage = 'Đã có lỗi xảy ra.';
                            if (error.message) {
                                errorMessage = error.message;
                            }
                            Swal.fire({
                                title: 'Lỗi!',
                                html: errorMessage,
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        });
                }
            });
        });

        function onSuccess() {
            const urlParams = new URLSearchParams(window.location.search);
            let status = urlParams.get('status') || '';
            const page = urlParams.get('page') || 1;

            if (!status) {
                const statusSelect = document.getElementById('status');
                status = statusSelect ? statusSelect.value || '{{ $appointment->status }}' : '{{ $appointment->status }}';
            }

            let redirectUrl = '{{ route('appointments.index') }}';
            const queryParams = [];
            if (status) {
                queryParams.push(`status=${encodeURIComponent(status)}`);
            }
            queryParams.push(`page=${encodeURIComponent(page)}`);
            if (queryParams.length > 0) {
                redirectUrl += `?${queryParams.join('&')}`;
            }

            window.location.href = redirectUrl;
        }
    </script>

@endsection
