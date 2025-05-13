@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard quản lý salon</h1>
@stop

@section('content')
    <div class="row">
        <!-- Box 1 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>24</h3>
                    <p>Dịch vụ hiện có</p>
                </div>
                <div class="icon">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <a href="{{ url('admin/services') }}" class="small-box-footer">
                    Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Box 2 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>15</h3>
                    <p>Thợ đang làm việc</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-scissors"></i>
                </div>
                <a href="{{ url('admin/barbers') }}" class="small-box-footer">
                    Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Box 3 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>32</h3>
                    <p>Lịch đặt hôm nay</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Xem lịch <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Box 4 -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>7.200.000đ</h3>
                    <p>Doanh thu hôm nay</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Xem báo cáo <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Doanh thu 7 ngày gần nhất</h3>
        </div>
        <div class="card-body">
            <canvas id="revenueChart" height="100"></canvas>
        </div>
    </div>

    <!-- Dịch vụ mới nhất -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Dịch vụ mới nhất</h3>
        </div>
        <div class="card-body table-responsive p-0" style="max-height: 300px;">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên dịch vụ</th>
                        <th>Giá</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Cắt tóc nam</td>
                        <td>80.000đ</td>
                        <td>2025-05-05</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Gội đầu + massage</td>
                        <td>100.000đ</td>
                        <td>2025-05-04</td>
                    </tr>
                    <!-- Thêm các dòng khác nếu muốn -->
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['30/4', '1/5', '2/5', '3/5', '4/5', '5/5'],
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: [500000, 800000, 1200000, 750000, 950000, 720000],
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + 'đ';
                            }
                        }
                    }
                }
            }
        });
    </script>
@stop
