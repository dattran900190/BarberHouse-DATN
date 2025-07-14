@extends('layouts.AdminLayout')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Bảng điều khiển</h3>
            <h6 class="op-7 mb-2">Trang quản trị hệ thống tiệm & bán hàng</h6>
        </div>
        {{-- <div class="ms-md-auto py-2 py-md-0">
            <a href="#" class="btn btn-label-info btn-round me-2">Quản lý</a>
            <a href="#" class="btn btn-primary btn-round">Thêm khách hàng</a>
        </div> --}}
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-3 mb-3">
            <div class="card card-stats card-round">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Lượt đặt lịch</p>
                                <h4 class="card-title">{{ number_format($totalBookings) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                <h4 class="card-title">{{ number_format($totalRegistrations) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                <h4 class="card-title">₫{{ number_format($serviceRevenue) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                <h4 class="card-title">₫{{ number_format($productRevenue) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            @foreach ($barberStats as $barber)
                                <tr>
                                    <td>{{ $barber['name'] }}</td>
                                    <td class="text-center">{{ $barber['cut_count'] }}</td>
                                    <td class="text-center">{{ $barber['avg_rating'] }} ⭐</td>
                                </tr>
                            @endforeach
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
                <div class="card-category">{{ now()->format('d/m/Y') }}</div>
            </div>
            <div class="card-body pb-0">
                <div class="mb-4 mt-2">
                    <h1>₫{{ number_format($todayRevenue, 2) }}</h1>
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
                        @foreach ($upcomingAppointments as $appointment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $appointment->name }} -
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                <span
                                    class="badge bg-success">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m') }}</span>
                            </li>
                        @endforeach
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
                        @foreach ($topProducts as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item->productVariant->product->name ?? 'Không xác định' }}
                                <span class="badge bg-primary rounded-pill">{{ $item->total_sold }} sp</span>
                            </li>
                        @endforeach


                    </ul>
                </div>
            </div>
        </div>
    </div>

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
                                @foreach ($latestTransactions as $order)
                                    <tr>
                                        <th scope="row">
                                            <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            Giao dịch #{{ $order->order_code ?? $order->id }}
                                        </th>
                                        <td class="text-end">
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y, H:i') }}</td>
                                        <td class="text-end">₫{{ number_format($order->total_money) }}</td>
                                        <td class="text-end">
                                            <span class="badge badge-success">{{ ucfirst($order->status) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection

@section('css')
@endsection
