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
                            @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher->promotion->code }}"
                                    data-discount-type="{{ $voucher->promotion->discount_type }}"
                                    data-discount-value="{{ $voucher->promotion->discount_value }}"
                                    data-expired-at="{{ $voucher->promotion->end_date }}"
                                    data-voucher-id="{{ $voucher->id }}"
                                    data-max-discount="{{ $voucher->promotion->max_discount_amount ?? 0 }}"
                                    {{ old('voucher_code', $appointment->promotion ? $appointment->promotion->code : '') == $voucher->promotion->code ? 'selected' : '' }}>
                                    {{ $voucher->promotion->code }}
                                    ({{ $voucher->promotion->discount_type === 'fixed' ? number_format($voucher->promotion->discount_value) . ' VNĐ' : $voucher->promotion->discount_value . '%' }})
                                </option>
                            @endforeach
                            @foreach ($publicPromotions as $promotion)
                                <option value="{{ $promotion->code }}"
                                    data-discount-type="{{ $promotion->discount_type }}"
                                    data-discount-value="{{ $promotion->discount_value }}"
                                    data-expired-at="{{ $promotion->end_date }}"
                                    data-promotion-id="public_{{ $promotion->id }}"
                                    data-max-discount="{{ $promotion->max_discount_amount ?? 0 }}"
                                    {{ old('voucher_code', $appointment->promotion ? $appointment->promotion->code : '') == $promotion->code ? 'selected' : '' }}>
                                    {{ $promotion->code }}
                                    ({{ $promotion->discount_type === 'fixed' ? number_format($promotion->discount_value) . ' VNĐ' : $promotion->discount_value . '%' }})
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
                                            <option value="">Chọn dịch vụ thêm</option>
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
    <style>
        /* Barber Cards Styles */
        .barber-cards-wrapper {
            position: relative;
        }

        .all-barbers-notice {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 15px;
            color: #856404;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .all-barbers-notice i {
            color: #f39c12;
            font-size: 14px;
        }

        .barber-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, 280px);
            /* mỗi cột 280px cố định */
            gap: 15px;
            justify-content: center;
            /* căn giữa nếu không đủ hàng */
            margin-top: 10px;
            padding-top: 10px;
            padding-bottom: 25px;
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }


        .scroll-indicator {
            text-align: center;
            padding: 10px;
            color: #6c757d;
            font-size: 12px;
            background: linear-gradient(to bottom, transparent, #fff);
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            pointer-events: none;
            opacity: 0.8;
        }

        .scroll-indicator i {
            margin-right: 5px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-5px);
            }

            60% {
                transform: translateY(-3px);
            }
        }

        /* Custom scrollbar cho barber cards */
        .barber-cards-container::-webkit-scrollbar {
            width: 6px;
        }

        .barber-cards-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .barber-cards-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .barber-cards-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .barber-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            background: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .barber-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
        }

        .barber-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }

        .barber-card.selected::after {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .barber-avatar {
            margin-right: 15px;
            flex-shrink: 0;
        }

        .barber-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f8f9fa;
        }

        .barber-img-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #e9ecef;
        }

        .barber-img-placeholder i {
            font-size: 24px;
            color: #6c757d;
        }

        .barber-info {
            flex: 1;
        }

        .barber-name {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .barber-rating {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }

        .barber-rating i {
            font-size: 14px;
            margin-right: 2px;
        }

        .rating-text {
            margin-left: 5px;
            font-size: 12px;
            color: #6c757d;
        }

        .barber-skill {
            margin-top: 5px;
        }

        .skill-badge {
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .skill-badge.bg-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
        }

        .skill-badge.bg-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        }

        .skill-badge.bg-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .skill-badge.bg-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }

        .skill-badge.bg-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #212529;
        }

        .skill-badge.bg-dark {
            background: linear-gradient(135deg, #343a40 0%, #1d2124 100%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .barber-cards-container {
                grid-template-columns: 1fr;
            }

            .barber-card {
                padding: 12px;
            }

            .barber-img,
            .barber-img-placeholder {
                width: 50px;
                height: 50px;
            }

            .barber-name {
                font-size: 14px;
            }
        }

        /* Responsive cho time grid */
        @media (max-width: 768px) {
            .time-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .time-slot {
                padding: 10px 6px;
                font-size: 13px;
            }
        }

        /* Service Notice Styles */
        .service-notice {
            margin: 15px 0;
            padding: 16px 20px;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            animation: slideIn 0.3s ease-out;
        }

        .service-notice:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .notice-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            border-left: 4px solid #f39c12;
        }

        .notice-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border-left: 4px solid #17a2b8;
        }

        .notice-content {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .notice-icon {
            flex-shrink: 0;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }

        .notice-warning .notice-icon {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }

        .notice-info .notice-icon {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .notice-text {
            flex: 1;
        }

        .notice-text h6 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: #2c3e50;
        }

        .notice-text p {
            margin: 0;
            font-size: 14px;
            color: #5a6c7d;
            line-height: 2;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive cho service notice */
        @media (max-width: 768px) {
            .service-notice {
                padding: 12px 16px;
                margin: 10px 0;
            }

            .notice-content {
                gap: 12px;
            }

            .notice-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }

            .notice-text h6 {
                font-size: 15px;
            }

            .notice-text p {
                font-size: 13px;
            }
        }
    </style>
@endsection

@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
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
                    updateTotal();
                }
            });

            // Initialize Select2 for voucher
            $('#voucher_id').select2({
                // placeholder: 'Chọn hoặc tìm mã khuyến mãi',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Không tìm thấy mã phù hợp";
                    }
                },
                disabled: {{ $appointment->promotion ? 'true' : 'false' }} // Disable Select2 nếu đã có voucher
            });

            // Filter vouchers by date
            // const appointmentDateInput = document.getElementById('appointment_date');
            // const voucherSelect = document.getElementById('voucher_id');

            // function filterVouchersByDate() {
            //     const selectedDate = appointmentDateInput.value;
            //     if (!selectedDate || voucherSelect.disabled) return; // Không lọc nếu select bị khóa
            //     Array.from(voucherSelect.options).forEach((option, idx) => {
            //         if (idx === 0) return;
            //         const expiredAt = option.getAttribute('data-expired-at');
            //         if (expiredAt && selectedDate > expiredAt) {
            //             option.style.display = 'none';
            //         } else {
            //             option.style.display = '';
            //         }
            //     });
            //     if (voucherSelect.selectedIndex > 0 && voucherSelect.options[voucherSelect.selectedIndex].style
            //         .display === 'none') {
            //         voucherSelect.selectedIndex = 0;
            //         $('#voucher_id').trigger('change');
            //     }
            // }
            // appointmentDateInput.addEventListener('change', filterVouchersByDate);
            // filterVouchersByDate();

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
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        confirmButton: 'custom-swal-confirm'
                    }
                });
            @endif
            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: '{{ session('error') }}',
                    confirmButtonText: 'Thử lại',
                    customClass: {
                        popup: 'custom-swal-popup',
                        title: 'custom-swal-title',
                        confirmButton: 'custom-swal-confirm'
                    }
                });
            @endif

            // Handle branch icon click for Google Maps
            document.querySelectorAll('.branch-icon').forEach(icon => {
                icon.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const googleMapUrl = this.getAttribute('data-value');
                    if (googleMapUrl) {
                        window.open(googleMapUrl, '_blank');
                    }
                });
            });
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
                if (isMainServiceCombo) {
                    serviceSelectElement.innerHTML = options;
                } else {
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
                removeBtn.className = 'btn btn-outline-danger btn-sm ms-2 col-md-1';
                removeBtn.style.padding = '10px 0.5rem';
                removeBtn.style.border = '1px solid #ccc';
                removeBtn.innerHTML = '<i class="fa fa-times"></i> Xóa';
                removeBtn.addEventListener('click', function() {
                    additionalServicesContainer.removeChild(serviceWrapper);
                    updateAdditionalServicesInput();
                    updateTotal();
                });
                serviceWrapper.appendChild(serviceSelectElement);
                serviceWrapper.appendChild(removeBtn);
                additionalServicesContainer.appendChild(serviceWrapper);
                updateAdditionalServicesInput();
                updateTotal();
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

            updateAdditionalServicesInput();
        }

        // Calculate total price and duration
        // function getServiceInfo(opt) {
        //     if (!opt.length) return {
        //         price: 0,
        //         duration: 0
        //     };
        //     return {
        //         price: parseFloat(opt.data('price')) || 0,
        //         duration: parseInt(opt.data('duration')) || 0
        //     };
        // }

        // function getAdditionalServicesInfo() {
        //     let totalPrice = 0;
        //     let totalDuration = 0;
        //     $('.additional-service-select').each(function() {
        //         const opt = $(this).find('option:selected');
        //         if (opt.val()) {
        //             const info = getServiceInfo(opt);
        //             totalPrice += info.price;
        //             totalDuration += info.duration;
        //         }
        //     });
        //     return {
        //         totalPrice,
        //         totalDuration
        //     };
        // }

        // function updateTotal() {
        //     const mainOpt = $('#service option:selected');
        //     const mainInfo = getServiceInfo(mainOpt);
        //     const addInfo = getAdditionalServicesInfo();
        //     let totalPrice = mainInfo.price + addInfo.totalPrice;
        //     const totalDuration = mainInfo.duration + addInfo.totalDuration;
        //     const voucherOpt = $('#voucher_id option:selected');
        //     const isVoucherLocked = voucherSelect.disabled; // Kiểm tra xem voucher có bị khóa không
        //     let discountValue = 0;
        //     let discountType = '';
        //     let maxDiscount = 0;
        //     let discount = 0;
        //     let discountText = '';

        //     if (isVoucherLocked && '{{ $appointment->promotion ? $appointment->promotion->code : '' }}') {
        //         // Sử dụng voucher ban đầu nếu select bị khóa
        //         const initialVoucher = Array.from(voucherSelect.options).find(opt =>
        //             opt.value === '{{ $appointment->promotion ? $appointment->promotion->code : '' }}'
        //         );
        //         if (initialVoucher) {
        //             discountValue = parseFloat(initialVoucher.getAttribute('data-discount-value')) || 0;
        //             discountType = initialVoucher.getAttribute('data-discount-type');
        //             maxDiscount = parseFloat(initialVoucher.getAttribute('data-max-discount')) || 0;

        //             if (discountType === 'fixed') {
        //                 discount = discountValue;
        //                 discountText = '- ' + discount.toLocaleString('vi-VN') + ' VNĐ';
        //             } else if (discountType === 'percent') {
        //                 discount = Math.round(totalPrice * discountValue / 100);
        //                 if (maxDiscount > 0 && discount > maxDiscount) {
        //                     discount = maxDiscount;
        //                     discountText = discount.toLocaleString('vi-VN') + ' VNĐ';
        //                 } else {
        //                     discountText = '- ' + discountValue + '% (' + discount.toLocaleString('vi-VN') + ' VNĐ)';
        //                 }
        //             }
        //         }
        //     } else if (voucherOpt.val() && totalPrice > 0) {
        //         // Sử dụng voucher được chọn nếu select không bị khóa
        //         discountValue = parseFloat(voucherOpt.data('discount-value')) || 0;
        //         discountType = voucherOpt.data('discount-type');
        //         maxDiscount = parseFloat(voucherOpt.data('max-discount')) || 0;

        //         if (discountType === 'fixed') {
        //             discount = discountValue;
        //             discountText = '- ' + discount.toLocaleString('vi-VN') + ' VNĐ';
        //         } else if (discountType === 'percent') {
        //             discount = Math.round(totalPrice * discountValue / 100);
        //             if (maxDiscount > 0 && discount > maxDiscount) {
        //                 discount = maxDiscount;
        //                 discountText = discount.toLocaleString('vi-VN') + ' VNĐ';
        //             } else {
        //                 discountText = '- ' + discountValue + '% (' + discount.toLocaleString('vi-VN') + ' VNĐ)';
        //             }
        //         }
        //     }

        //     let total = totalPrice - discount;
        //     if (total < 0) total = 0;
        //     $('#totalPrice').text(total.toLocaleString('vi-VN') + ' VNĐ');
        //     $('#total_after_discount').html(discount > 0 ?
        //         '<span class="text-success">Đã giảm: ' + discountText + '</span>' : '');
        //     $('#totalDuration').text(totalDuration + ' Phút');
        // }

        // // Gọi updateTotal khi trang tải để áp dụng giảm giá ban đầu
        // updateTotal();

        // Event listeners for updates
        $('#service').on('change', function() {
            document.getElementById('additionalServicesContainer').innerHTML = '';
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
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Hàm khởi tạo quản lý dịch vụ
            function setupServiceManagement() {
                const additionalServicesContainer = document.getElementById('additionalServicesContainer');
                const additionalServicesInput = document.getElementById('additionalServicesInput');

                // Thêm sự kiện xóa cho các nút xóa hiện có
                function setupRemoveButtons() {
                    const removeButtons = additionalServicesContainer.querySelectorAll('.remove-service-btn');
                    removeButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const serviceWrapper = this.closest('.service-wrapper');
                            if (serviceWrapper) {
                                additionalServicesContainer.removeChild(serviceWrapper);
                                updateAdditionalServicesInput();
                                updateTotal();
                            }
                        });
                    });
                }

                // Gọi hàm để thiết lập sự kiện cho các nút xóa hiện có
                setupRemoveButtons();

                // Cập nhật input ẩn chứa danh sách dịch vụ bổ sung
                function updateAdditionalServicesInput() {
                    const additionalServices = getSelectedAdditionalServices();
                    additionalServicesInput.value = JSON.stringify(additionalServices);
                }

                // Lấy danh sách ID của các dịch vụ bổ sung đã chọn
                function getSelectedAdditionalServices() {
                    return Array.from(additionalServicesContainer.querySelectorAll('.additional-service-select'))
                        .map(select => select.value)
                        .filter(value => value !== '');
                }

                // Gọi lại setupRemoveButtons khi thêm dịch vụ mới
                const originalAddAdditionalService = addAdditionalService;
                addAdditionalService = function() {
                    originalAddAdditionalService();
                    setupRemoveButtons(); // Cập nhật sự kiện cho nút xóa mới
                };
            }

            // Gọi lại setupServiceManagement để đảm bảo các nút xóa hiện có hoạt động
            setupServiceManagement();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appointmentDateInput = document.getElementById('appointment_date');
            const voucherSelect = document.getElementById('voucher_id');

            function filterVouchersByDate() {
                const selectedDate = appointmentDateInput.value;
                if (!selectedDate) return;

                Array.from(voucherSelect.options).forEach((option, idx) => {
                    if (idx === 0) return; // Bỏ qua option đầu
                    const expiredAt = option.getAttribute('data-expired-at');
                    if (expiredAt && selectedDate > expiredAt) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = '';
                    }
                });

                // Nếu option đang chọn bị ẩn thì reset về mặc định
                if (voucherSelect.selectedIndex > 0 && voucherSelect.options[voucherSelect.selectedIndex].style
                    .display === 'none') {
                    voucherSelect.selectedIndex = 0;
                }
            }

            if (appointmentDateInput && voucherSelect) {
                appointmentDateInput.addEventListener('change', filterVouchersByDate);
                filterVouchersByDate();
            }
        });
        // $(document).ready(function() {
        //     $('#voucher_id').select2({
        //         placeholder: 'Chọn hoặc tìm mã khuyến mãi',
        //         allowClear: true,
        //         width: '100%',
        //         language: {
        //             noResults: function() {
        //                 return "Không tìm thấy mã phù hợp";
        //             }
        //         }
        //     });
        // });

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
            // Main service
            const mainOpt = $('#service option:selected');
            const mainInfo = getServiceInfo(mainOpt);

            // Additional services
            const addInfo = getAdditionalServicesInfo();

            // Total before discount
            const totalPrice = mainInfo.price + addInfo.totalPrice;
            const totalDuration = mainInfo.duration + addInfo.totalDuration;

            // Voucher
            const voucherOpt = $('#voucher_id option:selected');
            const discountType = voucherOpt.data('discount-type');
            const discountValue = parseFloat(voucherOpt.data('discount-value')) || 0;
            const maxDiscount = parseFloat(voucherOpt.data('max-discount')) || 0;

            let discount = 0;
            let discountText = '';
            if ($('#voucher_id').val() && totalPrice > 0 && discountType) {
                if (discountType === 'fixed') {
                    discount = discountValue;
                    discountText = '- ' + discount.toLocaleString('vi-VN') + ' vnđ';
                } else if (discountType === 'percent') {
                    discount = Math.round(totalPrice * discountValue / 100);
                    if (maxDiscount > 0 && discount > maxDiscount) {
                        discount = maxDiscount;
                        discountText = discount.toLocaleString('vi-VN') + ' vnđ';
                    } else {
                        discountText = '- ' + discountValue + '% (' + discount.toLocaleString('vi-VN') + ' vnđ)';
                    }
                }
            }

            let total = totalPrice - discount;
            if (total < 0) total = 0;

            $('#totalPrice').text(total.toLocaleString('vi-VN') + ' vnđ');
            $('#total_after_discount').html(discount > 0 ?
                '<span class="text-success">Đã giảm: ' + discountText + '</span>' :
                '');
            $('#totalDuration').text(totalDuration + ' Phút');
        }

        $('#service').on('change', updateTotal);
        $('#voucher_id').on('select2:select', updateTotal);
        $('#voucher_id').on('change', updateTotal);
        $('#additionalServicesContainer').on('change', '.additional-service-select', updateTotal);

        // Also update when add/remove additional service
        new MutationObserver(updateTotal).observe(document.getElementById('additionalServicesContainer'), {
            childList: true,
            subtree: true
        });

        updateTotal();
    </script>

    <script>
        // hàm để xử lý khi cập nhật thành công
        function onSuccess() {
            // Lấy tham số status từ URL hiện tại
            const urlParams = new URLSearchParams(window.location.search);
            let status = urlParams.get('status') || ''; // Lấy giá trị status từ URL
            const page = urlParams.get('page') || 1; // Lấy giá trị page, mặc định là 1

            // Nếu không có status trong URL, sử dụng status từ appointment (nếu có)
            if (!status) {
                const statusSelect = document.getElementById('status');
                if (statusSelect) {
                    status = statusSelect.value || '{{ $appointment->status }}'; // Lấy từ select hoặc biến Blade
                } else {
                    status = '{{ $appointment->status }}'; // Dùng status của appointment
                }
            }

            // Tạo URL cho route appointments.index với các tham số query
            let redirectUrl = '{{ route('appointments.index') }}';
            const queryParams = [];
            if (status) {
                queryParams.push(`status=${encodeURIComponent(status)}`);
            }
            queryParams.push(`page=${encodeURIComponent(page)}`);
            if (queryParams.length > 0) {
                redirectUrl += `?${queryParams.join('&')}`;
            }

            // Chuyển hướng đến URL đã tạo
            window.location.href = redirectUrl;
        }

        // Xử lý submit form chỉnh sửa
        document.querySelector('#editBookingForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const form = this;

            // Kiểm tra form trước khi gửi
            if (!form.checkValidity()) {
                form.reportValidity(); // Hiển thị lỗi HTML5 mặc định
                return;
            }

            // Hiển thị SweetAlert2 để xác nhận
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
                    // Cửa sổ loading
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

                    // Thu thập dữ liệu từ form
                    const formData = new FormData(form);

                    // Gửi yêu cầu AJAX
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
                                throw data;
                            }
                            if (data.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message ||
                                        'Lịch hẹn đã được cập nhật thành công!',
                                    icon: 'success',
                                    customClass: {
                                        popup: 'custom-swal-popup'
                                    }
                                }).then(() => {
                                    // Chuyển hướng về trang danh sách
                                    onSuccess();
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            let errorMessage = 'Đã có lỗi xảy ra.';
                            if (error.errors) {
                                errorMessage = Object.values(error.errors).flat().join('<br>');
                            } else if (error.message) {
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
    </script>
@endsection
