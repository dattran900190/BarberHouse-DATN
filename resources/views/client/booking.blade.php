@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Đặt lịch Baber House
@endsection

@section('slider')
     <section class="hero-slider">
      <div class="slide active">
        <img
          src="https://4rau.vn/upload/hinhanh/cover-fb-10th-collection-0744.png"
          alt="Slide 1"
        />
      </div>
      <div class="slide">
        <img
          src="https://4rau.vn/upload/hinhanh/z4459651440290_1e4a90c27fc15cc175132ecd94872e98-2870.jpg"
          alt="Slide 2"
        />
      </div>
      <div class="slide">
        <img
          src="https://4rau.vn/upload/hinhanh/z6220937549697_8ae15d51c35246081cf6bc8d60780126-1254.jpg"
          alt="Slide 3"
        />
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
      <form id="bookingForm">
        <label for="fullName">Họ và tên:</label>
        <input type="text" id="fullName" name="fullName" required />

        <label for="phone">Số điện thoại:</label>
        <input
          type="tel"
          id="phone"
          name="phone"
          required
          pattern="[0-9]{10,11}"
        />

        <label for="branch">Chi nhánh:</label>
        <select id="branch" name="branch" required>
          <option value="">-- Chọn chi nhánh --</option>
          <option value="quan1">Quận 1 - Tân Định</option>
          <option value="quan2">Quận 2 - Thảo Điền</option>
          <option value="binhthanh">Bình Thạnh</option>
          <!-- Thêm các chi nhánh khác nếu cần -->
        </select>

        <label for="service">Dịch vụ:</label>
        <select id="service" name="service" required>
          <option value="">-- Chọn dịch vụ --</option>
          <option value="cat-toc">Cắt tóc</option>
          <option value="goi-dau">Gội đầu</option>
          <option value="cao-rau">Cạo râu</option>
          <option value="nhuom-toc">Nhuộm tóc</option>
          <!-- Thêm các dịch vụ khác nếu cần -->
        </select>

        <label for="date">Ngày hẹn:</label>
        <input type="date" id="date" name="date" required />

        <label for="time">Giờ hẹn:</label>
        <select id="time" name="time" required>
          <option value="">-- Chọn khung giờ --</option>
          <!-- Các option sẽ được sinh ra bởi JavaScript -->
        </select>

        <button type="submit">Đặt lịch</button>
      </form>
    </main>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection
