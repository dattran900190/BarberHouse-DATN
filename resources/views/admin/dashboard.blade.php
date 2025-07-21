@extends('layouts.AdminLayout')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
        <div>
            <h3 class="fw-bold mb-3">Bảng điều khiển</h3>
            <h6 class="op-7 mb-2">Trang quản trị hệ thống tiệm & bán hàng</h6>
        </div>
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
                        <div class="card-title">Thống kê doanh thu dịch vụ & sản phẩm</div>
                        <div class="card-tools">
                            <form method="GET" id="chartFilterForm" class="mb-0">
                                <div class="row g-2 align-items-end">
                                    <!-- Filter Type -->
                                    <div class="col-auto">
                                        <label for="filter_type" class="form-label small mb-1">Lọc theo:</label>
                                        <select name="filter_type" id="filter_type" class="form-select form-select-sm"
                                            onchange="toggleFilterInputs()">
                                            <option value="month"
                                                {{ ($filterType ?? 'month') == 'month' ? 'selected' : '' }}>Tháng</option>
                                            <option value="date_range"
                                                {{ ($filterType ?? '') == 'date_range' ? 'selected' : '' }}>Khoảng ngày
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Filter tháng -->
                                    <div id="monthFilter" class="col-auto"
                                        style="{{ ($filterType ?? 'month') == 'month' ? '' : 'display: none;' }}">
                                        <label for="month" class="form-label small mb-1">Chọn tháng:</label>
                                        <select name="month" id="month" class="form-select form-select-sm">
                                            <option value="">Cả năm {{ date('Y') }}</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}"
                                                    {{ isset($month) && $month == $i ? 'selected' : '' }}>
                                                    Tháng {{ $i }}/{{ date('Y') }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <!-- Filter khoảng ngày -->
                                    <div id="dateRangeFilter"
                                        style="{{ ($filterType ?? '') == 'date_range' ? '' : 'display: none;' }}">
                                        <div class="row g-2">
                                            <div class="col-auto">
                                                <label for="start_date" class="form-label small mb-1">Từ ngày:</label>
                                                <input type="date" name="start_date" id="start_date"
                                                    class="form-control form-control-sm" value="{{ $startDate ?? '' }}"
                                                    max="{{ date('Y-m-d') }}">
                                            </div>
                                            <div class="col-auto">
                                                <label for="end_date" class="form-label small mb-1">Đến ngày:</label>
                                                <input type="date" name="end_date" id="end_date"
                                                    class="form-control form-control-sm" value="{{ $endDate ?? '' }}"
                                                    max="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Button -->
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-filter me-1"></i>Lọc
                                        </button>
                                        @if (request()->anyFilled(['filter_type', 'month', 'start_date', 'end_date']))
                                            <a href="{{ route('dashboard') }}"
                                                class="btn btn-outline-secondary btn-sm ms-1">
                                                <i class="fa fa-times me-1"></i>Xóa
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="min-height: 375px;">
                        <canvas id="statisticsChart"></canvas>
                    </div>
                    <div id="myChartLegend" class="mt-2 text-center"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card card-round h-100 d-flex flex-column">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Hiệu suất nhân viên (tuần này)</div>
                    </div>
                    <div class="card-category">{{ $weekRange ?? 'Tuần hiện tại' }}</div>
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
                            @forelse ($barberStats as $barber)
                                <tr>
                                    <td>{{ $barber->name }}</td>
                                    <td class="text-center">{{ $barber->cut_count }}</td>
                                    <td class="text-center">{{ $barber->avg_rating }} ⭐</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                        @forelse ($upcomingAppointments as $appointment)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $appointment->name }} -
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                <span
                                    class="badge bg-success">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m') }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-center">Không có lịch hẹn sắp tới</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-round">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Sản phẩm bán chạy & ít bán</div>
                        <div class="card-tools">
                            <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-top-tab" data-bs-toggle="pill"
                                        href="#pills-top" role="tab">Bán chạy</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-low-tab" data-bs-toggle="pill" href="#pills-low"
                                        role="tab">Ít bán</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        <!-- Tab sản phẩm bán chạy -->
                        <div class="tab-pane fade show active" id="pills-top" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @forelse ($topProducts as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->productVariant->product->name ?? 'Không xác định' }}</strong>
                                            <br><small class="text-muted">{{ $item->productVariant->name ?? '' }}</small>
                                        </div>
                                        <span class="badge bg-success rounded-pill">{{ $item->total_sold }} sp</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center">Không có sản phẩm nào được bán</li>
                                @endforelse
                            </ul>
                        </div>
                        <!-- Tab sản phẩm ít bán -->
                        <div class="tab-pane fade" id="pills-low" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @forelse ($lowSellingProducts as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->product->name ?? 'Không xác định' }}</strong>
                                            <br><small class="text-muted">Variant: {{ $item->name ?? 'Mặc định' }}</small>
                                        </div>
                                        <span class="badge bg-warning rounded-pill">{{ $item->total_sold }} sp</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center">Không có dữ liệu</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
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
                                @forelse ($latestTransactions as $order)
                                    <tr>
                                        <th scope="row">
                                            <button class="btn btn-icon btn-round btn-success btn-sm me-2">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            Giao dịch #{{ $order->order_code ?? $order->id }}
                                        </th>
                                        <td class="text-end">
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y, H:i') }}
                                        </td>
                                        <td class="text-end">₫{{ number_format($order->total_money) }}</td>
                                        <td class="text-end">
                                            <span class="badge badge-success">{{ ucfirst($order->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Không có giao dịch nào</td>
                                    </tr>
                                @endforelse
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
        const labels = @json($labels);
        const serviceRevenue = @json($serviceRevenuePerPeriod);
        const productRevenue = @json($productRevenuePerPeriod);

        const ctx = document.getElementById('statisticsChart').getContext('2d');
        const statisticsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                        label: 'Doanh thu dịch vụ',
                        data: serviceRevenue,
                        backgroundColor: 'rgba(0, 204, 102, 0.2)',
                        borderColor: '#00cc66',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Doanh thu sản phẩm',
                        data: productRevenue,
                        backgroundColor: 'rgba(52, 144, 220, 0.2)',
                        borderColor: '#3490dc',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₫' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        function toggleFilterInputs() {
            const filterType = document.getElementById('filter_type').value;

            // Ẩn tất cả các filter
            document.getElementById('monthFilter').style.display = 'none';
            document.getElementById('dateRangeFilter').style.display = 'none';

            // Hiển thị filter tương ứng
            if (filterType === 'month') {
                document.getElementById('monthFilter').style.display = 'block';
            } else if (filterType === 'date_range') {
                document.getElementById('dateRangeFilter').style.display = 'block';
            }
        }

        // Tự động submit form khi thay đổi month (chỉ khi filter type là month)
        document.getElementById('month').addEventListener('change', function() {
            if (document.getElementById('filter_type').value === 'month') {
                document.getElementById('chartFilterForm').submit();
            }
        });

        // Validation cho date range
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = this.value;
            const endDateInput = document.getElementById('end_date');

            if (startDate) {
                endDateInput.min = startDate;
                if (endDateInput.value && endDateInput.value < startDate) {
                    endDateInput.value = startDate;
                }
            }
        });

        document.getElementById('end_date').addEventListener('change', function() {
            const endDate = this.value;
            const startDateInput = document.getElementById('start_date');

            if (endDate) {
                startDateInput.max = endDate;
                if (startDateInput.value && startDateInput.value > endDate) {
                    startDateInput.value = endDate;
                }
            }
        });
    </script>
@endsection

@section('css')
    <style>
        .nav-pills .nav-link {
            border-radius: 0.375rem;
            color: #6c757d;
        }

        .nav-pills .nav-link.active {
            background-color: #007bff;
            color: white;
        }

        /* Styling cho form filter */
        .form-label.small {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6c757d;
        }

        .form-select-sm,
        .form-control-sm {
            font-size: 0.875rem;
        }

        /* Responsive cho filter form */
        @media (max-width: 768px) {
            #dateRangeFilter .row {
                flex-direction: column;
            }

            #dateRangeFilter .col-auto {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        /* Animation cho filter toggle */
        #monthFilter,
        #dateRangeFilter {
            transition: all 0.3s ease;
        }
    </style>
@endsection
