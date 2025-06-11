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

        {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form id="bookingForm" method="POST" action="{{ route('dat-lich.store') }}">
            @csrf


            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <span class="form-label">Họ và tên</span>
                        <input id="name" name="name" class="form-control" type="text"
                            placeholder="Nhập họ và tên">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <span class="form-label">Số điện thoại</span>
                        <input id="phone" name="phone" class="form-control" type="tel" pattern="[0-9]{10,11}"
                            placeholder="Nhập số điện thoại">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <span class="form-label">Ngày hẹn</span>
                        <input id="appointment_date" name="appointment_date" class="form-control" type="date">
                        @error('appointment_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group mb-3">
                        <span class="form-label">Giờ hẹn</span>
                        <input id="appointment_time" name="appointment_time" class="form-control" type="time">
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
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
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
                        <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                    @endforeach
                </select>
                @error('barber_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-btn mt-3">
                <button type="submit" class="submit-btn btn btn-primary">
                    Đặt lịch
                </button>
            </div>
        </form>


    </main>
    <script>
        const icon = document.getElementById("search-icon");
        const overlay = document.getElementById("search-overlay");
        const closeBtn = document.querySelector(".close-btn");
        if (icon && overlay) {
            icon.addEventListener("click", e => {
                e.preventDefault();
                overlay.style.display = "flex";
            });
            // đóng
            closeBtn?.addEventListener("click", () => overlay.style.display = "none");
            overlay.addEventListener("click", e => {
                if (!e.target.closest(".search-content")) overlay.style.display = "none";
            });
            document.addEventListener("keydown", e => {
                if (e.key === "Escape") overlay.style.display = "none";
            });
        }
    </script>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection
