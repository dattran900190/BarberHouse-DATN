@extends('layouts.AdminLayout')

@section('title', 'Tổng quan')

@section('content')
    <style>
        .scroll-rows-5 {
            height: 260px;
            min-height: 260px;
            overflow-y: auto;
        }

        .scroll-rows-5 table {
            margin-bottom: 0;
        }

        .scroll-rows-5 ul {
            margin-bottom: 0;
        }
    </style>
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Bảng điều khiển</h3>
            <h6 class="op-7 mb-2">Trang quản trị hệ thống tiệm & bán hàng</h6>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats card-round h-100">
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

        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats card-round h-100">
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

        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats card-round h-100">
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
                                <h4 class="card-title">{{ number_format($serviceRevenue) }} VNĐ</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card card-stats card-round h-100">
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
                                <h4 class="card-title">{{ number_format($productRevenue) }} VNĐ</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu hôm nay -->
    <!-- Doanh thu hôm nay - CẢI THIỆN -->
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card card-stats card-round h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Doanh thu hôm nay</p>
                                <h4 class="card-title">{{ number_format($todayRevenue) }} VNĐ</h4>
                                <div class="mt-2">
                                    <small class="text-success d-block">
                                        <i class="fas fa-cut me-1"></i>
                                        Dịch vụ: {{ number_format($todayServiceRevenue) }} VNĐ
                                        @if ($todayServiceCount > 0)
                                            ({{ $todayServiceCount }} lượt)
                                        @endif
                                    </small>
                                    <small class="text-info d-block">
                                        <i class="fas fa-shopping-cart me-1"></i>
                                        Sản phẩm: {{ number_format($todayProductRevenue) }} VNĐ
                                        @if ($todayProductOrderCount > 0)
                                            ({{ $todayProductOrderCount }} đơn)
                                        @endif
                                    </small>

                                    <!-- Hiển thị tỷ lệ -->
                                    @if ($todayRevenue > 0)
                                        @php
                                            $servicePercent = round(($todayServiceRevenue / $todayRevenue) * 100, 1);
                                            $productPercent = round(($todayProductRevenue / $todayRevenue) * 100, 1);
                                        @endphp
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ $servicePercent }}%"
                                                title="Dịch vụ: {{ $servicePercent }}%"></div>
                                            <div class="progress-bar bg-info" role="progressbar"
                                                style="width: {{ $productPercent }}%"
                                                title="Sản phẩm: {{ $productPercent }}%"></div>
                                        </div>
                                        <small class="text-muted">
                                            Dịch vụ {{ $servicePercent }}% • Sản phẩm {{ $productPercent }}%
                                        </small>
                                    @endif

                                    <!-- So sánh với hôm qua -->
                                    @php
                                        $yesterday = \Carbon\Carbon::yesterday();
                                        $yesterdayServiceRevenue = \App\Models\Appointment::whereDate(
                                            'appointment_time',
                                            $yesterday,
                                        )
                                            ->where('status', 'completed')
                                            ->sum('total_amount');
                                        $yesterdayProductRevenue = \App\Models\Order::whereDate(
                                            'created_at',
                                            $yesterday,
                                        )
                                            ->where('status', 'completed')
                                            ->sum('total_money');
                                        $yesterdayTotal = $yesterdayServiceRevenue + $yesterdayProductRevenue;

                                        if ($yesterdayTotal > 0) {
                                            $changePercent =
                                                (($todayRevenue - $yesterdayTotal) / $yesterdayTotal) * 100;
                                            $isIncrease = $changePercent > 0;
                                            $changeClass = $isIncrease ? 'text-success' : 'text-danger';
                                            $changeIcon = $isIncrease ? 'fa-arrow-up' : 'fa-arrow-down';
                                        }
                                    @endphp

                                    @if (isset($changePercent) && $yesterdayTotal > 0)
                                        <div class="mt-2 pt-2 border-top">
                                            <small class="{{ $changeClass }}">
                                                <i class="fas {{ $changeIcon }} me-1"></i>
                                                {{ $isIncrease ? '+' : '' }}{{ number_format($changePercent, 1) }}% so với
                                                hôm qua
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                Hôm qua: {{ number_format($yesterdayTotal) }} VNĐ
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thêm thống kê bổ sung -->
        <div class="col-lg-4">
            <div class="card card-stats card-round h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Hoạt động hôm nay</p>
                                <div class="mt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Lịch đã hoàn thành:</span>
                                        <strong class="text-success">{{ $todayServiceCount ?? 0 }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Đơn hàng hoàn thành:</span>
                                        <strong class="text-info">{{ $todayProductOrderCount ?? 0 }}</strong>
                                    </div>

                                    @php
                                        $todayPendingAppointments = \App\Models\Appointment::whereDate(
                                            'appointment_time',
                                            \Carbon\Carbon::today(),
                                        )
                                            ->whereIn('status', ['confirmed', 'checked-in', 'progress'])
                                            ->count();
                                        $todayPendingOrders = \App\Models\Order::whereDate(
                                            'created_at',
                                            \Carbon\Carbon::today(),
                                        )
                                            ->whereIn('status', ['pending', 'confirmed'])
                                            ->count();
                                    @endphp

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted">Lịch đang xử lý:</span>
                                        <strong class="text-warning">{{ $todayPendingAppointments }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Đơn hàng chờ:</span>
                                        <strong class="text-warning">{{ $todayPendingOrders }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trung bình doanh thu -->
        <div class="col-lg-4">
            <div class="card card-stats card-round h-100">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-icon">
                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                        <div class="col col-stats ms-3 ms-sm-0">
                            <div class="numbers">
                                <p class="card-category">Trung bình tháng này</p>
                                @php
                                    $currentMonth = date('n');
                                    $currentYear = date('Y');
                                    $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
                                    $currentDay = date('j');

                                    $monthServiceRevenue = \App\Models\Appointment::whereMonth(
                                        'appointment_time',
                                        $currentMonth,
                                    )
                                        ->whereYear('appointment_time', $currentYear)
                                        ->where('status', 'completed')
                                        ->sum('total_amount');
                                    $monthProductRevenue = \App\Models\Order::whereMonth('created_at', $currentMonth)
                                        ->whereYear('created_at', $currentYear)
                                        ->where('status', 'completed')
                                        ->sum('total_money');
                                    $monthTotal = $monthServiceRevenue + $monthProductRevenue;

                                    $avgPerDay = $currentDay > 0 ? $monthTotal / $currentDay : 0;
                                    $projectedMonth = $avgPerDay * $daysInMonth;
                                @endphp
                                <h4 class="card-title">{{ number_format($avgPerDay) }} VNĐ/ngày</h4>
                                <div class="mt-2">
                                    <small class="text-muted d-block">
                                        Tổng tháng này: {{ number_format($monthTotal) }} VNĐ
                                    </small>
                                    <small class="text-muted d-block">
                                        Dự kiến cuối tháng: {{ number_format($projectedMonth) }} VNĐ
                                    </small>
                                    @if ($projectedMonth > 0 && $monthTotal > 0)
                                        @php
                                            $monthProgress = ($monthTotal / $projectedMonth) * 100;
                                        @endphp
                                        <div class="progress mt-2" style="height: 4px;">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                style="width: {{ min($monthProgress, 100) }}%"></div>
                                        </div>
                                        <small class="text-primary">{{ number_format($monthProgress, 1) }}% tiến độ
                                            tháng</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Chart Section -->
    <div class="row g-3 mb-4">
        <!-- Biểu đồ theo tuần/khoảng ngày -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-2 fw-bold">Thống kê doanh thu theo khoảng ngày</h5>
                    <div class="d-flex align-items-end gap-2 flex-wrap">
                        <div>
                            <label for="week_start" class="form-label small mb-1">Từ ngày:</label>
                            <input type="date" id="week_start" class="form-control form-control-sm"
                                value="{{ $viewWeekStart ?? '' }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label for="week_end" class="form-label small mb-1">Đến ngày:</label>
                            <input type="date" id="week_end" class="form-control form-control-sm"
                                value="{{ $viewWeekEnd ?? '' }}" max="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <button type="button" id="resetWeekFilter" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-refresh me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 300px;">
                        <canvas id="weekChart"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <!-- Biểu đồ theo tháng -->
        <div class="col-lg-6">
            <div class="card card-round shadow-sm h-100 d-flex flex-column">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-2 fw-bold">Thống kê doanh thu theo tháng</h5>
                    <div class="d-flex align-items-end gap-2 flex-wrap">
                        <div>
                            <label for="selected_month" class="form-label small mb-1 text-muted">Chọn tháng:</label>
                            <select id="selected_month" class="form-select form-select-sm">
                                <option value="">Tất cả tháng {{ $year ?? date('Y') }}</option>
                                @foreach ($availableMonths as $monthNum)
                                    <option value="{{ $monthNum }}"
                                        {{ isset($selectedMonth) && $selectedMonth == $monthNum ? 'selected' : '' }}>
                                        Tháng {{ $monthNum }}/{{ $year ?? date('Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="button" id="resetMonthFilter" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-refresh me-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 300px;">
                        <canvas id="monthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="row g-3">
        <!-- Cột trái -->
        <div class="col-lg-6">
            <div class="row g-3">
                <!-- Dịch vụ phổ biến & ít dùng -->
                <div class="col-12">
                    <div class="card card-round h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold">Dịch vụ phổ biến & ít dùng</h5>
                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-top-service-tab" data-bs-toggle="pill"
                                        href="#pills-top-service" role="tab">Phổ biến</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-low-service-tab" data-bs-toggle="pill"
                                        href="#pills-low-service" role="tab">Ít dùng</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Phổ biến -->
                                <div class="tab-pane fade show active" id="pills-top-service" role="tabpanel">
                                    <div class="scroll-rows-5">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($topServices as $item)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $item->service->name ?? 'Không xác định' }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ number_format($item->service->price ?? 0) }}
                                                            VNĐ</small>
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $item->usage_count }} lượt
                                                    </span>
                                                </li>
                                            @empty
                                                <li class="list-group-item text-center text-muted">Không có dịch vụ nào
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>

                                <!-- Ít dùng -->
                                <div class="tab-pane fade" id="pills-low-service" role="tabpanel">
                                    <div class="scroll-rows-5">
                                        <ul class="list-group list-group-flush">
                                            @forelse ($lowUsageServices as $item)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $item->name ?? 'Không xác định' }}</strong><br>
                                                        <small class="text-muted">{{ number_format($item->price ?? 0) }}
                                                            VNĐ</small>
                                                    </div>
                                                    <span class="badge bg-secondary rounded-pill">
                                                        {{ $item->usage_count }} lượt
                                                    </span>
                                                </li>
                                            @empty
                                                <li class="list-group-item text-center text-muted">Không có dữ liệu</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê ngày nghỉ của thợ -->
                <div class="col-12">
                    <div class="card card-round h-100">
                        <div class="card-header d-flex flex-column">
                            <h5 class="card-title mb-1 fw-bold">Ngày nghỉ của thợ (Top 5)</h5>
                            <div class="d-flex align-items-end gap-2 flex-wrap mt-2">
                                <div>
                                    <label for="leave_month" class="form-label small mb-1 text-muted">Chọn tháng:</label>
                                    <select id="leave_month" class="form-select form-select-sm">
                                        <option value="">Tháng hiện tại</option>
                                        @foreach ($availableMonths as $monthNum)
                                            <option value="{{ $monthNum }}"
                                                {{ isset($selectedLeaveMonth) && $selectedLeaveMonth == $monthNum ? 'selected' : '' }}>
                                                Tháng {{ $monthNum }}/{{ $year ?? date('Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="leave_branch" class="form-label small mb-1 text-muted">Chi nhánh:</label>
                                    <select id="leave_branch" class="form-select form-select-sm">
                                        <option value="">Tất cả chi nhánh</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ isset($selectedBranch) && $selectedBranch == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="button" id="resetLeaveFilter"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-refresh me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="barberLeaveTable" class="table table-hover table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nhân viên</th>
                                            <th class="text-center">Tổng ngày nghỉ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($barberLeaves as $barber)
                                            <tr>
                                                <td>{{ $barber->name }}</td>
                                                <td class="text-center">{{ $barber->total_off }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center text-muted">Không có dữ liệu ngày
                                                    nghỉ</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải -->
        <div class="col-lg-6">
            <div class="row g-3">
                <!-- Sản phẩm bán chạy & ít bán -->
                <div class="col-12">
                    <div class="card card-round h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold">Sản phẩm bán chạy & ít bán</h5>
                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-top-product-tab" data-bs-toggle="pill"
                                        href="#pills-top-product" role="tab">
                                        Bán chạy
                                        <span class="badge bg-success ms-1"
                                            id="top-count">{{ $topProducts->total() ?? count($topProducts) }}</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-low-product-tab" data-bs-toggle="pill"
                                        href="#pills-low-product" role="tab">
                                        Ít bán
                                        <span class="badge bg-warning ms-1"
                                            id="low-count">{{ $lowSellingProducts->total() ?? count($lowSellingProducts) }}</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Bộ lọc và tìm kiếm -->
                        <div class="card-body border-bottom">
                            <!-- Bỏ form wrapper, chỉ dùng các input riêng lẻ -->
                            <div class="row g-3" id="product-filter-controls">
                                <!-- Tìm kiếm -->
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control" name="search"
                                            placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}"
                                            id="product-search">
                                    </div>
                                </div>

                                <!-- Số lượng hiển thị -->
                                <div class="col-md-3">
                                    <select name="per_page" class="form-select form-select-sm" id="per-page-select">
                                        <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 sản phẩm
                                        </option>
                                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10
                                            sản phẩm</option>
                                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20 sản
                                            phẩm</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 sản
                                            phẩm</option>
                                        <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Tất cả
                                        </option>
                                    </select>
                                </div>

                                <!-- Sắp xếp -->
                                <div class="col-md-3">
                                    <select name="sort_by" class="form-select form-select-sm" id="sort-select">
                                        <option value="total_sold"
                                            {{ request('sort_by', 'total_sold') == 'total_sold' ? 'selected' : '' }}>Theo
                                            số lượng bán</option>
                                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Theo
                                            tên A-Z</option>
                                        <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Theo
                                            giá</option>
                                        <option value="created_at"
                                            {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Theo ngày tạo
                                        </option>
                                    </select>
                                </div>

                                <!-- Action buttons -->
                                <div class="col-md-2">
                                    <div class="btn-group btn-group-sm w-100">
                                        <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()"
                                            title="Reset lọc">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading spinner -->
                            <div class="text-center loading-spinner" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-primary mt-2" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <small class="text-muted d-block mt-1">Đang tải...</small>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Tab Bán chạy -->
                                <div class="tab-pane fade show active" id="pills-top-product" role="tabpanel">
                                    <div class="scroll-rows-5">
                                        <div id="top-products-content">
                                            <ul class="list-group list-group-flush">
                                                @forelse ($topProducts as $index => $item)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 product-item"
                                                        data-name="{{ strtolower($item->productVariant->product->name ?? 'không xác định') }}">
                                                        <div class="d-flex align-items-center">
                                                            <!-- Ranking -->
                                                            <div class="me-3">
                                                                @if ($index < 3)
                                                                    <span class="badge bg-warning rounded-circle p-2">
                                                                        <i class="fas fa-medal"></i>
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-light text-dark rounded-circle p-2">
                                                                        {{ $index + 1 }}
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <!-- Product info -->
                                                            <div>
                                                                <strong
                                                                    class="d-block">{{ $item->productVariant->product->name ?? 'Không xác định' }}</strong>
                                                                <small class="text-muted">
                                                                    {{ number_format($item->productVariant->price ?? 0) }}
                                                                    VNĐ
                                                                    @if ($item->productVariant->product->category ?? false)
                                                                        •
                                                                        {{ $item->productVariant->product->category->name }}
                                                                    @endif
                                                                </small>
                                                                @php
                                                                    $totalSales = $topProducts->sum('total_sold');
                                                                    $percentage =
                                                                        $totalSales > 0
                                                                            ? round(
                                                                                ($item->total_sold / $totalSales) * 100,
                                                                                1,
                                                                            )
                                                                            : 0;
                                                                @endphp
                                                                <div class="progress mt-1" style="height: 4px;">
                                                                    <div class="progress-bar bg-success"
                                                                        style="width: {{ $percentage }}%"></div>
                                                                </div>
                                                                <small class="text-muted">{{ $percentage }}% tổng doanh
                                                                    số</small>
                                                            </div>
                                                        </div>

                                                        <div class="text-end">
                                                            <span class="badge bg-success rounded-pill fs-6 mb-1">
                                                                {{ $item->total_sold }} sp
                                                            </span>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ number_format($item->total_sold * ($item->productVariant->price ?? 0)) }}
                                                                VNĐ
                                                            </small>

                                                            <div class="btn-group btn-group-sm mt-1">

                                                            </div>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center text-muted border-0">
                                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                        Không có sản phẩm nào được bán
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Ít bán -->
                                <div class="tab-pane fade" id="pills-low-product" role="tabpanel">
                                    <div class="scroll-rows-5">
                                        <!-- Nội dung cho sản phẩm ít bán -->
                                        <div id="low-products-content">
                                            <ul class="list-group list-group-flush">
                                                @forelse ($lowSellingProducts as $index => $item)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 product-item"
                                                        data-name="{{ strtolower($item->product->name ?? 'không xác định') }}">
                                                        <div class="d-flex align-items-center">
                                                            <!-- Warning icon -->
                                                            <div class="me-3">
                                                                @if (($item->total_sold ?? 0) == 0)
                                                                    <span class="badge bg-danger rounded-circle p-2">
                                                                        <i class="fas fa-exclamation"></i>
                                                                    </span>
                                                                @else
                                                                    <span class="badge bg-warning rounded-circle p-2">
                                                                        <i class="fas fa-slow"></i>
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <!-- Product info -->
                                                            <div>
                                                                <strong
                                                                    class="d-block">{{ $item->product->name ?? 'Không xác định' }}</strong>
                                                                <small class="text-muted">
                                                                    {{ number_format($item->price) }} VNĐ
                                                                    @if ($item->product->category ?? false)
                                                                        • {{ $item->product->category->name }}
                                                                    @endif
                                                                </small>

                                                                <!-- Ngày tạo -->
                                                                <div class="mt-1">
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                                        Tạo:
                                                                        {{ $item->product->created_at->format('d/m/Y') }}
                                                                        ({{ $item->product->created_at->diffForHumans() }})
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="text-end">
                                                            @if (($item->total_sold ?? 0) == 0)
                                                                <span class="badge bg-danger rounded-pill fs-6 mb-1">
                                                                    Chưa bán
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning rounded-pill fs-6 mb-1">
                                                                    {{ $item->total_sold }} sp
                                                                </span>
                                                            @endif


                                                            <div class="btn-group btn-group-sm mt-1">

                                                            </div>
                                                        </div>
                                                    </li>
                                                @empty
                                                    <li class="list-group-item text-center text-muted border-0">
                                                        <i class="fas fa-smile fa-2x mb-2 d-block text-success"></i>
                                                        Tất cả sản phẩm đều bán tốt!
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>





                <!-- Hiệu suất nhân viên -->
                <div class="col-12">
                    <div class="card card-round h-100">
                        <div class="card-header d-flex flex-column">
                            <h5 class="card-title mb-1 fw-bold">Hiệu suất nhân viên (Top 5)</h5>
                            <div class="d-flex align-items-end gap-2 flex-wrap mt-2">
                                <div>
                                    <label for="performance_month" class="form-label small mb-1 text-muted">Chọn
                                        tháng:</label>
                                    <select id="performance_month" class="form-select form-select-sm">
                                        <option value="">Tháng hiện tại</option>
                                        @foreach ($availableMonths as $monthNum)
                                            <option value="{{ $monthNum }}"
                                                {{ isset($selectedPerformanceMonth) && $selectedPerformanceMonth == $monthNum ? 'selected' : '' }}>
                                                Tháng {{ $monthNum }}/{{ $year ?? date('Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="performance_branch" class="form-label small mb-1 text-muted">Chi
                                        nhánh:</label>
                                    <select id="performance_branch" class="form-select form-select-sm">
                                        <option value="">Tất cả chi nhánh</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ isset($selectedPerformanceBranch) && $selectedPerformanceBranch == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <button type="button" id="resetPerformanceFilter"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-refresh me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="barberPerformanceTable" class="table table-hover table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Nhân viên</th>
                                            <th class="text-center">Lượt cắt</th>
                                            <th class="text-center">Đánh giá</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($barberStats as $barber)
                                            <tr>
                                                <td>{{ $barber->name }}</td>
                                                <td class="text-center">{{ $barber->cut_count }}</td>
                                                <td class="text-center">
                                                    {{ number_format($barber->avg_rating, 1) }} ⭐
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Không có dữ liệu
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch sử giao dịch - Full width -->
    <div class="row g-3 mt-2">
        <div class="col-12">
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
                    <div class="table-responsive scroll-rows-5">
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
                                @php
                                    $statusLabels = [
                                        'pending' => ['label' => 'Chờ xử lý', 'class' => 'bg-warning'],
                                        'unconfirmed' => ['label' => 'Chưa xác nhận', 'class' => 'bg-secondary'],
                                        'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'bg-info'],
                                        'checked-in' => ['label' => 'Đã đến', 'class' => 'bg-primary'],
                                        'progress' => ['label' => 'Đang thực hiện', 'class' => 'bg-info'],
                                        'completed' => ['label' => 'Hoàn tất', 'class' => 'bg-success'],
                                        'cancelled' => ['label' => 'Đã huỷ', 'class' => 'bg-danger'],
                                    ];
                                @endphp

                                @foreach ($latestTransactions as $transaction)
                                    <tr>
                                        <td>
                                            @if ($transaction->type === 'order')
                                                Mua hàng #{{ $transaction->code }}
                                            @else
                                                Đặt lịch #{{ $transaction->code }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $transaction->created_at->format('d/m/Y, H:i') }}</td>
                                        <td class="text-end">{{ number_format($transaction->amount) }} VNĐ</td>
                                        <td class="text-end">
                                            @php
                                                $status = strtolower($transaction->status);
                                                $label = $statusLabels[$status]['label'] ?? ucfirst($status);
                                                $class = $statusLabels[$status]['class'] ?? 'bg-secondary';
                                            @endphp
                                            <span class="badge {{ $class }}">{{ $label }}</span>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Biến global để lưu trữ chart instances
        let weekChart, monthChart;

        // Data gốc từ server
        const originalWeekData = {
            labels: @json($weekLabels),
            serviceRevenue: @json($weekServiceRevenue),
            productRevenue: @json($weekProductRevenue)
        };

        const originalMonthData = {
            labels: @json($monthLabels),
            serviceRevenue: @json($monthServiceRevenue),
            productRevenue: @json($monthProductRevenue)
        };

        // Khởi tạo biểu đồ tuần
        function initWeekChart(labels, serviceData, productData) {
            const weekCtx = document.getElementById('weekChart').getContext('2d');

            if (weekChart) {
                weekChart.destroy();
            }

            weekChart = new Chart(weekCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu dịch vụ',
                        data: serviceData,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        tension: 0.4
                    }, {
                        label: 'Doanh thu sản phẩm',
                        data: productData,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString() +
                                        ' VNĐ';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Thời gian'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Doanh thu (VNĐ)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Khởi tạo biểu đồ tháng
        function initMonthChart(labels, serviceData, productData) {
            const monthCtx = document.getElementById('monthChart').getContext('2d');

            if (monthChart) {
                monthChart.destroy();
            }

            monthChart = new Chart(monthCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Doanh thu dịch vụ',
                        data: serviceData,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Doanh thu sản phẩm',
                        data: productData,
                        backgroundColor: 'rgba(255, 159, 64, 0.6)',
                        borderColor: 'rgba(255, 159, 64, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y.toLocaleString() +
                                        ' VNĐ';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Tháng'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Doanh thu (VNĐ)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' VNĐ';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Khởi tạo biểu đồ với dữ liệu ban đầu
        document.addEventListener('DOMContentLoaded', function() {
            initWeekChart(originalWeekData.labels, originalWeekData.serviceRevenue, originalWeekData
                .productRevenue);
            initMonthChart(originalMonthData.labels, originalMonthData.serviceRevenue, originalMonthData
                .productRevenue);
        });

        // Xử lý lọc biểu đồ tuần
        function handleWeekFilter() {
            const startDate = document.getElementById('week_start').value;
            const endDate = document.getElementById('week_end').value;

            if (!startDate && !endDate) {
                initWeekChart(originalWeekData.labels, originalWeekData.serviceRevenue, originalWeekData.productRevenue);
                return;
            }

            // Gửi AJAX request để lấy dữ liệu mới
            fetch('{{ route('admin.dashboard') }}?' + new URLSearchParams({
                    week_start: startDate,
                    week_end: endDate,
                    ajax: '1'
                }))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        initWeekChart(data.weekLabels, data.weekServiceRevenue, data.weekProductRevenue);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Xử lý lọc biểu đồ tháng
        function handleMonthFilter() {
            const selectedMonth = document.getElementById('selected_month').value;

            if (!selectedMonth) {
                initMonthChart(originalMonthData.labels, originalMonthData.serviceRevenue, originalMonthData
                    .productRevenue);
                return;
            }

            // Gửi AJAX request để lấy dữ liệu mới
            fetch('{{ route('admin.dashboard') }}?' + new URLSearchParams({
                    selected_month: selectedMonth,
                    ajax: '1'
                }))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        initMonthChart(data.monthLabels, data.monthServiceRevenue, data.monthProductRevenue);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Xử lý lọc ngày nghỉ
        function handleLeaveFilter() {
            const month = document.getElementById('leave_month').value;
            const branch = document.getElementById('leave_branch').value;

            // Gửi AJAX request để lấy dữ liệu ngày nghỉ mới
            fetch('{{ route('admin.dashboard') }}?' + new URLSearchParams({
                    leave_month: month,
                    leave_branch: branch,
                    ajax: '1'
                }))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật bảng ngày nghỉ
                        const tbody = document.querySelector('#barberLeaveTable tbody');
                        tbody.innerHTML = '';

                        if (data.barberLeaves.length > 0) {
                            data.barberLeaves.forEach(barber => {
                                const row = `
                                    <tr>
                                        <td>${barber.name}</td>
                                        <td class="text-center">${barber.total_off}</td>
                                    </tr>
                                `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="2" class="text-center text-muted">Không có dữ liệu ngày nghỉ</td></tr>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Event listeners
        document.getElementById('week_start').addEventListener('change', handleWeekFilter);
        document.getElementById('week_end').addEventListener('change', handleWeekFilter);
        document.getElementById('selected_month').addEventListener('change', handleMonthFilter);

        // Reset filters
        document.getElementById('resetWeekFilter').addEventListener('click', function() {
            document.getElementById('week_start').value = '';
            document.getElementById('week_end').value = '';
            initWeekChart(originalWeekData.labels, originalWeekData.serviceRevenue, originalWeekData
                .productRevenue);
        });

        document.getElementById('resetMonthFilter').addEventListener('click', function() {
            document.getElementById('selected_month').value = '';
            initMonthChart(originalMonthData.labels, originalMonthData.serviceRevenue, originalMonthData
                .productRevenue);
        });

        // Reset bộ lọc ngày nghỉ
        document.getElementById('resetLeaveFilter').addEventListener('click', function() {
            document.getElementById('leave_month').value = '';
            document.getElementById('leave_branch').value = '';
            handleLeaveFilter();
        });

        // Event listeners cho bộ lọc ngày nghỉ
        document.getElementById('leave_month').addEventListener('change', handleLeaveFilter);
        document.getElementById('leave_branch').addEventListener('change', handleLeaveFilter);

        // Xử lý lọc hiệu suất nhân viên
        function handlePerformanceFilter() {
            const month = document.getElementById('performance_month').value;
            const branch = document.getElementById('performance_branch').value;

            // Gửi AJAX request để lấy dữ liệu hiệu suất nhân viên mới
            fetch('{{ route('admin.dashboard') }}?' + new URLSearchParams({
                    performance_month: month,
                    performance_branch: branch,
                    ajax: '1'
                }))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cập nhật bảng hiệu suất nhân viên
                        const tbody = document.querySelector('#barberPerformanceTable tbody');
                        tbody.innerHTML = '';

                        if (data.barberStats.length > 0) {
                            data.barberStats.forEach(barber => {
                                const row = `
                                    <tr>
                                        <td>${barber.name}</td>
                                        <td class="text-center">${barber.cut_count}</td>
                                        <td class="text-center">${barber.avg_rating} ⭐</td>
                                    </tr>
                                `;
                                tbody.innerHTML += row;
                            });
                        } else {
                            tbody.innerHTML =
                                '<tr><td colspan="3" class="text-center text-muted">Không có dữ liệu</td></tr>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Reset bộ lọc hiệu suất nhân viên
        document.getElementById('resetPerformanceFilter').addEventListener('click', function() {
            document.getElementById('performance_month').value = '';
            document.getElementById('performance_branch').value = '';
            handlePerformanceFilter();
        });

        // Event listeners cho bộ lọc hiệu suất nhân viên
        document.getElementById('performance_month').addEventListener('change', handlePerformanceFilter);
        document.getElementById('performance_branch').addEventListener('change', handlePerformanceFilter);
    </script>
    <script>
        // Global variables
        let searchTimeout;
        let isLoading = false;

        // CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initializeProductFilter();
        });

        function initializeProductFilter() {
            // Real-time search với debounce
            document.getElementById('product-search').addEventListener('input', function() {
                const searchValue = this.value;
                clearTimeout(searchTimeout);

                // Client-side filtering for immediate feedback
                filterProductsClientSide(searchValue);

                // Server-side search after delay
                searchTimeout = setTimeout(() => {
                    filterProductsAjax();
                }, 800); // Tăng delay để giảm số request
            });

            // Auto-submit on select change
            document.getElementById('per-page-select').addEventListener('change', function() {
                filterProductsAjax();
            });

            document.getElementById('sort-select').addEventListener('change', function() {
                filterProductsAjax();
            });

            // Handle tab switching
            document.querySelectorAll('[data-bs-toggle="pill"]').forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    // Optionally refresh data when switching tabs
                    // filterProductsAjax();
                });
            });
        }

        // Client-side filtering for immediate feedback
        function filterProductsClientSide(searchValue) {
            const searchLower = searchValue.toLowerCase();
            const activeTabPane = document.querySelector('.tab-pane.active');

            if (!activeTabPane) return;

            const productItems = activeTabPane.querySelectorAll('.product-item');
            let visibleCount = 0;

            productItems.forEach(item => {
                const productName = item.dataset.name || '';
                if (productName.includes(searchLower)) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Update badge count for immediate feedback
            updateTabBadges(visibleCount, searchValue);
        }

        // AJAX filter function
        function filterProductsAjax() {
            if (isLoading) return;

            isLoading = true;
            showLoadingState();

            const formData = new FormData();
            formData.append('search', document.getElementById('product-search').value);
            formData.append('per_page', document.getElementById('per-page-select').value);
            formData.append('sort_by', document.getElementById('sort-select').value);
            formData.append('_token', csrfToken);

            fetch('{{ route('dashboard.filter-products') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateProductTables(data);
                        updateTabBadges(data.topCount, data.lowCount);
                    } else {
                        throw new Error(data.message || 'Unknown error occurred');
                    }
                })
                .catch(error => {
                    console.error('Filter error:', error);
                    showErrorMessage('Có lỗi xảy ra khi lọc sản phẩm. Vui lòng thử lại!');
                })
                .finally(() => {
                    isLoading = false;
                    hideLoadingState();
                });
        }

        // Update product tables with new data
        function updateProductTables(data) {
            // Update top products
            const topProductsContent = document.getElementById('top-products-content');
            if (topProductsContent && data.topProductsHtml) {
                topProductsContent.innerHTML = data.topProductsHtml;
            }

            // Update low products  
            const lowProductsContent = document.getElementById('low-products-content');
            if (lowProductsContent && data.lowProductsHtml) {
                lowProductsContent.innerHTML = data.lowProductsHtml;
            }
        }

        // Update tab badges
        function updateTabBadges(topCount, lowCount) {
            const topBadge = document.getElementById('top-count');
            const lowBadge = document.getElementById('low-count');

            if (topBadge && typeof topCount !== 'undefined') {
                topBadge.textContent = topCount;
            }

            if (lowBadge && typeof lowCount !== 'undefined') {
                lowBadge.textContent = lowCount;
            }
        }

        // Loading state functions
        function showLoadingState() {
            const activeTabPane = document.querySelector('.tab-pane.active');
            if (activeTabPane) {
                const content = activeTabPane.querySelector('.scroll-rows-5') || activeTabPane;
                content.style.opacity = '0.6';
                content.style.pointerEvents = 'none';
            }

            // Show loading spinner if exists
            const loadingSpinner = document.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.style.display = 'block';
            }
        }

        function hideLoadingState() {
            const activeTabPane = document.querySelector('.tab-pane.active');
            if (activeTabPane) {
                const content = activeTabPane.querySelector('.scroll-rows-5') || activeTabPane;
                content.style.opacity = '1';
                content.style.pointerEvents = 'auto';
            }

            // Hide loading spinner
            const loadingSpinner = document.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }
        }

        // Error handling
        function showErrorMessage(message) {
            // Create or update error alert
            let errorAlert = document.querySelector('.error-alert');
            if (!errorAlert) {
                errorAlert = document.createElement('div');
                errorAlert.className = 'alert alert-danger alert-dismissible fade show error-alert';
                errorAlert.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span class="error-message"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

                const cardBody = document.querySelector('.card-body');
                cardBody.insertBefore(errorAlert, cardBody.firstChild);
            }

            errorAlert.querySelector('.error-message').textContent = message;
            errorAlert.style.display = 'block';

            // Auto hide after 5 seconds
            setTimeout(() => {
                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
            }, 5000);
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('product-search').value = '';
            document.getElementById('per-page-select').value = '10';
            document.getElementById('sort-select').value = 'total_sold';
            filterProductsAjax();
        }

        // Product action functions - Updated to use modals instead of new tabs
        function viewProductModal(productId) {
            // Tạo modal để hiển thị thông tin sản phẩm thay vì mở tab mới
            showProductDetailModal(productId);
        }

        function showProductDetailModal(productId) {
            // Create and show product detail modal
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = 'productDetailModal';
            modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Chi tiết sản phẩm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Đang tải thông tin sản phẩm...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

            document.body.appendChild(modal);
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();

            // Load product details via AJAX
            fetch(`/admin/products/${productId}/details`)
                .then(response => response.json())
                .then(data => {
                    modal.querySelector('.modal-body').innerHTML = data.html;
                })
                .catch(error => {
                    modal.querySelector('.modal-body').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Không thể tải thông tin sản phẩm
                    </div>
                `;
                });

            // Clean up modal when closed
            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        function createPromotionModal(productId) {
            // Create promotion modal instead of redirecting
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tạo khuyến mãi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="promotionForm">
                            <input type="hidden" name="product_id" value="${productId}">
                            <div class="mb-3">
                                <label class="form-label">Tên khuyến mãi</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Giảm giá (%)</label>
                                <input type="number" class="form-control" name="discount" min="1" max="100" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Từ ngày</label>
                                <input type="date" class="form-control" name="start_date" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Đến ngày</label>
                                <input type="date" class="form-control" name="end_date" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" onclick="savePromotion()">Tạo khuyến mãi</button>
                    </div>
                </div>
            </div>
        `;

            document.body.appendChild(modal);
            new bootstrap.Modal(modal).show();

            modal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(modal);
            });
        }

        function boostProduct(productId) {
            // Show boost product options
            alert('Tính năng đẩy mạnh sản phẩm đang phát triển!');
        }

        function analyzeProduct(productId) {
            // Show product analytics
            alert('Tính năng phân tích sản phẩm đang phát triển!');
        }

        function savePromotion() {
            // Implementation for saving promotion
            alert('Tính năng tạo khuyến mãi đang phát triển!');
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + F to focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                document.getElementById('product-search').focus();
            }

            // Ctrl + 1 for top products tab
            if (e.ctrlKey && e.key === '1') {
                e.preventDefault();
                document.getElementById('pills-top-product-tab').click();
            }

            // Ctrl + 2 for low products tab
            if (e.ctrlKey && e.key === '2') {
                e.preventDefault();
                document.getElementById('pills-low-product-tab').click();
            }

            // Ctrl + R to reset filters
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                resetFilters();
            }
        });

        // Export functionality
        function exportProducts() {
            const activeTab = document.querySelector('.nav-link.active');
            const type = activeTab.getAttribute('href') === '#pills-top-product' ? 'top' : 'low';

            const searchParams = new URLSearchParams();
            searchParams.set('export', type);
            searchParams.set('search', document.getElementById('product-search').value);
            searchParams.set('sort_by', document.getElementById('sort-select').value);

            window.open(`{{ route('dashboard') }}?${searchParams.toString()}`, '_blank');
        }
    </script>

    <style>
        .product-item:hover {
            background-color: rgba(0, 123, 255, 0.05);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .progress {
            background-color: #f8f9fa;
        }

        .badge {
            font-size: 0.65em;
        }

        .btn-group-sm .btn {
            padding: 0.15rem 0.4rem;
            font-size: 0.7rem;
        }

        .nav-pills .nav-link {
            border-radius: 15px;
            font-size: 0.9rem;
        }

        .nav-pills .nav-link.active {
            background-color: #6861ce;
        }

        .list-group-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid #f8f9fa !important;
        }

        .list-group-item:last-child {
            border-bottom: none !important;
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .btn-group-sm .btn {
                padding: 0.2rem 0.3rem;
                font-size: 0.65rem;
            }

            .badge {
                font-size: 0.6em;
            }
        }
    </style>
@endsection
