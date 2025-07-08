@extends('layouts.ClientLayout')

@section('title-page')
    Đặt lịch Baber House
@endsection


@section('content')
    <main class="container" style="padding: 10% 0;">
        {{-- <h2 style="text-align: center; font-family: 'Segoe UI', sans-serif">
            Đặt Lịch Cắt Tóc
        </h2> --}}

        @if (session('success'))
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        @endif

        @if (session('error'))
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        @endif


        {{-- <form id="bookingForm" method="POST" action="{{ route('dat-lich.store') }}">
            @csrf

            <div class="row align-items-center">
                <div class="col-sm-1">
                    <div class="form-group mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="other_person" name="other_person"
                            {{ old('other_person') ? 'checked' : '' }}>
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
                            <span class="form-label">Họ và tên</span>
                            <input id="name" name="name" class="form-control" type="text"
                                placeholder="Nhập họ và tên" value="{{ old('name') }}">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <span class="form-label">Số điện thoại</span>
                            <input id="phone" name="phone" class="form-control" type="tel"
                                placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-group mb-3">
                    <span class="form-label">Email</span>
                    <input id="email" name="email" value="{{ old('email') }}"class="form-control"
                        placeholder="Nhập email" type="text">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <span class="form-label">Ngày hẹn</span>
                        <input id="appointment_date" name="appointment_date"
                            value="{{ old('appointment_date') }}"class="form-control" type="date">
                        @error('appointment_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <span class="form-label">Giờ hẹn</span>
                        <input id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}"
                            class="form-control" type="time">
                        @error('appointment_time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>


            <div class="form-group mb-3">
                <span class="form-label">Chi nhánh</span>
                <select id="branch" name="branch_id" class="form-control">
                    <option value="">-- Chọn chi nhánh --</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}</option>
                    @endforeach
                </select>
                @error('branch_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <span class="form-label">Dịch vụ</span>
                <select id="service" name="service_id" class="form-control">
                    <option value="">-- Chọn dịch vụ --</option>
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

            <div class="form-group mb-3">
                <span class="form-label">Thợ</span>
                <select id="barber" name="barber_id" class="form-control">
                    <option value="">-- Chọn thợ --</option>
                    @foreach ($barbers as $barber)
                        <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                            {{ $barber->name }}</option>
                    @endforeach
                </select>
                @error('barber_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <input type="hidden" id="service_price" value="{{ $service->price ?? 0 }}">
            <select name="voucher_id" id="voucher_id" class="form-control">
                <option value="">Không sử dụng mã giảm giá</option>
                @foreach ($vouchers as $voucher)
                    <option value="{{ $voucher->id }}" data-discount-type="{{ $voucher->promotion->discount_type }}"
                        data-discount-value="{{ $voucher->promotion->discount_value }}">
                        {{ $voucher->promotion->code }}
                        ({{ $voucher->promotion->discount_type === 'fixed' ? number_format($voucher->promotion->discount_value) . ' VNĐ' : $voucher->promotion->discount_value . '%' }})
                    </option>
                @endforeach
                @foreach ($publicPromotions as $promotion)
                    <option value="public_{{ $promotion->id }}" data-discount-type="{{ $promotion->discount_type }}"
                        data-discount-value="{{ $promotion->discount_value }}">
                        {{ $promotion->code }}
                        ({{ $promotion->discount_type === 'fixed' ? number_format($promotion->discount_value) . ' VNĐ' : $promotion->discount_value . '%' }})
                    </option>
                @endforeach
            </select> 
            <p>Tổng tiền: <strong id="totalPrice">{{ number_format($service->price ?? 0) }} vnđ</strong></p>


            <div class="form-group mb-3">
                <span class="form-label">Ghi chú</span>
                <textarea name="note" id="" class="form-control" rows="4" placeholder="ghi chú có thể bỏ trống ...">{{ old('note') }}</textarea>
                @error('note')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mb-3">
                <p>Tổng tiền: <strong id="totalPrice">0 vnđ</strong></p>
                <span id="total_after_discount"></span>
                <p>Thời lượng dự kiến: <strong id="totalDuration">0 Phút</strong></p>
            </div>

            <div class="form-btn mt-3">
                <button type="submit" class="submit-btn btn btn-primary booking-btn" data-id="{{ $service->id }}">
                    Đặt lịch
                </button>
            </div>
        </form> --}}
        <div class="booking-container">
            <!-- Header -->
            <div class="booking-header">
                <h1 class="booking-title">Đặt lịch cắt tóc</h1>
                <p class="booking-subtitle">Giờ mở cửa: 08:00 - 19:30</p>
                <p class="booking-note">
                    <i class="fas fa-exclamation-circle"></i>
                    Vui lòng nhập thông tin bắt buộc
                </p>
            </div>
            {{-- 
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
                                <span class="form-label">Họ và tên</span>
                                <input id="name" name="name" class="form-control" type="text"
                                    placeholder="Nhập họ và tên" value="{{ old('name') }}">
                                @error('name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group mb-3">
                                <span class="form-label">Số điện thoại</span>
                                <input id="phone" name="phone" class="form-control" type="tel"
                                    placeholder="Nhập số điện thoại" value="{{ old('phone') }}">
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <span class="form-label">Email</span>
                        <input id="email" name="email" value="{{ old('email') }}"class="form-control"
                            placeholder="Nhập email" type="text">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                 <div class="form-group">
                <input type="tel" class="form-control" placeholder="Số điện thoại" id="phoneNumber">
                </div>

                 <div class="form-group">
                    <input type="text" class="form-control" placeholder="Họ và tên" id="fullName">
                </div>

                <div class="form-group">
                <input type="tel" class="form-control" placeholder="Email" id="phoneNumber">
                </div>


                <div class="section-title">Thông tin dịch vụ</div>

                <div class="form-group">
                    <label class="form-label">Ngày đặt lịch <span class="required">*</span></label>
                    <div class="date-input">
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                            value="{{ old('appointment_date') }}" placeholder="Chọn thời điểm">
                    </div>
                    @error('appointment_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <span class="form-label">Ngày hẹn</span>
                            <input id="appointment_date" name="appointment_date"
                                value="{{ old('appointment_date') }}"class="form-control" type="date">
                            @error('appointment_date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <span class="form-label">Giờ hẹn</span>
                            <input id="appointment_time" name="appointment_time" value="{{ old('appointment_time') }}"
                                class="form-control" type="time">
                            @error('appointment_time')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <label class="form-label">Chọn chi nhánh <span class="required">*</span></label>
                <div class="branch-list">
                    @foreach ($branches as $branch)
                    <div class="branch-item" data-branch="quan1-yersin">
                            <div class="branch-content">
                                <i class="fas fa-map-marker-alt branch-icon"></i>
                                <span  value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}</span>
                            </div>
                            <div class="branch-radio"></div>
                        </div>
                        @endforeach
                    @error('branch_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Yêu cầu kĩ thuật viên <span class="required">*</span></label>
                    <div class="position-relative">
                        <select class="form-select" id="technician" name="barber_id">
                            <option value="">Chọn kĩ thuật viên</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}"
                                    {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}</option>
                            @endforeach
                        </select>
                        @error('barber_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dịch vụ <span class="required">*</span></label>
                    <div id="servicesList">
                        <div class="service-item" data-service-index="0">
                            <div class="position-relative">
                                <select id="service" name="service_id" class="form-select service-select"
                                    data-index="0">
                                    <option value="">Chọn dịch vụ</option>
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
                    <button class="add-service-btn" type="button" id="addServiceBtn">Thêm dịch vụ</button>
                </div>

                <div class="service-info">
                    <div>Tổng tiền: <strong id="totalPrice">0 vnđ</strong></div>
                    <span id="total_after_discount"></span>
                    <div>Thời lượng dự kiến: <strong id="totalDuration">0 Phút</strong></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Chọn khung giờ dịch vụ <span class="required">*</span></label>
                    <div class="time-grid" id="timeGrid" name="appointment_time" value="{{ old('appointment_time') }}">
                        <button class="time-slot" name="appointment_time" data-time="10:00">08:00</button>
                        <button class="time-slot" name="appointment_time" data-time="10:00">08:30</button>
                        <button class="time-slot" name="appointment_time" data-time="10:00">09:00</button>
                        <button class="time-slot" name="appointment_time" data-time="10:00">09:30</button>
                        <button class="time-slot" name="appointment_time" data-time="10:00">10:00</button>
                        <button class="time-slot" name="appointment_time" data-time="10:30">10:30</button>
                        <button class="time-slot" name="appointment_time" data-time="11:00">11:00</button>
                        <button class="time-slot" name="appointment_time" data-time="11:30">11:30</button>
                        <button class="time-slot" name="appointment_time" data-time="12:00">12:00</button>
                        <button class="time-slot" name="appointment_time" data-time="12:30">12:30</button>
                        <button class="time-slot" name="appointment_time" data-time="13:00">13:00</button>
                        <button class="time-slot" name="appointment_time" data-time="13:30">13:30</button>
                        <button class="time-slot" name="appointment_time" data-time="14:00">14:00</button>
                        <button class="time-slot" name="appointment_time" data-time="14:30">14:30</button>
                        <button class="time-slot" name="appointment_time" data-time="15:00">15:00</button>
                        <button class="time-slot" name="appointment_time" data-time="15:30">15:30</button>
                        <button class="time-slot" name="appointment_time" data-time="16:00">16:00</button>
                        <button class="time-slot" name="appointment_time" data-time="16:30">16:30</button>
                        <button class="time-slot" name="appointment_time" data-time="17:00">17:00</button>
                        <button class="time-slot" name="appointment_time" data-time="17:30">17:30</button>
                        <button class="time-slot" name="appointment_time" data-time="18:00">18:00</button>
                        <button class="time-slot" name="appointment_time" data-time="18:30">18:30</button>
                        <button class="time-slot" name="appointment_time" data-time="19:00">19:00</button>
                        <button class="time-slot" name="appointment_time" data-time="19:30">19:30</button>
                    </div>
                     @error('appointment_time')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                </div>
                

                <div class="form-group">
                    <label class="form-label">Khuyến mãi</label>
                    <div class="position-relative">
                        <select class="form-select" name="voucher_id" id="voucher_id">
                            <option value="">Không sử dụng mã giảm giá</option>
                            @foreach ($vouchers as $voucher)
                                <option value="{{ $voucher->id }}"
                                    data-discount-type="{{ $voucher->promotion->discount_type }}"
                                    data-discount-value="{{ $voucher->promotion->discount_value }}">
                                    {{ $voucher->promotion->code }}
                                    ({{ $voucher->promotion->discount_type === 'fixed' ? number_format($voucher->promotion->discount_value) . ' VNĐ' : $voucher->promotion->discount_value . '%' }})
                                </option>
                            @endforeach
                            @foreach ($publicPromotions as $promotion)
                                <option value="public_{{ $promotion->id }}"
                                    data-discount-type="{{ $promotion->discount_type }}"
                                    data-discount-value="{{ $promotion->discount_value }}">
                                    {{ $promotion->code }}
                                    ({{ $promotion->discount_type === 'fixed' ? number_format($promotion->discount_value) . ' VNĐ' : $promotion->discount_value . '%' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <textarea class="form-control notes-textarea" name="note" placeholder="ghi chú có thể bỏ trống ..." id="notes">{{ old('note') }}</textarea>
                </div> 

                <div class="form-group mb-3">
                    <span class="form-label">Dịch vụ</span>
                    <select id="service" name="service_id" class="form-control">
                        <option value="">-- Chọn dịch vụ --</option>
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

                <div class="form-group mb-3">
                    <span class="form-label">Thợ</span>
                    <select id="barber" name="barber_id" class="form-control">
                        <option value="">-- Chọn thợ --</option>
                        @foreach ($barbers as $barber)
                            <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                {{ $barber->name }}</option>
                        @endforeach
                    </select>
                    @error('barber_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <input type="hidden" id="service_price" value="{{ $service->price ?? 0 }}">
                <select name="voucher_id" id="voucher_id" class="form-control">
                    <option value="">Không sử dụng mã giảm giá</option>
                    @foreach ($vouchers as $voucher)
                        <option value="{{ $voucher->id }}" data-discount-type="{{ $voucher->promotion->discount_type }}"
                            data-discount-value="{{ $voucher->promotion->discount_value }}">
                            {{ $voucher->promotion->code }}
                            ({{ $voucher->promotion->discount_type === 'fixed' ? number_format($voucher->promotion->discount_value) . ' VNĐ' : $voucher->promotion->discount_value . '%' }})
                        </option>
                    @endforeach
                    @foreach ($publicPromotions as $promotion)
                        <option value="public_{{ $promotion->id }}" data-discount-type="{{ $promotion->discount_type }}"
                            data-discount-value="{{ $promotion->discount_value }}">
                            {{ $promotion->code }}
                            ({{ $promotion->discount_type === 'fixed' ? number_format($promotion->discount_value) . ' VNĐ' : $promotion->discount_value . '%' }})
                        </option>
                    @endforeach
                </select>
                <p>Tổng tiền: <strong id="totalPrice">{{ number_format($service->price ?? 0) }} vnđ</strong></p>


                <div class="form-group mb-3">
                    <span class="form-label">Ghi chú</span>
                    <textarea name="note" id="" class="form-control" rows="4" placeholder="ghi chú có thể bỏ trống ...">{{ old('note') }}</textarea>
                    @error('note')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <p>Tổng tiền: <strong id="totalPrice">0 vnđ</strong></p>
                    <span id="total_after_discount"></span>
                    <p>Thời lượng dự kiến: <strong id="totalDuration">0 Phút</strong></p>
                </div>

                <button type="submit" class="book-btn booking-btn" id="bookButton" data-id="{{ $service->id }}">
                    <i class="fas fa-calendar-alt"></i>
                    Đặt lịch
                </button>
            </form> --}}

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

                {{-- <div class="form-group">
                    <label class="form-label">Ngày đặt lịch <span class="required">*</span></label>
                    <div class="date-input">
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                            value="{{ old('appointment_date') }}" placeholder="Chọn thời điểm">
                    </div>
                    @error('appointment_date')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div> --}}
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
                    <div id="branch" name="branch_id" class="branch-list">
                        <input type="hidden" id="branch_input" name="branch_id" value="{{ old('branch_id') }}">
                        @foreach ($branches as $branch)
                            <div class="branch-item {{ old('branch_id') == $branch->id ? 'active' : '' }}"
                                data-id="{{ $branch->id }}">
                                <i class="fas fa-map-marker-alt branch-icon"
                                    data-value="{{ $branch->google_map_url }}"></i>
                                <span class="branch-name">{{ $branch->name }}</span>
                                <div class="branch-radio"></div>
                            </div>
                        @endforeach
                    </div>
                    @error('branch_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
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
                                    <option value="">Chọn dịch vụ</option>
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
                    {{-- <button class="add-service-btn" type="button" id="addServiceBtn">Thêm dịch vụ</button> --}}
                </div>

                <div class="form-group">
                    <label class="form-label">Yêu cầu kĩ thuật viên <span class="required">*</span></label>
                    <div class="position-relative">
                        <select class="form-select" id="barber" name="barber_id">
                            <option value="">Chọn kĩ thuật viên</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}"
                                    {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}</option>
                            @endforeach
                        </select>
                        @error('barber_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Khuyến mãi</label>
                    <input type="hidden" id="service_price" value="{{ $service->price ?? 0 }}">
                    <select name="voucher_id" id="voucher_id" class="form-control">
                        <option value="">Không sử dụng mã giảm giá</option>
                        @foreach ($vouchers as $voucher)
                            <option value="{{ $voucher->id }}"
                                data-discount-type="{{ $voucher->promotion->discount_type }}"
                                data-discount-value="{{ $voucher->promotion->discount_value }}">
                                {{ $voucher->promotion->code }}
                                ({{ $voucher->promotion->discount_type === 'fixed' ? number_format($voucher->promotion->discount_value) . ' VNĐ' : $voucher->promotion->discount_value . '%' }})
                            </option>
                        @endforeach
                        @foreach ($publicPromotions as $promotion)
                            <option value="public_{{ $promotion->id }}"
                                data-discount-type="{{ $promotion->discount_type }}"
                                data-discount-value="{{ $promotion->discount_value }}">
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
                    <label class="form-label">Phương thức thanh toán <span class="required">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="">-- Chọn phương thức thanh toán --</option>
                        <option value="cash">Tiền mặt</option>
                        <option value="vnpay">VNPay</option>
                    </select>
                    @error('payment_method')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-btn mt-3">
                    <button type="submit" class="submit-btn book-btn booking-btn" data-id="{{ $service->id }}">
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
    </style>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
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

        // // Enhanced service management
        // function setupServiceManagement() {
        //     // Add service button
        //     document.getElementById('addServiceBtn').addEventListener('click', addNewService);

        //     // Setup initial service
        //     setupServiceSelect(0);
        // }

        // function addNewService() {
        //     serviceCounter++;
        //     const servicesList = document.getElementById('servicesList');

        //     const newServiceItem = document.createElement('div');
        //     newServiceItem.className = 'service-item';
        //     newServiceItem.setAttribute('data-service-index', serviceCounter);

        //     newServiceItem.innerHTML = `
    //         <div class="position-relative">
    //             <select class="form-select service-select" data-index="${serviceCounter}">
    //                 <option value="">Chọn dịch vụ</option>
    //                 <option value="haircut" data-price="150000" data-duration="30">Cắt tóc - 150,000 vnđ (30 phút)</option>
    //                 <option value="wash" data-price="50000" data-duration="15">Gội đầu - 50,000 vnđ (15 phút)</option>
    //                 <option value="styling" data-price="100000" data-duration="45">Tạo kiểu - 100,000 vnđ (45 phút)</option>
    //                 <option value="coloring" data-price="300000" data-duration="120">Nhuộm tóc - 300,000 vnđ (120 phút)</option>
    //                 <option value="treatment" data-price="200000" data-duration="60">Ủ tóc - 200,000 vnđ (60 phút)</option>
    //             </select>
    //             <button class="remove-service" onclick="removeService(${serviceCounter})">×</button>
    //         </div>
    //     `;

        //     servicesList.appendChild(newServiceItem);
        //     setupServiceSelect(serviceCounter);
        // }

        // function removeService(index) {
        //     const serviceItem = document.querySelector(`[data-service-index="${index}"]`);
        //     if (serviceItem) {
        //         serviceItem.remove();
        //         // Remove from services array
        //         services = services.filter(service => service.index !== index);
        //         updateServiceSummary();
        //         validateForm();
        //     }
        // }

        // function setupServiceSelect(index) {
        //     const select = document.querySelector(`[data-index="${index}"]`);
        //     if (select) {
        //         select.addEventListener('change', function() {
        //             const selectedOption = this.options[this.selectedIndex];

        //             if (this.value) {
        //                 const serviceData = {
        //                     index: index,
        //                     name: selectedOption.text.split(' - ')[0],
        //                     value: this.value,
        //                     price: parseInt(selectedOption.dataset.price),
        //                     duration: parseInt(selectedOption.dataset.duration)
        //                 };

        //                 // Update or add service
        //                 const existingIndex = services.findIndex(s => s.index === index);
        //                 if (existingIndex >= 0) {
        //                     services[existingIndex] = serviceData;
        //                 } else {
        //                     services.push(serviceData);
        //                 }
        //             } else {
        //                 // Remove service if deselected
        //                 services = services.filter(s => s.index !== index);
        //             }

        //             updateServiceSummary();
        //             validateForm();
        //         });
        //     }
        // }

        // function updateServiceSummary() {
        //     const totalPrice = services.reduce((sum, service) => sum + service.price, 0);
        //     const totalDuration = services.reduce((sum, service) => sum + service.duration, 0);

        //     // Update or create service summary
        //     let summaryElement = document.querySelector('.service-summary');
        //     if (!summaryElement) {
        //         summaryElement = document.createElement('div');
        //         summaryElement.className = 'service-summary';
        //         document.querySelector('.service-info').replaceWith(summaryElement);
        //     }

        //     let summaryHTML =
        //         '<div class="service-summary-title" style="font-weight: bold; margin-bottom: 10px;">Dịch vụ đã chọn:</div>';

        //     services.forEach(service => {
        //         summaryHTML += `
    //             <div class="service-summary-item">
    //                 <span>${service.name}</span>
    //                 <span>${service.price.toLocaleString('vi-VN')} vnđ</span>
    //             </div>
    //         `;
        //     });

        //     summaryHTML += `
    //         <div class="service-summary-total">
    //             <div class="service-summary-item">
    //                 <span>Tổng tiền:</span>
    //                 <span>${totalPrice.toLocaleString('vi-VN')} vnđ</span>
    //             </div>
    //             <div class="service-summary-item">
    //                 <span>Thời lượng dự kiến:</span>
    //                 <span>${totalDuration} phút</span>
    //             </div>
    //         </div>
    //     `;

        //     summaryElement.innerHTML = summaryHTML;
        // }

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

        // // Time slot selection
        // function setupTimeSelection() {
        //     const timeSlots = document.querySelectorAll('.time-slot');
        //     timeSlots.forEach(slot => {
        //         slot.addEventListener('click', function() {
        //             // Remove previous selection
        //             timeSlots.forEach(time => time.classList.remove('selected'));

        //             // Select current time
        //             this.classList.add('selected');
        //             selectedTime = this.dataset.time;

        //             validateForm();
        //         });
        //     });
        // }

        // // Barber checkbox
        // function setupBarberCheckbox() {
        //     const checkbox = document.getElementById('barberCheckbox');
        //     const checkIcon = checkbox.querySelector('i');

        //     checkbox.addEventListener('click', function() {
        //         barberSilent = !barberSilent;

        //         if (barberSilent) {
        //             this.classList.add('checked');
        //             checkIcon.style.display = 'block';
        //         } else {
        //             this.classList.remove('checked');
        //             checkIcon.style.display = 'none';
        //         }
        //     });
        // }

        // // Set minimum date to today
        // function setMinDate() {
        //     const today = new Date().toISOString().split('T')[0];
        //     document.getElementById('appointment_date').min = today;
        // }

        // // Enhanced form validation
        // function setupFormValidation() {
        //     const requiredFields = ['phoneNumber', 'fullName', 'technician', 'appointment_date'];

        //     requiredFields.forEach(fieldId => {
        //         const field = document.getElementById(fieldId);
        //         if (field) {
        //             field.addEventListener('input', validateForm);
        //             field.addEventListener('change', validateForm);
        //         }
        //     });
        // }

        // function validateForm() {
        //     const phoneNumber = document.getElementById('phoneNumber').value;
        //     const fullName = document.getElementById('fullName').value;
        //     const technician = document.getElementById('technician').value;
        //     const appointment_date = document.getElementById('appointment_date').value;

        //     const isValid = phoneNumber && fullName && technician &&
        //         appointment_date && selectedBranch && selectedTime &&
        //         services.length > 0;

        //     document.getElementById('bookButton').disabled = !isValid;
        // }

        // // Book appointment
        // document.getElementById('bookButton').addEventListener('click', function() {
        //     const bookingData = {
        //         phoneNumber: document.getElementById('phoneNumber').value,
        //         fullName: document.getElementById('fullName').value,
        //         guestCount: document.getElementById('guestCount').value,
        //         branch: selectedBranch,
        //         technician: document.getElementById('technician').value,
        //         services: services,
        //         date: document.getElementById('appointment_date').value,
        //         time: selectedTime,
        //         barberSilent: barberSilent,
        //         voucher_id: document.getElementById('voucher_id').value,
        //         notes: document.getElementById('notes').value,
        //         totalPrice: services.reduce((sum, service) => sum + service.price, 0),
        //         totalDuration: services.reduce((sum, service) => sum + service.duration, 0)
        //     };

        //     // Simulate booking
        //     alert(
        //         `Đặt lịch thành công!\n\nTổng tiền: ${bookingData.totalPrice.toLocaleString('vi-VN')} vnđ\nThời gian: ${bookingData.totalDuration} phút\n\nChúng tôi sẽ liên hệ với bạn sớm nhất.`
        //     );
        //     console.log('Booking data:', bookingData);
        // });
    </script>
    <script>
        // $('#service').select2({
        //     width: '100%',
        //     templateResult: function(data) {
        //         if (!data.id) return data.text;
        //         let name = $(data.element).data('name');
        //         let price = $(data.element).data('price');
        //         return $(`<div style="display: flex; justify-content: space-between;">
    //             <span>${name}</span>
    //             <span>${price}</span>
    //         </div>`);
        //     },
        //     templateSelection: function(data) {
        //         return data.text;
        //     }
        // });
        serviceSelect.addEventListener('change', function() {
            const sel = this.options[this.selectedIndex];
            console.log('DEBUG sel.dataset =', sel.dataset);…
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
<<<<<<< HEAD
=======

>>>>>>> 51ec286452ffce7e0a408e1292eb65f0e7032359
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
@endsection
