@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Giỏ hàng Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-custom">
            <div class="padding-5vh">
                <div class="flex-center">
                    <div class="col-12">
                        <div class="card-registration">
                            <div class="card-body no-padding">
                                <div class="flex-row no-gap">
                                    <div class="col-left-66">
                                        <div class="p-5vh">
                                            <div class="d-flex justify-content-between align-items-center mb-5">
                                                <h1 class="fw-bold mb-0">Giỏ hàng</h1>
                                                <h6 class="mb-0 text-muted">3 sản phẩm</h6>
                                            </div>
                                            <hr class="my-4">

                                            <div class="row mb-4 d-flex justify-content-between align-items-center">
                                                <div class="col-md-2 col-lg-2 col-xl-2">
                                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-shopping-carts/img5.webp"
                                                        class="img-fluid rounded-3" alt="Cotton T-shirt">
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-3">
                                                    <h6 class="text-muted">Shirt</h6>
                                                    <h6 class="mb-0">Cotton T-shirt</h6>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                                    <div class="quantity d-flex align-items-center" id="quantity-control">
                                                        <button type="button"
                                                            class="btn btn-outline-dark btn-sm quantity-minus"
                                                            id="quantity-minus">−</button>
                                                        <input type="number"
                                                            class="form-control form-control-sm mx-2 quantity-input"
                                                            id="quantity-input" value="1" min="1" />
                                                        <button type="button"
                                                            class="btn btn-outline-dark btn-sm quantity-plus"
                                                            id="quantity-plus">+</button>
                                                    </div>

                                                </div>
                                                <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                                    <h6 class="mb-0">€ 44.00</h6>
                                                </div>
                                                <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                                    <a href="#!" class="text-muted"><i class="fas fa-times"></i></a>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <div class="row mb-4 d-flex justify-content-between align-items-center">
                                                <div class="col-md-2 col-lg-2 col-xl-2">
                                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-shopping-carts/img6.webp"
                                                        class="img-fluid rounded-3" alt="Cotton T-shirt">
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-3">
                                                    <h6 class="text-muted">Shirt</h6>
                                                    <h6 class="mb-0">Cotton T-shirt</h6>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                                    <div class="quantity d-flex align-items-center" id="quantity-control">
                                                        <button type="button"
                                                            class="btn btn-outline-dark btn-sm quantity-minus"
                                                            id="quantity-minus">−</button>
                                                        <input type="number"
                                                            class="form-control form-control-sm mx-2 quantity-input"
                                                            id="quantity-input" value="1" min="1" />
                                                        <button type="button"
                                                            class="btn btn-outline-dark btn-sm quantity-plus"
                                                            id="quantity-plus">+</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                                    <h6 class="mb-0">€ 44.00</h6>
                                                </div>
                                                <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                                    <a href="#!" class="text-muted"><i class="fas fa-times"></i></a>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <div class="row mb-4 d-flex justify-content-between align-items-center">
                                                <div class="col-md-2 col-lg-2 col-xl-2">
                                                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-shopping-carts/img7.webp"
                                                        class="img-fluid rounded-3" alt="Cotton T-shirt">
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-3">
                                                    <h6 class="text-muted">Shirt</h6>
                                                    <h6 class="mb-0">Cotton T-shirt</h6>
                                                </div>
                                                <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                                    <div class="quantity d-flex align-items-center" id="quantity-control">
                                                        <button type="button"
                                                            class="btn btn-outline-dark btn-sm quantity-minus"
                                                            id="quantity-minus">−</button>
                                                        <input type="number"
                                                            class="form-control form-control-sm mx-2 quantity-input"
                                                            id="quantity-input" value="1" min="1" />
                                                        <button type="button"
                                                            class="btn btn-outline-dark btn-sm quantity-plus"
                                                            id="quantity-plus">+</button>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                                    <h6 class="mb-0">€ 44.00</h6>
                                                </div>
                                                <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                                    <a href="#!" class="text-muted"><i class="fas fa-times"></i></a>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <div class="pt-5">
                                                <h6 class="mb-0"><a href="#!" class="text-body"><i
                                                            class="fas fa-long-arrow-alt-left me-2"></i>Quay lại cửa
                                                        hàng</a></h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 bg-body-tertiary">
                                        <div class="p-5">
                                            <h3 class="fw-bold mb-5 mt-2 pt-1">Tổng</h3>
                                            <hr class="my-4">

                                            <div class="d-flex justify-content-between mb-4">
                                                <h5 class="text-uppercase">sản phẩm 3</h5>
                                                <h5>€ 132.00</h5>
                                            </div>

                                            <h5 class="text-uppercase mb-3">Phí ship</h5>

                                            <div class="mb-4 pb-2">
                                                <select data-mdb-select-init>
                                                    <option value="1">Standard-Delivery- €5.00</option>
                                                    <option value="2">Two</option>
                                                    <option value="3">Three</option>
                                                    <option value="4">Four</option>
                                                </select>
                                            </div>

                                            <h5 class="text-uppercase mb-3">mã khuyến mại</h5>

                                            <div class="mb-5">
                                                <div data-mdb-input-init class="form-outline">
                                                    <input type="text" id="form3Examplea2"
                                                        class="form-control form-control-lg" />
                                                    <label class="form-label" for="form3Examplea2">Enter your code</label>
                                                </div>
                                            </div>

                                            <hr class="my-4">

                                            <div class="d-flex justify-content-between mb-5">
                                                <h5 class="text-uppercase">Tổng tiền</h5>
                                                <h5>€ 137.00</h5>
                                            </div>

                                            <a href="{{ asset('thanh-toan') }}">
                                                <button type="button" data-mdb-button-init data-mdb-ripple-init
                                                class="btn btn-dark btn-block btn-lg" data-mdb-ripple-color="dark">
                                                Xác nhận
                                            </button>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy các phần tử bằng ID
        const minusBtn = document.getElementById('quantity-minus');
        const plusBtn = document.getElementById('quantity-plus');
        const input = document.getElementById('quantity-input');

        // Xử lý sự kiện nút trừ
        minusBtn.addEventListener('click', function() {
            let value = parseInt(input.value);
            if (value > 1) { // Đảm bảo giá trị không nhỏ hơn 1
                input.value = value - 1;
            }
        });

        // Xử lý sự kiện nút cộng
        plusBtn.addEventListener('click', function() {
            let value = parseInt(input.value);
            input.value = value + 1;
        });

        // Xử lý khi người dùng nhập trực tiếp vào input
        input.addEventListener('change', function() {
            let value = parseInt(input.value);
            if (isNaN(value) || value < 1) { // Đảm bảo giá trị hợp lệ
                input.value = 1;
            }
        });
    });

    const nav = document.getElementById("mainNav");

    window.addEventListener("scroll", () => {
        if (window.scrollY = 100) {
            nav.classList.add("scrolled");
        } else {
            nav.classList.remove("scrolled");
        }
    });
</script>
