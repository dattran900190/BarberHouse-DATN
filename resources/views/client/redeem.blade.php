@extends('layouts.ClientLayout')

@section('title-page')
    Đổi điểm lấy mã giảm giá
@endsection

@section('content')
    <main class="container py-4">
        <section class="h-100">
           <div class="main-redeem">
             {{-- Thông tin người dùng --}}
             <div class="bg-white p-4 rounded shadow mb-4">
                <h3 class="mb-4">Thông tin người dùng</h3>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tên người dùng</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->email }}" disabled>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Số điểm hiện tại</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->points_balance }} điểm" disabled>
                    </div>
                </div>
            </div>

            {{-- Danh sách mã khuyến mãi --}}
            <div class="bg-white p-4 rounded shadow mb-4">
                <div class="card-body">
                    <h3 class="card-title mb-4">Danh sách Voucher</h3>

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($promotions->count())
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                            @foreach ($promotions as $promo)
                                <div class="col">
                                    <div class="card h-100 voucher-card">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title text-dark fw-bold">{{ $promo->code }}</h5>

                                            <p class="card-text mb-2">
                                                <span class="fs-6 fw-bold">Giảm giá: </span>
                                                <span class="fw-bold fs-6">
                                                    @if ($promo->discount_type === 'percent')
                                                        {{ $promo->discount_value }}%
                                                    @else
                                                        {{ number_format($promo->discount_value) }}VNĐ
                                                    @endif
                                                </span>
                                                @if ($promo->max_discount_amount)
                                                    <small class="text-muted d-block">(Max
                                                        {{ number_format($promo->max_discount_amount) }}VNĐ)</small>
                                                @endif
                                            </p>

                                            <ul class="list-unstyled small mb-4">
                                                <li><strong>Điểm yêu cầu:</strong> {{ $promo->required_points }}</li>
                                                <li><strong>Số lượng:</strong> {{ $promo->quantity }}</li>
                                                <li><strong>HSD:</strong> {{ $promo->end_date->format('d/m/Y') }}</li>
                                            </ul>

                                            <div class="mt-auto text-center">
                                                <button type="button" 
                                                        class="btn-outline-booking btn-sm redeem-btn" 
                                                        style="padding: 5px 10px;"
                                                        data-promotion-id="{{ $promo->id }}"
                                                        data-promotion-code="{{ $promo->code }}"
                                                        data-required-points="{{ $promo->required_points }}">
                                                    Đổi Voucher
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 d-flex justify-content-center">
                            {{ $promotions->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-secondary">Bạn chưa đủ điểm để đổi bất kỳ Voucher nào.</p>
                        </div>
                    @endif

                </div>
            </div>
           </div>

        </section>
    </main>

    {{-- CSS tùy chỉnh --}}
    <style>
        #mainNav {
            background-color: #000;
        }

        .voucher-card {
            border-radius: 18px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .voucher-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .main-redeem .bg-white {
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .main-redeem .bg-white:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .redeem-btn {
            transition: all 0.3s ease;
        }

        .redeem-btn:hover {
            transform: scale(1.05);
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .custom-swal-popup {
                width: 95vw !important;
                max-width: 95vw !important;
                padding: 15px;
            }

            .swal2-title {
                font-size: 18px !important;
            }

            .swal2-html-container {
                font-size: 14px !important;
            }
        }
    </style>

    {{-- SweetAlert2 CSS và JS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- JavaScript --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Xử lý sự kiện click vào nút đổi voucher
            document.querySelectorAll('.redeem-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const promotionId = this.getAttribute('data-promotion-id');
                    const promotionCode = this.getAttribute('data-promotion-code');
                    const requiredPoints = this.getAttribute('data-required-points');
                    const currentPoints = {{ Auth::user()->points_balance }};
                    
                    // Hiển thị SweetAlert2 để xác nhận
                    Swal.fire({
                        title: 'Xác nhận đổi voucher',
                        html: `
                            <p>Bạn có chắc chắn muốn đổi mã giảm giá <strong>${promotionCode}</strong> không?</p>
                            <p>Số điểm cần thiết: <strong>${requiredPoints}</strong> điểm</p>
                            <p>Số điểm hiện tại: <strong>${currentPoints}</strong> điểm</p>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        // reverseButtons: true,
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tạo form và submit
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("client.redeem.store") }}';
                            
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            
                            const promotionIdInput = document.createElement('input');
                            promotionIdInput.type = 'hidden';
                            promotionIdInput.name = 'promotion_id';
                            promotionIdInput.value = promotionId;
                            
                            form.appendChild(csrfToken);
                            form.appendChild(promotionIdInput);
                            document.body.appendChild(form);
                            
                            // Hiển thị loading
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            form.submit();
                        }
                    });
                });
            });

            // Kiểm tra nếu có thông báo thành công, hiển thị SweetAlert2 với nút OK
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: '{{ session("success") }}',
                    confirmButtonText: 'OK', // Thêm nút OK
                    customClass: {
                        popup: 'custom-swal-popup',
                        confirmButton: 'btn btn-primary' // Đồng bộ style với nút xác nhận
                    },
                    buttonsStyling: false
                });
            @endif

            // Kiểm tra nếu có thông báo lỗi, hiển thị SweetAlert2
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Có lỗi xảy ra!',
                    text: '{{ session("error") }}',
                    confirmButtonText: 'Đóng',
                    customClass: {
                        popup: 'custom-swal-popup',
                        confirmButton: 'btn btn-primary'
                    },
                    buttonsStyling: false
                });
            @endif
        });
    </script>
@endsection
