@extends('layouts.AdminLayout')

@section('title', 'Dashboard')

@section('content')
    

    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Bảng điều khiển</h3>
            <h6 class="op-7 mb-2">Trang quản trị hệ thống tiệm & bán hàng</h6>
        </div>
        <div class="ms-md-auto py-2 py-md-0">
            <a href="#" class="btn btn-label-info btn-round me-2">Quản lý</a>
            <a href="#" class="btn btn-primary btn-round">Thêm khách hàng</a>
        </div>
    </div>


    <div class="row">
        {{-- Visitors --}}
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Khách truy cập</p>
                                <h4 class="card-title">1.294</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Subscribers --}}
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                <i class="fas fa-user-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Đăng ký</p>
                                <h4 class="card-title">1.303</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Đơn hàng (dịch vụ) --}}
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-luggage-cart"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Doanh thu dịch vụ</p>
                                <h4 class="card-title">₫1.345.000</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Đơn hàng (sản phẩm) --}}
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                <i class="fas fa-tags"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Doanh thu sản phẩm</p>
                                <h4 class="card-title">₫576.000</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Biểu đồ thống kê -->
    <div class="row align-items-stretch">
        <div class="col-md-8 mb-3">
            <div class="card card-round h-100 d-flex flex-column">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Thống kê người dùng</div>
                        <div class="card-tools">
                            <a href="#" class="btn btn-label-success btn-round btn-sm me-2">
                                <i class="fa fa-file-export btn-label"></i> Export
                            </a>
                            <a href="#" class="btn btn-label-info btn-round btn-sm">
                                <i class="fa fa-print btn-label"></i> Print
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 375px;">
                        <canvas id="statisticsChart"></canvas>
                        
                    </div>
                    <div id="myChartLegend" class="mt-2 text-center">[Chart Legend]</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card card-round h-100 d-flex flex-column">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Hiệu suất nhân viên (tuần này)</div>
                    </div>
                    <div class="card-category">Từ 01/07 đến 06/07</div>
                </div>
                <div class="card-body pb-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nhân viên</th>
                                <th class="text-center">Lượt cắt</th>
                                <th class="text-center">Đánh giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Trịnh Văn Nam</td>
                                <td class="text-center">27</td>
                                <td class="text-center">4.8 ⭐</td>
                            </tr>
                            <tr>
                                <td>Ngô Thị Mai</td>
                                <td class="text-center">22</td>
                                <td class="text-center">4.6 ⭐</td>
                            </tr>
                            <tr>
                                <td>Vũ Thành Công</td>
                                <td class="text-center">19</td>
                                <td class="text-center">4.9 ⭐</td>
                            </tr>
                            <tr>
                                <td>Vũ Thành Công</td>
                                <td class="text-center">19</td>
                                <td class="text-center">4.9 ⭐</td>
                            </tr>
                            <tr>
                                <td>Vũ Thành Công</td>
                                <td class="text-center">19</td>
                                <td class="text-center">4.9 ⭐</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card card-primary card-round">
            <div class="card-header">
                <div class="card-head-row">
                    <div class="card-title">Doanh thu hôm nay</div>
                    <div class="card-tools">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-label-light dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Lọc
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#">Hôm nay</a>
                                <a class="dropdown-item" href="#">7 ngày gần nhất</a>
                                <a class="dropdown-item" href="#">30 ngày gần nhất</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-category">06/07/2025</div>
            </div>
            <div class="card-body pb-0">
                <div class="mb-4 mt-2">
                    <h1>$4,578.58</h1>
                    <p class="text-muted">Tổng doanh thu hôm nay</p>
                </div>
                <div class="pull-in">
                    <canvas id="dailySalesChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title">Lịch hẹn sắp tới</div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nguyễn Văn A - 10:00 AM
                            <span class="badge bg-success">06/07</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Trần Thị B - 11:30 AM
                            <span class="badge bg-success">06/07</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Phạm Văn C - 01:00 PM
                            <span class="badge bg-success">06/07</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-title">Top sản phẩm bán chạy</div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Sáp vuốt tóc Volcanic Clay
                            <span class="badge bg-primary rounded-pill">52 sp</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Dầu gội đầu Herbal
                            <span class="badge bg-primary rounded-pill">39 sp</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Gel tạo kiểu Gatsby
                            <span class="badge bg-primary rounded-pill">35 sp</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Bảng giao dịch -->
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                        <h4 class="card-title">Lịch sử giao dịch</h4>
                        <div class="card-tools">
                            <button class="btn btn-icon btn-link btn-primary btn-xs">
                                <i class="fa fa-sync-alt btn-refresh-card"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Thanh toán #</th>
                                    <th class="text-end">Ngày giờ</th>
                                    <th class="text-end">Số tiền</th>
                                    <th class="text-end">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                    <tr>
                                        <th scope="row">
                                            <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            Giao dịch #1023{{ $i }}
                                        </th>
                                        <td class="text-end">Mar 19, 2020, 2.45pm</td>
                                        <td class="text-end">₫250.000</td>
                                        <td class="text-end">
                                            <span class="badge badge-success">Hoàn thành</span>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('js')
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}

@stop

@section('css')
   
@endsection
