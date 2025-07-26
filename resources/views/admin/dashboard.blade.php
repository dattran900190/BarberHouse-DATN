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

    <!-- Chart Section -->
    <div class="row align-items-stretch">
        <!-- Biểu đồ theo tuần/khoảng ngày -->
        <div class="col-md-6 mb-3">
            <div class="card card-round h-100 d-flex flex-column">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Thống kê doanh thu theo tuần</div>
                        <div class="card-tools">
                            <form method="GET" id="weekChartFilterForm" class="mb-0">
                                <div class="row g-2 align-items-end">
                                    <div class="col-auto">
                                        <label for="week_start" class="form-label small mb-1">Từ ngày:</label>
                                        <input type="date" name="week_start" id="week_start"
                                            class="form-control form-control-sm" value="{{ $weekStart ?? '' }}"
                                            max="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-auto">
                                        <label for="week_end" class="form-label small mb-1">Đến ngày:</label>
                                        <input type="date" name="week_end" id="week_end"
                                            class="form-control form-control-sm" value="{{ $weekEnd ?? '' }}"
                                            max="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-filter me-1"></i>Lọc
                                        </button>
                                        @if (request()->anyFilled(['week_start', 'week_end']))
                                            <a href="{{ route('dashboard') }}"
                                                class="btn btn-outline-secondary btn-sm ms-1">
                                                <i class="fa fa-times me-1"></i>Xóa
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <small class="text-muted">Để trống để xem tuần hiện tại</small>
                            </form>
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
        <div class="col-md-6 mb-3">
            <div class="card card-round h-100 d-flex flex-column">
                <div class="card-header">
                    <div class="card-head-row">
                        <div class="card-title">Thống kê doanh thu theo tháng</div>
                        <div class="card-tools">
                            <form method="GET" id="monthChartFilterForm" class="mb-0">
                                <div class="row g-2 align-items-end">
                                    <div class="col-auto">
                                        <label for="selected_month" class="form-label small mb-1">Chọn tháng:</label>
                                        <select name="selected_month" id="selected_month"
                                            class="form-select form-select-sm">
                                            <option value="">Tất cả tháng {{ $year ?? date('Y') }}</option>
                                            @foreach ($availableMonths as $monthNum)
                                                <option value="{{ $monthNum }}"
                                                    {{ isset($selectedMonth) && $selectedMonth == $monthNum ? 'selected' : '' }}>
                                                    Tháng {{ $monthNum }}/{{ $year ?? date('Y') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa fa-filter me-1"></i>Lọc
                                        </button>
                                        @if (request()->anyFilled(['selected_month']))
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
                    <div class="chart-container" style="min-height: 300px;">
                        <canvas id="monthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row align-items-stretch">
        <!-- Barber Performance -->
        <div class="col-md-12 mb-3">

        </div>
    </div> --}}

    <!-- Bottom Row -->
    <div class="row g-3">
        {{-- Cột bên trái --}}
        <div class="col-md-6 d-flex flex-column gap-3">
            {{-- Lịch hẹn sắp tới --}}
            <div class="card card-round flex-fill">
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

            {{-- Hiệu suất nhân viên --}}
            <div class="card card-round flex-fill">
                <div class="card-header d-flex flex-column">
                    <h5 class="card-title mb-1 fw-bold">Hiệu suất nhân viên (tuần này)</h5>
                    <span class="text-muted small">{{ $weekRange ?? 'Tuần hiện tại' }}</span>
                </div>
                <div class="card-body pb-0">
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

        {{-- Cột bên phải --}}
        <div class="col-md-6">
            <div class="card card-round h-100 d-flex flex-column">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">Sản phẩm bán chạy & ít bán</h5>
                    <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pills-top-tab" data-bs-toggle="pill" href="#pills-top"
                                role="tab">Bán chạy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="pills-low-tab" data-bs-toggle="pill" href="#pills-low"
                                role="tab">Ít bán</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">
                        {{-- Bán chạy --}}
                        <div class="tab-pane fade show active" id="pills-top" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @forelse ($topProducts as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->productVariant->product->name ?? 'Không xác định' }}</strong><br>
                                            <small
                                                class="text-muted">{{ $item->productVariant->name ?? 'Mặc định' }}</small>
                                        </div>
                                        <span class="badge bg-success rounded-pill">
                                            {{ $item->total_sold }} sp
                                        </span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted">Không có sản phẩm nào được bán</li>
                                @endforelse
                            </ul>
                        </div>

                        {{-- Ít bán --}}
                        <div class="tab-pane fade" id="pills-low" role="tabpanel">
                            <ul class="list-group list-group-flush">
                                @forelse ($lowSellingProducts as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $item->product->name ?? 'Không xác định' }}</strong><br>
                                            <small class="text-muted">Variant: {{ $item->name ?? 'Mặc định' }}</small>
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
    </div>


    <!-- Transaction History -->
    <div class="row mt-3">
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
                                @php
                                    $statusLabels = [
                                        'pending' => 'Đang chờ',
                                        'processing' => 'Đang xử lý',
                                        'shipping' => 'Đang giao hàng',
                                        'completed' => 'Hoàn tất',
                                        'cancelled' => 'Đã hủy',
                                    ];
                                @endphp

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
                                            <span
                                                class="badge 
    {{ $order->status == 'completed'
        ? 'bg-success'
        : ($order->status == 'pending'
            ? 'bg-warning'
            : ($order->status == 'cancelled'
                ? 'bg-danger'
                : 'bg-primary')) }}">
                                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                            </span>

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
        // Data cho biểu đồ tuần
        const weekLabels = @json($weekLabels);
        const weekServiceRevenue = @json($weekServiceRevenue);
        const weekProductRevenue = @json($weekProductRevenue);

        // Data cho biểu đồ tháng
        const monthLabels = @json($monthLabels);
        const monthServiceRevenue = @json($monthServiceRevenue);
        const monthProductRevenue = @json($monthProductRevenue);

        // Biểu đồ tuần
        const weekCtx = document.getElementById('weekChart').getContext('2d');
        const weekChart = new Chart(weekCtx, {
            type: 'line',
            data: {
                labels: weekLabels,
                datasets: [{
                    label: 'Doanh thu dịch vụ',
                    data: weekServiceRevenue,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.4
                }, {
                    label: 'Doanh thu sản phẩm',
                    data: weekProductRevenue,
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
                                return context.dataset.label + ': ₫' + context.parsed.y.toLocaleString();
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
                            text: 'Doanh thu (₫)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₫' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Biểu đồ tháng
        const monthCtx = document.getElementById('monthChart').getContext('2d');
        const monthChart = new Chart(monthCtx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'Doanh thu dịch vụ',
                    data: monthServiceRevenue,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }, {
                    label: 'Doanh thu sản phẩm',
                    data: monthProductRevenue,
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
                                return context.dataset.label + ': ₫' + context.parsed.y.toLocaleString();
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
                            text: 'Doanh thu (₫)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₫' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
