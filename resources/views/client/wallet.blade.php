@extends('layouts.ClientLayout')

@section('title-page')
    Ví tài khoản
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card wallet-page mt-4 shadow-sm">
                <div class="card-header border-0">
                    <h3 class="mb-0 fw-bold">Ví của tôi</h3>
                    <div class="d-flex justify-content-between">
                        <span class="mt-2">Số dư hiện tại: 2.500.000 VNĐ</span>
                        <a href="{{ route('client.withdrawal') }}" class="btn btn-primary m-0">Rút tiền</a>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="fw-bold mb-3">Yêu cầu hoàn tiền</h4>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <form class="d-flex w-50">
                            <input type="text" class="form-control me-2" placeholder="Tìm kiếm theo mã đặt lịch">
                            <button type="submit" class="btn btn-primary">Tìm</button>
                        </form>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Lọc theo trạng thái
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item" href="#">Đang chờ</a></li>
                                <li><a class="dropdown-item" href="#">Đã duyệt</a></li>
                                <li><a class="dropdown-item" href="#">Bị từ chối</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- Yêu cầu hoàn tiền 1 -->
                    <div class="refund-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Mã đặt lịch: AP12345</span><br>
                                <span class="text-muted">Lý do: Không thể tham gia</span><br>
                                <span class="text-muted">Số tiền hoàn: 1.250.000 VNĐ</span><br>
                                <span class="text-muted">Ngày yêu cầu: 22/06/2025</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-processing">Đang chờ</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('client.detailWallet') }}">Xem chi
                                    tiết</a>
                            </div>
                        </div>
                    </div>
                    <!-- Yêu cầu hoàn tiền 2 -->
                    <div class="refund-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Mã đặt lịch: AP12346</span><br>
                                <span class="text-muted">Lý do: Dịch vụ bị hủy</span><br>
                                <span class="text-muted">Số tiền hoàn: 1.875.000 VNĐ</span><br>
                                <span class="text-muted">Ngày yêu cầu: 20/06/2025</span><br>
                                <span class="text-muted">Ngày hoàn tiền: 21/06/2025</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-delivered">Đã hoàn thành</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('client.detailWallet') }}">Xem chi
                                    tiết</a>
                            </div>
                        </div>
                    </div>

                    <div class="refund-item mb-3 p-3 rounded-3">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <span class="fw-bold">Mã đặt lịch: AP12346</span><br>
                                <span class="text-muted">Lý do: Dịch vụ bị hủy</span><br>
                                <span class="text-muted">Số tiền hoàn: 1.875.000 VNĐ</span><br>
                                <span class="text-muted">Ngày yêu cầu: 20/06/2025</span><br>
                                <span class="text-muted">Ngày hoàn tiền: 21/06/2025</span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="status-label status-canceled">Từ chối</span>
                            </div>
                            <div class="col-md-3 text-center">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('client.detailWallet') }}">Xem chi
                                    tiết</a>
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
