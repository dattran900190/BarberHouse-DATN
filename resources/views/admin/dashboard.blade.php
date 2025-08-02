@extends('layouts.AdminLayout')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Bảng điều khiển</h3>
            <h6 class="op-7 mb-2">Trang quản trị hệ thống tiệm & bán hàng</h6>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
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
        <div class="col-sm-6 col-lg-3">
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
        <div class="col-sm-6 col-lg-3">
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
        <div class="col-sm-6 col-lg-3">
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
                <!-- Hiệu suất nhân viên -->
                <div class="col-12">
                    <div class="card card-round h-100">
                        <div class="card-header d-flex flex-column">
                            <h5 class="card-title mb-1 fw-bold">Hiệu suất nhân viên (tuần này)</h5>
                            <span class="text-muted small">{{ $weekRange ?? 'Tuần hiện tại' }}</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle mb-0">
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
                                                <td colspan="3" class="text-center text-muted">Không có dữ liệu</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
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

                <!-- Lịch hẹn sắp tới -->
                {{-- <div class="col-12">
                    <div class="card card-round h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0 fw-bold">Lịch hẹn sắp tới</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @forelse ($upcomingAppointments as $appointment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-semibold">{{ $appointment->name ?? '-' }}</span> -
                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                        </div>
                                        <span class="badge bg-success">
                                            {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m') }}
                                        </span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted">Không có lịch hẹn sắp tới</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div> --}}
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
                                        href="#pills-top-product" role="tab">Bán chạy</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-low-product-tab" data-bs-toggle="pill"
                                        href="#pills-low-product" role="tab">Ít bán</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Bán chạy -->
                                <div class="tab-pane fade show active" id="pills-top-product" role="tabpanel">
                                    <ul class="list-group list-group-flush">
                                        @forelse ($topProducts as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $item->productVariant->product->name ?? 'Không xác định' }}</strong>
                                                </div>
                                                <span class="badge bg-success rounded-pill">
                                                    {{ $item->total_sold }} sp
                                                </span>
                                            </li>
                                        @empty
                                            <li class="list-group-item text-center text-muted">Không có sản phẩm nào được
                                                bán</li>
                                        @endforelse
                                    </ul>
                                </div>

                                <!-- Ít bán -->
                                <div class="tab-pane fade" id="pills-low-product" role="tabpanel">
                                    <ul class="list-group list-group-flush">
                                        @forelse ($lowSellingProducts as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $item->product->name ?? 'Không xác định' }}</strong>
                                                </div>
                                                <span class="badge bg-warning rounded-pill">
                                                    {{ $item->total_sold }} sp
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
                                    <ul class="list-group list-group-flush">
                                        @forelse ($topServices as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
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
                                            <li class="list-group-item text-center text-muted">Không có dịch vụ nào</li>
                                        @endforelse
                                    </ul>
                                </div>

                                <!-- Ít dùng -->
                                <div class="tab-pane fade" id="pills-low-service" role="tabpanel">
                                    <ul class="list-group list-group-flush">
                                        @forelse ($lowUsageServices as $item)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
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
            fetch('{{ route('dashboard') }}?' + new URLSearchParams({
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
            fetch('{{ route('dashboard') }}?' + new URLSearchParams({
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
            fetch('{{ route('dashboard') }}?' + new URLSearchParams({
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
    </script>
@endsection
