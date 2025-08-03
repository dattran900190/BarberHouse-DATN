@extends('layouts.ClientLayout')

@section('title-page')
    Đặt lịch Baber House
@endsection

@section('content')
    <main class="container" style="padding: 10% 0;">
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

        <div class="booking-container">
            <!-- Header -->
            <div class="booking-header">
                <h1 class="booking-title">Đặt lịch cắt tóc</h1>
                <p class="booking-subtitle">Giờ mở cửa: 08:00 - 19:30</p>
                <p class="booking-note">
                    <i class="fas fa-exclamation-circle"></i>
                    Vui lòng nhập thông tin bắt buộc để lọc thợ phù hợp
                </p>
            </div>

            <form id="bookingForm" method="POST" action="{{ route('dat-lich.store') }}">
                @csrf

                <div class="row align-items-center">
                    <div class="col-sm-1">
                        <div class="form-group mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="other_person"
                                name="other_person" {{ old('other_person') ? 'checked' : '' }}>
                        </div>
                    </div>
                    <div class="col-sm-11">
                        <div class="form-group mb-3">
                            <label class="form-check-label" for="other_person">
                                Tôi muốn đặt lịch cho người khác
                            </label>
                        </div>
                    </div>
                </div>


                <div id="other-info" style="{{ old('other_person') ? '' : 'display:none;' }}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Họ và tên <span class="required">*</span></label>
                                <input id="name" name="name" class="form-control" type="text"
                                    placeholder="Nhập họ và tên" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Số điện thoại <span class="required">*</span></label>
                                <input id="phone" name="phone" class="form-control" type="tel"
                                    placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input id="email" name="email" value="{{ old('email') }}"class="form-control"
                            placeholder="Nhập email" type="text">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ngày đặt lịch <span class="required">*</span></label>
                    <div class="date-input">
                        <input type="text" class="form-control" id="appointment_date" name="appointment_date"
                            placeholder="Chọn thời điểm" readonly>
                    </div>
                    @error('appointment_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Chọn chi nhánh <span class="required">*</span></label>
                    <div class="position-relative">
                        <!-- Hidden input for form submission -->
                        <input type="hidden" id="branch_input" name="branch_id" value="{{ old('branch_id') }}">
                        
                        <!-- Branch selection cards -->
                        <div class="branch-cards-container" id="branchCards">
                            @foreach ($branches as $branch)
                                <div class="branch-card" data-branch-id="{{ $branch->id }}" data-branch-name="{{ $branch->name }}">
                                    <div class="branch-icon-wrapper">
                                        <i class="fas fa-map-marker-alt branch-icon" data-value="{{ $branch->google_map_url }}"></i>
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

                <div class="form-group">
                    <label class="form-label">Chọn khung giờ dịch vụ <span class="required">*</span></label>
                    <input type="hidden" name="appointment_time" id="appointment_time"
                        value="{{ old('appointment_time') }}">
                    <div class="time-grid" id="timeGrid">
                        <span class="time-slot" data-value="08:00">08:00</span>
                        <span class="time-slot" data-value="08:30">08:30</span>
                        <span class="time-slot" data-value="09:00">09:00</span>
                        <span class="time-slot" data-value="09:30">09:30</span>
                        <span class="time-slot" data-value="10:00">10:00</span>
                        <span class="time-slot" data-value="10:30">10:30</span>
                        <span class="time-slot" data-value="11:00">11:00</span>
                        <span class="time-slot" data-value="11:30">11:30</span>
                        <span class="time-slot" data-value="12:00">12:00</span>
                        <span class="time-slot" data-value="12:30">12:30</span>
                        <span class="time-slot" data-value="13:00">13:00</span>
                        <span class="time-slot" data-value="13:30">13:30</span>
                        <span class="time-slot" data-value="14:00">14:00</span>
                        <span class="time-slot" data-value="14:30">14:30</span>
                        <span class="time-slot" data-value="15:00">15:00</span>
                        <span class="time-slot" data-value="15:30">15:30</span>
                        <span class="time-slot" data-value="16:00">16:00</span>
                        <span class="time-slot" data-value="16:30">16:30</span>
                        <span class="time-slot" data-value="17:00">17:00</span>
                        <span class="time-slot" data-value="17:30">17:30</span>
                        <span class="time-slot" data-value="18:00">18:00</span>
                        <span class="time-slot" data-value="18:30">18:30</span>
                        <span class="time-slot" data-value="19:00">19:00</span>
                        <span class="time-slot" data-value="19:30">19:30</span>
                    </div>
                    @error('appointment_time')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


                <div class="form-group">
                    <label class="form-label">Dịch vụ <span class="required">*</span></label>
                    <div id="servicesList">
                        <div class="service-item" data-service-index="0">
                            <div class="position-relative">
                                <select id="service" name="service_id" class="form-select service-select"
                                    data-index="0">
                                    <option value="">Chọn dịch vụ chính</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}" data-name="{{ $service->name }}"
                                            data-price="{{ $service->price }}" data-duration="{{ $service->duration }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->name }} – {{ '(' . number_format($service->price) . 'đ)' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div id="additionalServicesContainer" class="mt-2"></div>
                    <input type="hidden" name="additional_services" id="additionalServicesInput">
                    <button class="add-service-btn mt-2" type="button" id="addServiceBtn">Thêm dịch vụ</button>
                </div>

                <div class="form-group">
                    <label class="form-label">Yêu cầu kĩ thuật viên <span class="required">*</span></label>
                    <div class="position-relative">
                        <!-- Hidden select for form submission -->
                        <select class="form-select d-none" id="barber" name="barber_id">
                            <option value="">Chọn kĩ thuật viên</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}"
                                    {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Barber selection cards -->
                        <div class="barber-cards-wrapper">
                            @if(!request('branch_id'))
                                <div class="all-barbers-notice">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Đang hiển thị tất cả thợ. Vui lòng chọn chi nhánh để xem thợ cụ thể.</span>
                                </div>
                            @endif
                            <div class="barber-cards-container" id="barberCards">
                                @foreach ($barbers as $barber)
                                    <div class="barber-card" data-barber-id="{{ $barber->id }}" data-barber-name="{{ $barber->name }}">
                                        <div class="barber-avatar">
                                            @if($barber->avatar)
                                                <img src="{{ asset('storage/' . $barber->avatar) }}" alt="{{ $barber->name }}" class="barber-img">
                                            @else
                                                <div class="barber-img-placeholder">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="barber-info">
                                            <h6 class="barber-name">{{ $barber->name }}</h6>
                                            <div class="barber-rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $barber->rating_avg)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-muted"></i>
                                                    @endif
                                                @endfor
                                                <span class="rating-text">({{ number_format($barber->rating_avg, 1) }})</span>
                                            </div>
                                                                                    <div class="barber-skill">
                                            @php
                                                $skillLevels = [
                                                    'assistant' => 'Thử việc',
                                                    'junior' => 'Sơ cấp',
                                                    'senior' => 'Chuyên nghiệp',
                                                    'master' => 'Bậc thầy',
                                                    'expert' => 'Chuyên gia'
                                                ];
                                                $skillLevelColors = [
                                                    'assistant' => 'secondary',
                                                    'junior' => 'info',
                                                    'senior' => 'primary',
                                                    'master' => 'success',
                                                    'expert' => 'warning'
                                                ];
                                                $levelKey = $barber->skill_level;
                                                $levelText = $skillLevels[$levelKey] ?? 'Chuyên nghiệp';
                                                $levelColor = $skillLevelColors[$levelKey] ?? 'dark';
                                            @endphp
                                            <span class="skill-badge bg-{{ $levelColor }}">{{ $levelText }}</span>
                                        </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if(count($barbers) > 5)
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
                    <label class="form-label">Khuyến mãi</label>
                    <input type="hidden" id="service_price" value="{{ $service->price ?? 0 }}">
                    <select name="voucher_code" id="voucher_id" class="form-control">
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
                            <option value="{{ $promotion->code }}" data-discount-type="{{ $promotion->discount_type }}"
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

                <div class="form-group">
                    <textarea class="form-control notes-textarea" name="note" placeholder="ghi chú có thể bỏ trống ..."
                        id="notes">{{ old('note') }}</textarea>
                </div>

                <div class="service-info">
                    <p>Tổng tiền: <strong id="totalPrice">0 vnđ</strong></p>
                    <span id="total_after_discount"></span>
                    <p>Thời lượng dự kiến: <strong id="totalDuration">0 Phút</strong></p>
                </div>

                <div class="form-group">
                    <label class="form-label me-3">Phương thức thanh toán <span class="required">*</span></label>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_cash"
                            value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_cash">Tiền mặt</label>
                    </div>

                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="payment_method" id="payment_vnpay"
                            value="vnpay" {{ old('payment_method') == 'vnpay' ? 'checked' : '' }}>
                        <label class="form-check-label" for="payment_vnpay">VNPay</label>
                    </div>

                    @error('payment_method')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>



                <div class="form-btn mt-3">
                    <button type="submit" class="booking-btn btn-outline-booking " style="padding: 16px 24px;" data-id="{{ $service->id }}">
                        Đặt lịch
                    </button>
                </div>
            </form>
        </div>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }
        
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
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
            margin-top: 10px;
            padding-top: 10px;
            padding-bottom: 25px;
            max-height: 400px; /* Giới hạn chiều cao */
            overflow-y: auto; /* Cho phép scroll */
            padding-right: 10px; /* Tạo khoảng cách cho scrollbar */
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
            0%, 20%, 50%, 80%, 100% {
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
            
            .barber-img, .barber-img-placeholder {
                width: 50px;
                height: 50px;
            }
            
            .barber-name {
                font-size: 14px;
            }
        }
        
        /* Branch Cards Styles */
        .branch-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        
        .branch-card {
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
        
        .branch-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
        }
        
        .branch-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
        }
        
        .branch-card.selected::after {
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
        
        .branch-icon-wrapper {
            margin-right: 15px;
            flex-shrink: 0;
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .branch-icon {
            color: white;
            font-size: 20px;
        }
        
        .branch-info {
            flex: 1;
        }
        
        .branch-name {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        
        .branch-address, .branch-hours {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
            font-size: 12px;
            color: #6c757d;
        }
        
        .branch-address i, .branch-hours i {
            margin-right: 5px;
            font-size: 10px;
        }
        
        /* Time Grid Styles */
        .time-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 15px;
        }
        
        .time-slot {
            padding: 12px 8px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            color: #333;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: #fff;
            position: relative;
        }
        
        .time-slot:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            transform: translateY(-2px);
            background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
        }
        
        .time-slot.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
            color: #155724;
        }
        
        .time-slot.selected::after {
            content: '✓';
            position: absolute;
            top: 5px;
            right: 5px;
            background: #28a745;
            color: white;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
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
    </style>
@endsection


@section('scripts')
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
        
        // Setup barber card selection
        function setupBarberCardSelection() {
            const barberCards = document.querySelectorAll('.barber-card');
            const barberSelect = document.getElementById('barber');
            
            barberCards.forEach(card => {
                card.addEventListener('click', function() {
                    // Remove previous selection
                    barberCards.forEach(c => c.classList.remove('selected'));
                    
                    // Select current card
                    this.classList.add('selected');
                    
                    // Update hidden select
                    const barberId = this.dataset.barberId;
                    const barberName = this.dataset.barberName;
                    
                    // Clear previous options
                    barberSelect.innerHTML = '<option value="">Chọn kỹ thuật viên</option>';
                    
                    // Add selected option
                    const option = document.createElement('option');
                    option.value = barberId;
                    option.textContent = barberName;
                    option.selected = true;
                    barberSelect.appendChild(option);
                    
                    // Trigger change event for any existing listeners
                    barberSelect.dispatchEvent(new Event('change'));
                });
            });
            
            // Set initial selection if there's a pre-selected value
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
                    // Remove previous selection
                    branchCards.forEach(c => c.classList.remove('selected'));
                    
                    // Select current card
                    this.classList.add('selected');
                    
                    // Update hidden input
                    const branchId = this.dataset.branchId;
                    branchInput.value = branchId;
                    
                    // Trigger change event for any existing listeners
                    branchInput.dispatchEvent(new Event('change'));
                });
            });
            
            // Set initial selection if there's a pre-selected value
            const preSelectedBranchId = branchInput.value;
            if (preSelectedBranchId) {
                const selectedCard = document.querySelector(`[data-branch-id="${preSelectedBranchId}"]`);
                if (selectedCard) {
                    selectedCard.classList.add('selected');
                }
            }
        }
        
        // Setup time card selection
        function setupTimeCardSelection() {
            const timeSlots = document.querySelectorAll('.time-slot');
            const timeInput = document.getElementById('appointment_time');
            
            timeSlots.forEach(slot => {
                slot.addEventListener('click', function() {
                    // Remove previous selection
                    timeSlots.forEach(s => s.classList.remove('selected'));
                    
                    // Select current slot
                    this.classList.add('selected');
                    
                    // Update hidden input
                    const timeValue = this.dataset.value;
                    timeInput.value = timeValue;
                    
                    // Trigger change event for any existing listeners
                    timeInput.dispatchEvent(new Event('change'));
                });
            });
            
            // Set initial selection if there's a pre-selected value
            const preSelectedTime = timeInput.value;
            if (preSelectedTime) {
                const selectedSlot = document.querySelector(`[data-value="${preSelectedTime}"]`);
                if (selectedSlot) {
                    selectedSlot.classList.add('selected');
                }
            }
        }
    </script>

    <script>
        // // Initialize booking system
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

        // // Branch selection with improved layout
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

            // Hàm cập nhật danh sách kỹ thuật viên
            function updateBarbers() {
                const branch = branchInput.value || 'null';
                const date = appointmentDate.value || 'null';
                const time = appointmentTime.value || 'null';
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
                        barberCardsContainer.innerHTML = `<div class="text-center text-muted py-4">${data.error}</div>`;
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
                            
                            const avatar = barber.avatar 
                                ? `<img src="/storage/${barber.avatar}" alt="${barber.name}" class="barber-img">`
                                : `<div class="barber-img-placeholder"><i class="fas fa-user"></i></div>`;
                            
                            const ratingStars = Array.from({length: 5}, (_, i) => {
                                const starClass = i < barber.rating_avg ? 'fas fa-star text-warning' : 'far fa-star text-muted';
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
                        
                        if (data.length > 5) {
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
                        barberSelect.innerHTML = '<option value="">Không có kỹ thuật viên khả dụng</option>';
                        barberCardsContainer.innerHTML = '<div class="text-center text-muted py-4">Không có kỹ thuật viên khả dụng</div>';
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
            


            // Xử lý thêm/xóa dịch vụ bổ sung
            if (addServiceBtn && additionalServicesContainer) {
                function addAdditionalService() {
                    const serviceWrapper = document.createElement('div');
                    serviceWrapper.className = 'service-wrapper mt-2 d-flex align-items-center';

                    const serviceSelect = document.createElement('select');
                    serviceSelect.className = 'form-select additional-service-select';
                    serviceSelect.name = 'additional_services[]';
                    serviceSelect.innerHTML = `
                <option value="">Chọn dịch vụ bổ sung</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" data-name="{{ $service->name }}"
                        data-price="{{ $service->price }}" data-duration="{{ $service->duration }}">
                        {{ $service->name }} – {{ '(' . number_format($service->price) . 'đ)' }}
                    </option>
                @endforeach
            `;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-sm btn-danger remove-service ms-2';
                    removeBtn.textContent = 'Xóa';
                    removeBtn.addEventListener('click', function() {
                        additionalServicesContainer.removeChild(serviceWrapper);
                        updateAdditionalServicesInput();
                        updateBarbers();
                    });

                    serviceWrapper.appendChild(serviceSelect);
                    serviceWrapper.appendChild(removeBtn);
                    additionalServicesContainer.appendChild(serviceWrapper);
                    updateAdditionalServicesInput();
                    updateBarbers();
                }

                function updateAdditionalServicesInput() {
                    const additionalServices = Array.from(additionalServicesContainer.querySelectorAll(
                            '.additional-service-select'))
                        .map(select => select.value)
                        .filter(value => value !== '');
                    additionalServicesInput.value = JSON.stringify(additionalServices);
                }

                addServiceBtn.addEventListener('click', addAdditionalService);
                additionalServicesContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('additional-service-select')) {
                        updateAdditionalServicesInput();
                        updateBarbers();
                    }
                });
            }

            // Thêm sự kiện lắng nghe cho các trường
            appointmentDate.addEventListener('change', updateBarbers);
            branchInput.addEventListener('change', updateBarbers);
            appointmentTime.addEventListener('change', updateBarbers);
            serviceSelect.addEventListener('change', updateBarbers);
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
                onChange: function(selectedDates, dateStr) {
                    appointmentDate.value = dateStr;
                    updateBarbers();
                }
            });

            // Xử lý search overlay
            const icon = document.getElementById('search-icon');
            const overlay = document.getElementById('search-overlay');
            const closeBtn = document.querySelector('.close-btn');
            if (icon && overlay) {
                icon.addEventListener('click', e => {
                    e.preventDefault();
                    overlay.style.display = 'flex';
                });
                closeBtn?.addEventListener('click', () => overlay.style.display = 'none');
                overlay.addEventListener('click', e => {
                    if (!e.target.closest('.search-content')) overlay.style.display = 'none';
                });
                document.addEventListener('keydown', e => {
                    if (e.key === 'Escape') overlay.style.display = 'none';
                });
            }

            // Xử lý checkbox "Đặt lịch cho người khác"
            const checkbox = document.getElementById('other_person');
            const otherInfo = document.getElementById('other-info');
            if (checkbox && otherInfo) {
                checkbox.addEventListener('change', function() {
                    otherInfo.style.display = this.checked ? 'block' : 'none';
                });
            }

            // Trigger cập nhật ban đầu nếu có dữ liệu cũ
            if (serviceSelect.value || branchInput.value || appointmentDate.value) {
                updateBarbers();
            }
        });
    </script>
    <script>
        serviceSelect.addEventListener('change', function() {
            const sel = this.options[this.selectedIndex];
            // console.log('DEBUG sel.dataset =', sel.dataset);…
        });
    </script>

    <script>
        window.userLoggedIn = {{ Auth::check() ? 'true' : 'false' }};

        document.querySelector('.booking-btn').addEventListener('click', function(event) {
            event.preventDefault();
            const form = document.getElementById('bookingForm');

            // Kiểm tra form trước khi gửi
            if (!form.checkValidity()) {
                form.reportValidity(); // Hiển thị lỗi HTML5 mặc định
                return;
            }

            if (window.userLoggedIn) {
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
                        fetch('{{ route('dat-lich.store') }}', {
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
                                    // if (formData.get('payment_method') === 'vnpay') {
                                    //     // Chuyển hướng đến thanh toán VNPay
                                    //     const vnpayForm = document.createElement('form');
                                    //     vnpayForm.method = 'POST';
                                    //     vnpayForm.action = '{{ route('client.payment.vnpay') }}';
                                    //     vnpayForm.innerHTML = `
                                //     <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                //     <input type="hidden" name="appointment_id" value="${data.appointment_id}">
                                // `;
                                    //     document.body.appendChild(vnpayForm);
                                    //     vnpayForm.submit();
                                    // } else {
                                    Swal.fire({
                                        title: 'Thành công!',
                                        text: data.message,
                                        icon: 'success',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        location.reload();
                                    });
                                    // }
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
            } else {
                Swal.fire({
                    title: 'Cần đăng nhập',
                    text: 'Bạn cần đăng nhập để đặt lịch.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Đăng nhập',
                    cancelButtonText: 'Hủy',
                    customClass: {
                        popup: 'custom-swal-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route('login') }}';
                    }
                });
            }
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
