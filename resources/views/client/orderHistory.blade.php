@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử đặt hàng
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card order-history mt-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center border-0 m-3">
                    <h3 class="mb-0 fw-bold">Lịch sử đặt hàng của tôi</h3>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Lọc lịch sử
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="#">Đơn hàng đã giao</a></li>
                            <li><a class="dropdown-item" href="#">Đơn hàng đang xử lý</a></li>
                            <li><a class="dropdown-item" href="#">Đơn hàng đã hủy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <form class="d-flex mb-4">
                        <input type="text" class="form-control me-2" placeholder="Tìm kiếm đơn hàng">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                    </form>
                    <!-- Đơn hàng 1 -->
                    <div class="order-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Đơn hàng: OD649344</span>
                                <br>
                                <span class="text-dark">Sáp VIP</span>
                                <br>
                                <span class="text-muted">Tổng số lượng: 2, Giá: $323.13</span>
                                <br>
                                <span class="text-muted">Tổng tiền: $323.13</span>
                                <br>
                                <span class="text-muted">Ngày đặt: 05/31/2025</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-canceled">Đã hủy</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('client.detailOrderHistory') }}">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Đơn hàng 2 -->
                    <div class="order-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Đơn hàng: OD649344</span>
                                <br>
                                <span class="text-dark">Sáp VIP, sáp Hot</span>
                                <br>
                                <span class="text-muted">Tổng số lượng: 12</span>
                                <br>
                                <span class="text-muted">Tổng tiền: $12623.13</span>
                                <br>
                                <span class="text-muted">Ngày đặt: 06/12/2025</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-processing">Đang xử lý</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm me-1" href="{{ route('client.detailOrderHistory') }}">Xem
 tiết</a>
                                    <a class="btn btn-outline-danger btn-sm" href="#" title="Hủy đơn hàng">Hủy đơn
                                        hàng</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Đơn hàng 3 -->
                    <div class="order-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Đơn hàng: OD649344</span>
                                <br>
                                <span class="text-dark">Dầu gội kokomi</span>
                                <br>
                                <span class="text-muted">Tổng số lượng: 4</span>
                                <br>
                                <span class="text-muted">Tổng tiền: $523.13</span>
                                <br>
                                <span class="text-muted">Ngày đặt: 06/20/2025</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-delivered">Đã giao</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="d-flex justify-content-center">
                                    <a class="btn btn-outline-primary btn-sm" href="{{ route('client.detailOrderHistory') }}">Xem chi tiết</a>
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
