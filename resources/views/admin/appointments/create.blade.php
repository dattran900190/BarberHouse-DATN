@extends('layouts.AdminLayout')

@section('title', 'Tạo đặt lịch tại tiệm')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Đặt lịch cắt tóc tại tiệm</h3>
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
                <a href="{{ url('admin/appointments/create') }}">Tạo đặt lịch</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-black align-items-center">
            <div class="card-title">Tạo đặt lịch</div>
        </div>

        <div class="card-body p-4">
            <div class="card-body">
                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: '{{ session('success') }}',
                                confirmButtonText: 'OK',
                                // timer: 3000,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                                timerProgressBar: true
                            });
                        });
                    </script>
                @endif

                <form id="bookingForm" method="POST" action="{{ route('appointments.createAppointment') }}">
                    @csrf

                    <div class="row g-3 form-group">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên <span class="required">*</span></label>
                            <input id="name" name="name" class="form-control" type="text"
                                placeholder="Nhập họ và tên" value="{{ old('name') }}">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày đặt lịch <span class="required">*</span></label>
                            <div class="date-input">
                                <input type="text" class="form-control" id="appointment_date" name="appointment_date"
                                    placeholder="Chọn thời điểm"
                                    value="{{ old('appointment_date', $currentDate) }} style="background-color: #fff
                                    !important;" readonly>
                            </div>
                            @error('appointment_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Chọn chi nhánh <span class="required">*</span></label>
                        <div class="position-relative">
                            <!-- Hidden input for form submission -->
                            <input type="hidden" id="branch_input" name="branch_id" value="{{ old('branch_id') }}">

                            <!-- Branch selection cards -->
                            <div class="branch-cards-container" id="branchCards">
                                @foreach ($branches as $branch)
                                    <div class="branch-card" data-branch-id="{{ $branch->id }}"
                                        data-branch-name="{{ $branch->name }}">
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
                                value="{{ old('appointment_time', $currentTime) }}">
                            @error('appointment_time')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Khuyến mãi</label>
                            <input type="hidden" id="service_price" value="{{ $service->price ?? 0 }}">
                            <select name="voucher_code" id="voucher_id">
                                <option value="">Không sử dụng mã giảm giá</option>
                                @foreach ($vouchers as $voucher)
                                    <option value="{{ $voucher->promotion->code }}"
                                        data-discount-type="{{ $voucher->promotion->discount_type }}"
                                        data-discount-value="{{ $voucher->promotion->discount_value }}"
                                        data-expired-at="{{ $voucher->promotion->end_date }}"
                                        data-voucher-id="{{ $voucher->id }}"
                                        data-max-discount="{{ $voucher->promotion->max_discount_amount ?? 0 }}">
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
                                        data-max-discount="{{ $promotion->max_discount_amount ?? 0 }}">
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
                            <div class="servicesList" data-service-index="0">
                                <div class="d-flex align-items-start gap-2">

                                    <div class="flex-grow-1 position-relative col-md-8">
                                        <select id="service" name="service_id" class="form-control service-select"
                                            data-index="0">
                                            <option value="">Chọn dịch vụ</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}" data-name="{{ $service->name }}"
                                                    data-price="{{ $service->price }}"
                                                    data-duration="{{ $service->duration }}"
                                                    data-is-combo="{{ $service->is_combo ? '1' : '0' }}">
                                                    {{ $service->name }} – ({{ number_format($service->price) }}đ)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('service_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <button class="add-service-btn btn btn-outline-primary col-md-4" type="button"
                                        id="addServiceBtn">
                                        Thêm dịch vụ
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="additionalServicesContainer" class="mt-2"></div>
                        <input type="hidden" name="additional_services" id="additionalServicesInput">
                    </div>


                    <div class="form-group">
                        <label class="form-label">Yêu cầu kĩ thuật viên <span class="required">*</span></label>
                        <div class="position-relative">
                            <select class="form-select d-none" id="barber" name="barber_id">
                                <option value="">Chọn kĩ thuật viên</option>
                                @foreach ($barbers as $barber)
                                    <option value="{{ $barber->id }}"
                                        {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->name }}</option>
                                @endforeach
                            </select>
                            <div class="barber-cards-wrapper">
                                <div class="barber-cards-container" id="barberCards">
                                    @foreach ($barbers as $barber)
                                        <div class="barber-card" data-barber-id="{{ $barber->id }}"
                                            data-barber-name="{{ $barber->name }}">
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
                            placeholder="ghi chú có thể bỏ trống ..." id="notes">{{ old('note') }}</textarea>
                    </div>

                    <div class="service-info form-group">
                        <p>Tổng tiền: <strong id="totalPrice">0 vnđ</strong></p>
                        <span id="total_after_discount"></span>
                        <p>Thời lượng dự kiến: <strong id="totalDuration">0 Phút</strong></p>
                    </div>

                    <div class="form-btn mt-3">
                        <button type="submit" class="btn btn-sm btn-outline-success booking-btn"
                            data-id="{{ $service->id }}">
                            <i class="fas fa-plus"></i> Tạo đặt lịch
                        </button>
                        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </form>
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
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Barber card selection
            setupBarberCardSelection();
            // Branch card selection
            setupBranchCardSelection();
            // Time card selection
            setupTimeCardSelection();

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
        });

        // chọn kỹ thuật viên
        function setupBarberCardSelection() {
            const barberCards = document.querySelectorAll('.barber-card');
            const barberSelect = document.getElementById('barber');

            barberCards.forEach(card => {
                card.addEventListener('click', function() {
                    // xóa lựa chọn trước đó
                    barberCards.forEach(c => c.classList.remove('selected'));

                    // chọn card hiện tại
                    this.classList.add('selected');

                    // cập nhật hidden select
                    const barberId = this.dataset.barberId;
                    const barberName = this.dataset.barberName;

                    // xóa tất cả options
                    barberSelect.innerHTML = '<option value="">Chọn kỹ thuật viên</option>';

                    // thêm option đã chọn
                    const option = document.createElement('option');
                    option.value = barberId;
                    option.textContent = barberName;
                    option.selected = true;
                    barberSelect.appendChild(option);

                    // trigger change event cho bất kỳ listener nào
                    barberSelect.dispatchEvent(new Event('change'));
                });
            });

            // chọn kỹ thuật viên
            const preSelectedBarberId = barberSelect.value;
            if (preSelectedBarberId) {
                const selectedCard = document.querySelector(`[data-barber-id="${preSelectedBarberId}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }
        }

        // Setup branch card selection
        function setupBranchCardSelection() {
            const branchCards = document.querySelectorAll('.branch-card');
            const branchInput = document.getElementById('branch_input');

            branchCards.forEach(card => {
                card.addEventListener('click', function() {
                    // xóa lựa chọn trước đó
                    branchCards.forEach(c => c.classList.remove('selected'));

                    // chọn card hiện tại
                    this.classList.add('selected');

                    // cập nhật hidden input
                    const branchId = this.dataset.branchId;
                    branchInput.value = branchId;

                    // trigger change event cho bất kỳ listener nào
                    branchInput.dispatchEvent(new Event('change'));
                });
            });

            // chọn chi nhánh
            const preSelectedBranchId = branchInput.value;
            if (preSelectedBranchId) {
                const selectedCard = document.querySelector(`[data-branch-id="${preSelectedBranchId}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }
        }
    </script>

    <script>
        // khởi tạo hệ thống đặt lịch
        let selectedBranch = null;
        let selectedTime = null;
        let barberSilent = false;
        let services = [];
        let serviceCounter = 0;

        document.addEventListener('DOMContentLoaded', function() {
            setupBranchSelection();
            setupTimeSelection();
            setupBarberCheckbox();
            setupServiceManagement();
            setupFormValidation();
            setMinDate();
        });

        document.querySelectorAll('.branch-icon').forEach(icon => {
            icon.addEventListener('click', function(event) {
                event.stopPropagation(); // Ngăn sự kiện lan ra .branch-item
                const googleMapUrl = this.getAttribute('data-value');
                if (googleMapUrl) {
                    window.open(googleMapUrl, '_blank'); // Mở Google Maps trong tab mới
                }
            });
        });

        // chọn chi nhánh
        function setupBranchSelection() {
            const branchItems = document.querySelectorAll('.branch-item');
            branchItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Remove previous selection
                    branchItems.forEach(branch => branch.classList.remove('selected'));

                    // Select current branch
                    this.classList.add('selected');
                    selectedBranch = this.dataset.branch;

                    validateForm();
                });
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khai báo các phần tử DOM
            const branchContainer = document.getElementById('branch');
            const branchInput = document.getElementById('branch_input');
            const appointmentDate = document.getElementById('appointment_date');
            const appointmentTime = document.getElementById('appointment_time');
            const serviceSelect = document.getElementById('service');
            const additionalServicesInput = document.getElementById('additionalServicesInput');
            const barberSelect = document.getElementById('barber');
            const timeGrid = document.getElementById('timeGrid');
            const addServiceBtn = document.getElementById('addServiceBtn');
            const additionalServicesContainer = document.getElementById('additionalServicesContainer');

            let lastRequest = null;

            // Khai báo danh sách dịch vụ toàn cục
            const allServices = @json($services);

            // Hàm cập nhật danh sách kỹ thuật viên
            function updateBarbers() {
                const branch = branchInput.value || 'null';
                const date = appointmentDate.value || '{{ $currentDate }}';
                const time = appointmentTime.value || '{{ $currentTime }}';
                const service = serviceSelect.value || 'null';
                let additional = [];
                try {
                    additional = JSON.parse(additionalServicesInput.value || '[]');
                } catch (e) {
                    console.error('Error parsing additional services:', e);
                    additional = [];
                }

                // Ngăn yêu cầu trùng lặp
                const requestKey = `${branch}|${date}|${time}|${service}|${JSON.stringify(additional.sort())}`;
                if (lastRequest === requestKey) return;
                lastRequest = requestKey;

                // Hiển thị trạng thái loading
                barberSelect.innerHTML = '<option value="">Đang tải...</option>';

                // Tạo URL cho yêu cầu AJAX
                const url =
                    `/get-available-barbers-by-date/${encodeURIComponent(branch)}/${encodeURIComponent(date)}/${encodeURIComponent(time)}/${encodeURIComponent(service)}?additional_services=${encodeURIComponent(JSON.stringify(additional))}`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.error || `HTTP error! Status: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update hidden select
                        barberSelect.innerHTML = '<option value="">Chọn kỹ thuật viên</option>';

                        // Update barber cards container
                        const barberCardsContainer = document.getElementById('barberCards');
                        const barberCardsWrapper = barberCardsContainer.parentElement;

                        // Remove existing notice
                        const existingNotice = barberCardsWrapper.querySelector('.all-barbers-notice');
                        if (existingNotice) existingNotice.remove();

                        if (data.error) {
                            barberSelect.innerHTML = `<option value="">${data.error}</option>`;
                            barberCardsContainer.innerHTML =
                                `<div class="text-center text-muted py-4">${data.error}</div>`;
                            // Remove scroll indicator if exists
                            const existingIndicator = barberCardsWrapper.querySelector('.scroll-indicator');
                            if (existingIndicator) existingIndicator.remove();
                        } else if (data.length > 0) {
                            // Clear existing cards
                            barberCardsContainer.innerHTML = '';

                            data.forEach(barber => {
                                // Add to hidden select
                                const option = document.createElement('option');
                                option.value = barber.id;
                                option.text = barber.name;
                                barberSelect.appendChild(option);

                                // Create barber card
                                const card = document.createElement('div');
                                card.className = 'barber-card';
                                card.dataset.barberId = barber.id;
                                card.dataset.barberName = barber.name;

                                const skillLevels = {
                                    'assistant': 'Thử việc',
                                    'junior': 'Sơ cấp',
                                    'senior': 'Chuyên nghiệp',
                                    'master': 'Bậc thầy',
                                    'expert': 'Chuyên gia'
                                };
                                const skillLevelColors = {
                                    'assistant': 'secondary',
                                    'junior': 'info',
                                    'senior': 'primary',
                                    'master': 'success',
                                    'expert': 'warning'
                                };
                                const levelKey = barber.skill_level;

                                const avatar = barber.avatar ?
                                    `<img src="/storage/${barber.avatar}" alt="${barber.name}" class="barber-img">` :
                                    `<div class="barber-img-placeholder"><i class="fas fa-user"></i></div>`;

                                const ratingStars = Array.from({
                                    length: 5
                                }, (_, i) => {
                                    const starClass = i < barber.rating_avg ?
                                        'fas fa-star text-warning' : 'far fa-star text-muted';
                                    return `<i class="${starClass}"></i>`;
                                }).join('');

                                card.innerHTML = `
                                <div class="barber-avatar">
                                    ${avatar}
                                </div>
                                <div class="barber-info">
                                    <h6 class="barber-name">${barber.name}</h6>
                                    <div class="barber-rating">
                                        ${ratingStars}
                                        <span class="rating-text">(${parseFloat(barber.rating_avg || 0).toFixed(1)})</span>
                                    </div>
                                    <div class="barber-skill">
                                        <span class="skill-badge bg-${skillLevelColors[levelKey] || 'dark'}">${skillLevels[levelKey] || 'Chuyên nghiệp'}</span>
                                    </div>
                                </div>
                            `;

                                barberCardsContainer.appendChild(card);
                            });

                            // Add scroll indicator if more than 5 barbers
                            const existingIndicator = barberCardsWrapper.querySelector('.scroll-indicator');
                            if (existingIndicator) existingIndicator.remove();

                            if (data.length > 12) {
                                const scrollIndicator = document.createElement('div');
                                scrollIndicator.className = 'scroll-indicator';
                                scrollIndicator.innerHTML = `
                                <i class="fas fa-chevron-down"></i>
                                <span>Cuộn để xem thêm thợ</span>
                            `;
                                barberCardsWrapper.appendChild(scrollIndicator);
                            }

                            // Add notice if no branch is selected
                            const branchInput = document.getElementById('branch_input');
                            if (!branchInput.value) {
                                const notice = document.createElement('div');
                                notice.className = 'all-barbers-notice';
                                notice.innerHTML = `
                                <i class="fas fa-info-circle"></i>
                                <span>Đang hiển thị tất cả thợ. Vui lòng chọn chi nhánh để xem thợ cụ thể.</span>
                            `;
                                barberCardsWrapper.insertBefore(notice, barberCardsContainer);
                            }

                            // Re-setup barber card selection
                            setupBarberCardSelection();
                        } else {
                            barberSelect.innerHTML =
                                '<option value="">Không có kỹ thuật viên khả dụng</option>';
                            barberCardsContainer.innerHTML =
                                '<div class="text-center text-muted py-4">Không có kỹ thuật viên khả dụng</div>';
                            // Remove scroll indicator if exists
                            const existingIndicator = barberCardsWrapper.querySelector('.scroll-indicator');
                            if (existingIndicator) existingIndicator.remove();

                            // Add notice if no branch is selected
                            const branchInput = document.getElementById('branch_input');
                            if (!branchInput.value) {
                                const notice = document.createElement('div');
                                notice.className = 'all-barbers-notice';
                                notice.innerHTML = `
                                <i class="fas fa-info-circle"></i>
                                <span>Đang hiển thị tất cả thợ. Vui lòng chọn chi nhánh để xem thợ cụ thể.</span>
                            `;
                                barberCardsWrapper.insertBefore(notice, barberCardsContainer);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching barbers:', error.message);
                        barberSelect.innerHTML = `<option value="">Lỗi: ${error.message}</option>`;
                    });
            }

            // Xử lý sự kiện nhấp chuột trên các ô giờ
            if (timeGrid) {
                timeGrid.querySelectorAll('.time-slot').forEach(slot => {
                    slot.addEventListener('click', function() {
                        timeGrid.querySelectorAll('.time-slot').forEach(s => s.classList.remove(
                            'selected'));
                        this.classList.add('selected');
                        appointmentTime.value = this.getAttribute('data-value');
                        updateBarbers();
                    });
                });

                // Đánh dấu ô giờ nếu có giá trị cũ
                const oldTime = appointmentTime.value;
                if (oldTime) {
                    const slot = timeGrid.querySelector(`.time-slot[data-value="${oldTime}"]`);
                    if (slot) slot.classList.add('selected');
                }
            }

            // Xử lý thêm/xóa dịch vụ thêm
            if (addServiceBtn && additionalServicesContainer) {
                function addAdditionalService() {
                    const serviceWrapper = document.createElement('div');
                    serviceWrapper.className = 'service-wrapper mt-2 d-flex align-items-center';

                    const serviceSelect = document.createElement('select');
                    serviceSelect.className = 'form-control additional-service-select';
                    serviceSelect.name = 'additional_services[]';

                    // Lấy dịch vụ chính đã chọn
                    const mainServiceSelect = document.getElementById('service');
                    const mainServiceOption = mainServiceSelect.options[mainServiceSelect.selectedIndex];
                    const isMainServiceCombo = mainServiceOption.getAttribute('data-is-combo') === '1';


                    // Tạo danh sách tùy chọn dựa trên loại dịch vụ chính
                    let options = '<option value="">Chọn dịch vụ thêm</option>';

                    // Nếu dịch vụ chính là combo, không cho thêm dịch vụ thêm
                    if (isMainServiceCombo) {
                        serviceSelect.innerHTML = options;
                    } else {
                        // Nếu dịch vụ chính là dịch vụ riêng, chỉ cho thêm dịch vụ riêng khác

                        // Lấy tất cả options từ dropdown chính
                        const allServiceOptions = mainServiceSelect.querySelectorAll('option[value]');

                        allServiceOptions.forEach(option => {
                            const serviceId = option.value;
                            const serviceName = option.textContent;
                            const isServiceCombo = option.getAttribute('data-is-combo') === '1';

                            //     isServiceCombo);

                            // Lấy danh sách dịch vụ đã được chọn
                            const selectedServices = getSelectedAdditionalServices();
                            const isAlreadySelected = selectedServices.includes(serviceId);


                            // Chỉ thêm dịch vụ riêng (không phải combo), không phải dịch vụ chính, và chưa được chọn
                            if (!isServiceCombo &&
                                serviceId != mainServiceSelect.value &&
                                !isAlreadySelected) {

                                // Lấy thông tin giá và thời lượng từ data attributes
                                const price = option.getAttribute('data-price');
                                const duration = option.getAttribute('data-duration');
                                const is_combo = option.getAttribute('data-is-combo');

                                options += `
                                    <option value="${serviceId}" data-name="${option.getAttribute('data-name')}"
                                        data-price="${price}" data-duration="${duration}" data-is-combo="${is_combo}">
                                        ${serviceName}
                                    </option>
                                `;
                            } else {
                                //     isServiceCombo ? 'is combo' :
                                //     serviceId == mainServiceSelect.value ? 'is main service' :
                                //     'already selected');
                            }
                        });
                    }

                    serviceSelect.innerHTML = options;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-outline-danger btn-sm ms-2 col-md-1';
                    removeBtn.style.padding = '10px 0.5rem';
                    removeBtn.style.border = '1px solid #ccc';
                    removeBtn.innerHTML = '<i class="fa fa-times"></i> Xóa';
                    removeBtn.addEventListener('click', function() {
                        additionalServicesContainer.removeChild(serviceWrapper);
                        updateAdditionalServicesInput();
                        updateBarbers();
                        // Cập nhật lại dropdown sau khi xóa
                        updateAdditionalServicesDropdowns();
                    });

                    serviceWrapper.appendChild(serviceSelect);
                    serviceWrapper.appendChild(removeBtn);
                    additionalServicesContainer.appendChild(serviceWrapper);
                    updateAdditionalServicesInput();
                    updateBarbers();

                    // Cập nhật lại tất cả dropdown sau khi thêm mới
                    updateAdditionalServicesDropdowns();
                }

                // Hàm cập nhật tất cả dropdown dịch vụ thêm
                function updateAdditionalServicesDropdowns() {
                    const mainServiceSelect = document.getElementById('service');
                    const mainServiceOption = mainServiceSelect.options[mainServiceSelect.selectedIndex];
                    const isMainServiceCombo = mainServiceOption.getAttribute('data-is-combo') === '1';

                    // Nếu dịch vụ chính là combo, xóa tất cả dropdown bổ sung
                    if (isMainServiceCombo) {
                        additionalServicesContainer.innerHTML = '';
                        updateAdditionalServicesInput();
                        return;
                    }

                    // Cập nhật từng dropdown
                    const additionalSelects = additionalServicesContainer.querySelectorAll(
                        '.additional-service-select');
                    additionalSelects.forEach((select, index) => {
                        const currentValue = select.value;
                        let options = '';

                        // Lấy tất cả options từ dropdown chính
                        const allServiceOptions = mainServiceSelect.querySelectorAll('option[value]');

                        allServiceOptions.forEach(option => {
                            const serviceId = option.value;
                            const serviceName = option.textContent;
                            const isServiceCombo = option.getAttribute('data-is-combo') === '1';

                            // Lấy danh sách dịch vụ đã được chọn (trừ dropdown hiện tại)
                            const selectedServices = getSelectedAdditionalServices().filter(id =>
                                id !== currentValue);
                            const isAlreadySelected = selectedServices.includes(serviceId);

                            // Chỉ thêm dịch vụ riêng, không phải dịch vụ chính, và chưa được chọn
                            if (!isServiceCombo &&
                                serviceId != parseInt(mainServiceSelect.value) &&
                                !isAlreadySelected) {
                                const selected = serviceId.toString() === currentValue ?
                                    'selected' : '';
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

                // Hàm lấy danh sách dịch vụ đã được chọn
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
                        // Xóa thông báo cũ nếu có
                        const existingNotices = additionalServicesContainer.querySelectorAll(
                            '.service-notice');
                        existingNotices.forEach(notice => notice.remove());

                        // Tạo thông báo đẹp
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

                        // Tự động xóa thông báo sau 5 giây
                        setTimeout(() => {
                            if (noticeNoSelectMainService.parentNode) {
                                noticeNoSelectMainService.remove();
                            }
                        }, 5000);
                        return;
                    }

                    // Kiểm tra xem dịch vụ chính có phải combo không
                    const mainServiceOption = serviceSelect.options[serviceSelect.selectedIndex];
                    const isMainServiceCombo = mainServiceOption.getAttribute('data-is-combo') === '1';

                    if (isMainServiceCombo) {
                        // Xóa thông báo cũ nếu có
                        const existingNotices = additionalServicesContainer.querySelectorAll(
                            '.service-notice');
                        existingNotices.forEach(notice => notice.remove());

                        // Tạo thông báo đẹp cho combo
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

                        // Tự động xóa thông báo sau 5 giây
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
                        updateBarbers();
                        // Cập nhật lại tất cả dropdown sau khi thay đổi
                        updateAdditionalServicesDropdowns();
                    }
                });
            }

            // Thêm sự kiện lắng nghe cho các trường
            appointmentDate.addEventListener('change', updateBarbers);
            branchInput.addEventListener('change', updateBarbers);
            appointmentTime.addEventListener('change', updateBarbers);
            serviceSelect.addEventListener('change', function() {
                // Xóa tất cả dịch vụ bổ sung khi thay đổi dịch vụ chính
                additionalServicesContainer.innerHTML = '';
                updateAdditionalServicesInput();
                updateBarbers();
                // Cập nhật lại dropdown nếu cần
                updateAdditionalServicesDropdowns();
            });
            additionalServicesInput.addEventListener('change', updateBarbers);

            // Xử lý sự kiện nhấp chuột trên các ô giờ
            if (timeGrid) {
                timeGrid.querySelectorAll('.time-slot').forEach(slot => {
                    slot.addEventListener('click', function() {
                        timeGrid.querySelectorAll('.time-slot').forEach(s => s.classList.remove(
                            'selected'));
                        this.classList.add('selected');
                        appointmentTime.value = this.getAttribute('data-value');
                        updateBarbers();
                    });
                });

                // Đánh dấu ô giờ nếu có giá trị cũ
                const oldTime = appointmentTime.value;
                if (oldTime) {
                    const slot = timeGrid.querySelector(`.time-slot[data-value="${oldTime}"]`);
                    if (slot) slot.classList.add('selected');
                }
            }

            // Khởi tạo flatpickr
            flatpickr(appointmentDate, {
                locale: 'vn',
                minDate: 'today',
                maxDate: new Date().fp_incr(90),
                dateFormat: 'Y-m-d',
                disableMobile: true,
                defaultDate: '{{ $currentDate }}',
                onChange: function(selectedDates, dateStr) {
                    appointmentDate.value = dateStr;
                    updateBarbers();
                }
            });
            // Đặt giờ mặc định và cập nhật kỹ thuật viên
            appointmentTime.value = '{{ $currentTime }}';

            // Trigger cập nhật ban đầu nếu có dữ liệu cũ
            if (serviceSelect.value || branchInput.value || appointmentDate.value) {
                updateBarbers();
            }
        });
    </script>
    <script>
        serviceSelect.addEventListener('change', function() {
            const sel = this.options[this.selectedIndex];
        });
    </script>

    <script>

        document.querySelector('.booking-btn').addEventListener('click', function(event) {
            event.preventDefault();
            const form = document.getElementById('bookingForm');

            // Kiểm tra form trước khi gửi
            if (!form.checkValidity()) {
                form.reportValidity(); // Hiển thị lỗi HTML5 mặc định
                return;
            }

                // Hiển thị SweetAlert2 để xác nhận khi đã đăng nhập
                Swal.fire({
                    title: 'Xác nhận đặt lịch',
                    text: 'Bạn có chắc chắn muốn đặt lịch hẹn này?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Đặt lịch',
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
                        fetch('{{ route('appointments.createAppointment') }}', {
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
                                    if (formData.get('payment_method') === 'vnpay') {
                                        // Chuyển hướng đến thanh toán VNPay
                                        const vnpayForm = document.createElement('form');
                                        vnpayForm.method = 'POST';
                                        vnpayForm.action = '{{ route('client.payment.vnpay') }}';
                                        vnpayForm.innerHTML = `
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="appointment_id" value="${data.appointment_id}">
                                `;
                                        document.body.appendChild(vnpayForm);
                                        vnpayForm.submit();
                                    } else {
                                        Swal.fire({
                                            title: 'Thành công!',
                                            text: data.message,
                                            icon: 'success',
                                            customClass: {
                                                popup: 'custom-swal-popup'
                                            }
                                        }).then(() => {
                                            // Chuyển hướng về trang lịch sử đặt lịch
                                            window.location.href =
                                                '{{ route('appointments.index') }}';
                                        });
                                    }
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
        $(document).ready(function() {
            $('#voucher_id').select2({
                placeholder: 'Chọn hoặc tìm mã khuyến mãi',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Không tìm thấy mã phù hợp";
                    }
                }
            });
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
@endsection
