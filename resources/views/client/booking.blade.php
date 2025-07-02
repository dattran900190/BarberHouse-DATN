@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Đặt lịch Baber House
@endsection

{{-- @section('slider')
    <section class="hero-slider">
        <div class="slide active">
            <img src="https://4rau.vn/upload/hinhanh/cover-fb-10th-collection-0744.png" alt="Slide 1" />
        </div>
        <div class="slide">
            <img src="https://4rau.vn/upload/hinhanh/z4459651440290_1e4a90c27fc15cc175132ecd94872e98-2870.jpg"
                alt="Slide 2" />
        </div>
        <div class="slide">
            <img src="https://4rau.vn/upload/hinhanh/z6220937549697_8ae15d51c35246081cf6bc8d60780126-1254.jpg"
                alt="Slide 3" />
        </div>
        <!-- optional prev/next buttons -->
        <button class="prev">‹</button>
        <button class="next">›</button>
    </section>
@endsection --}}

@section('content')
    <main class="container" style="padding-top: 10%">
        <h2 style="text-align: center; font-family: 'Segoe UI', sans-serif">
            Đặt Lịch Cắt Tóc
        </h2>

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

        @if (session('mustLogin'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Bạn cần đăng nhập để đặt lịch.</strong>
                <div class="mt-2">
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Đăng nhập</a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="alert">Huỷ</button>
                </div>
            </div>
        @endif


        <form id="bookingForm" method="POST" action="{{ route('dat-lich.store') }}">
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
                <textarea name="note" id="" class="form-control" rows="4"
                    placeholder="ghi chú có thể bỏ trống ...">{{ old('note') }}</textarea>
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
        </form>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('scripts')
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
        // Xử lý nút "Cập nhật"
        document.querySelector('.booking-btn').addEventListener('click', function(event) {
            event.preventDefault(); // Ngăn hành vi mặc định của form
            const form = document.getElementById('bookingForm');

            // Cửa sổ xác nhận
            Swal.fire({
                title: 'Xác nhận đặt lịch',
                text: 'Bạn có chắc chắn muốn đặt lịch hẹn này?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Đặt lịch',
                cancelButtonText: 'Hủy',
                customClass: {
                    popup: 'custom-swal-popup' // CSS
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cửa sổ loading
                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ trong giây lát.',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'custom-swal-popup' // CSS
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
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            Swal.close(); // Đóng cửa sổ loading

                            if (data.success) {
                                // Cửa sổ thành công
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: data.message,
                                    icon: 'success',
                                    customClass: {
                                        popup: 'custom-swal-popup' // CSS
                                    },
                                }).then(() => {
                                    // Chuyển hướng sau khi đặt lịch thành công
                                    window.location.href = '{{ route('appointments.index') }}';
                                });
                            } else {
                                // Cửa sổ lỗi
                                Swal.fire({
                                    title: 'Lỗi!',
                                    text: data.message,
                                    icon: 'error',
                                    customClass: {
                                        popup: 'custom-swal-popup' // CSS
                                    },
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close(); // Đóng cửa sổ loading
                            console.error('Lỗi AJAX:', error);
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Đã có lỗi xảy ra: ' + error.message,
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-swal-popup' // CSS
                                },
                            });
                        });
                }
            });
        });
    </script>
@endsection
