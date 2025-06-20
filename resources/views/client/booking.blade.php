@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Đặt lịch Baber House
@endsection

@section('slider')
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
@endsection

@section('content')
    <main class="container">
        <h2 style="text-align: center; font-family: 'Segoe UI', sans-serif">
            Đặt Lịch Cắt Tóc
        </h2>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
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
            <div class="form-group mb-3">
                <label for="voucher_id">Mã giảm giá (nếu có)</label>
                <select name="voucher_id" id="voucher_id" class="form-control">
                    <option value="">Không sử dụng mã giảm giá</option>
                    {{-- Voucher đã đổi --}}
                    @if (isset($vouchers))
                        @foreach ($vouchers as $voucher)
                            <option value="{{ $voucher->id }}">
                                {{ $voucher->promotion->code }}
                                ({{ $voucher->promotion->discount_type === 'fixed' ? number_format($voucher->promotion->discount_value) . ' VNĐ' : $voucher->promotion->discount_value . '%' }})
                            </option>
                        @endforeach
                    @endif
                    {{-- Voucher công khai --}}
                    @if (isset($publicPromotions))
                        @foreach ($publicPromotions as $promotion)
                            <option value="public_{{ $promotion->id }}">
                                {{ $promotion->code }}
                                ({{ $promotion->discount_type === 'fixed' ? number_format($promotion->discount_value) . ' VNĐ' : $promotion->discount_value . '%' }})
                                [Công khai]
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('voucher_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>

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
                <p>Thời lượng dự kiến: <strong id="totalDuration">0 Phút</strong></p>
            </div>





            <div class="form-btn mt-3">
                <button type="submit" class="submit-btn btn btn-primary">
                    Đặt lịch
                </button>
            </div>
        </form>


    </main>
    <script>
        // const checkbox = document.getElementById('other_person');
        // const otherInfo = document.getElementById('other-info');
        // checkbox.addEventListener('change', function() {
        //     otherInfo.style.display = this.checked ? 'block' : 'none';
        // });

        // document.addEventListener('DOMContentLoaded', function() {
        //     const checkbox = document.getElementById('other_person');
        //     const otherInfo = document.getElementById('other-info');

        //     checkbox.addEventListener('change', function() {
        //         otherInfo.style.display = this.checked ? 'block' : 'none';
        //     });
        // });

        // const icon = document.getElementById("search-icon");
        // const overlay = document.getElementById("search-overlay");
        // const closeBtn = document.querySelector(".close-btn");
        // if (icon && overlay) {
        //     icon.addEventListener("click", e => {
        //         e.preventDefault();
        //         overlay.style.display = "flex";
        //     });
        //     // đóng
        //     closeBtn?.addEventListener("click", () => overlay.style.display = "none");
        //     overlay.addEventListener("click", e => {
        //         if (!e.target.closest(".search-content")) overlay.style.display = "none";
        //     });
        //     document.addEventListener("keydown", e => {
        //         if (e.key === "Escape") overlay.style.display = "none";
        //     });
        // }

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
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection
