@extends('layouts.ClientLayout')

@section('title-page')
    Đặt lịch Baber House
@endsection

@section('content')
    <main class="container" style="padding: 10% 0;">
        {{-- 
        @if (session('success'))
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        @endif

        @if (session('error'))
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
            </button>
        @endif --}}
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
                            placeholder="Chọn thời điểm" style="background-color: #fff" readonly>
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
                    <button type="submit" class="submit-btn booking-btn" data-id="{{ $service->id }}">
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
        document.addEventListener('DOMContentLoaded', function() {
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

            // Xử lý click icon Google Maps
            document.querySelectorAll('.branch-icon').forEach(icon => {
                icon.addEventListener('click', function(event) {
                    event.stopPropagation(); // Ngăn sự kiện lan ra .branch-item
                    const googleMapUrl = this.getAttribute('data-value');
                    if (googleMapUrl) {
                        window.open(googleMapUrl, '_blank'); // Mở Google Maps trong tab mới
                    }
                });
            });

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
                        barberSelect.innerHTML = '<option value="">Chọn kỹ thuật viên</option>';
                        if (data.error) {
                            barberSelect.innerHTML = `<option value="">${data.error}</option>`;
                        } else if (data.length > 0) {
                            data.forEach(barber => {
                                const option = document.createElement('option');
                                option.value = barber.id;
                                option.text = barber.name;
                                barberSelect.appendChild(option);
                            });
                        } else {
                            barberSelect.innerHTML =
                                '<option value="">Không có kỹ thuật viên khả dụng</option>';
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

            // Xử lý click chi nhánh
            if (branchContainer) {
                branchContainer.querySelectorAll('.branch-item').forEach(item => {
                    item.addEventListener('click', () => {
                        branchContainer.querySelectorAll('.branch-item').forEach(el => el.classList
                            .remove('active'));
                        item.classList.add('active');
                        branchInput.value = item.getAttribute('data-id');
                        updateBarbers();
                    });
                });
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
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service');
            if (serviceSelect) {
                serviceSelect.addEventListener('change', function() {
                    const sel = this.options[this.selectedIndex];
                    // console.log('DEBUG sel.dataset =', sel.dataset);
                });
            }
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
