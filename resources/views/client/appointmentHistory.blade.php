@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử đặt lịch
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card order-history mt-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-0 m-3">
                    <h3 class="mb-0 fw-bold">Lịch sử đặt lịch của tôi</h3>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Lọc lịch sử
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="#">Đã hoàn thành</a></li>
                            <li><a class="dropdown-item" href="#">Đang chờ</a></li>
                            <li><a class="dropdown-item" href="#">Đã hủy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form class="d-flex mb-4">
                        <input type="text" class="form-control me-2" placeholder="Tìm kiếm đặt lịch">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                    </form>
                    <!-- Đặt lịch 1 -->
                    <div class="order-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Mã đặt lịch: AP12345</span>
                                <br>
                                <span class="text-dark">Dịch vụ: Cắt tóc nam</span>
                                <br>
                                <span class="text-muted">Thợ: Nguyễn Văn A</span>
                                <br>
                                <span class="text-muted">Chi nhánh: Quận 1</span>
                                <br>
                                <span class="text-muted">Thời gian: 05/31/2025 10:00 AM</span>
                                <br>
                                <span class="text-muted">Tổng tiền: $50.00</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-canceled">Đã hủy</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm"
                                        href="{{ route('client.detailAppointmentHistory') }}">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Đặt lịch 2 -->
                    <div class="order-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Mã đặt lịch: AP12346</span>
                                <br>
                                <span class="text-dark">Dịch vụ: Cắt tóc nam, Gội đầu</span>
                                <br>
                                <span class="text-muted">Thợ: Trần Thị B</span>
                                <br>
                                <span class="text-muted">Chi nhánh: Quận 2</span>
                                <br>
                                <span class="text-muted">Thời gian: 06/12/2025 2:00 PM</span>
                                <br>
                                <span class="text-muted">Tổng tiền: $75.00</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-processing">Đang chờ</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm me-1"
                                        href="{{ route('client.detailAppointmentHistory') }}">Xem chi tiết</a>
                                    <a class="btn btn-outline-danger btn-sm" href="#" title="Hủy đặt lịch">Hủy đặt
                                        lịch</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Đặt lịch 3 -->
                    <div class="order-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Mã đặt lịch: AP12347</span>
                                <br>
                                <span class="text-dark">Dịch vụ: Nhuộm tóc</span>
                                <br>
                                <span class="text-muted">Thợ: Lê Văn C</span>
                                <br>
                                <span class="text-muted">Chi nhánh: Quận 3</span>
                                <br>
                                <span class="text-muted">Thời gian: 06/20/2025 11:00 AM</span>
                                <br>
                                <span class="text-muted">Tổng tiền: $100.00</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-delivered">Đã hoàn thành</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm"
                                        href="{{ route('client.detailAppointmentHistory') }}">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <style>
        #mainNav {
            background-color: #000;
        }
    </style>
@endsection

@section('card-footer')
@endsection

@section('scripts')
@endsection
