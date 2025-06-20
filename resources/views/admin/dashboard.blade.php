@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard quản lý salon</h1>
@stop

@section('content')
    {{-- <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;">
        <div id="appointmentToast" class="toast" role="alert" data-bs-delay="5000">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Thông báo lịch hẹn</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close">Xem</button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div> --}}
     <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;" id="toastContainer">
        <!-- Toast mẫu (sẽ được clone động) -->
        <div id="appointmentToastTemplate" class="toast" role="alert" data-bs-delay="180000" style="display: none;">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Thông báo lịch hẹn</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <p id="toastMessage"></p>
                <a id="toastDetailLink" href="#" class="btn btn-sm btn-primary mt-2">Xem chi tiết</a>
            </div>
        </div>
    </div>
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
    <!-- Trong dashboard.blade.php, thêm vào -->
    {{-- <div id="notification" class="alert alert-success" style="display: none;">
        <span id="notification-message"></span>
    </div> --}}
    {{-- <script>
        Echo.channel('appointments')
            .listen('NewAppointment', (event) => {
                alert(event.message); // Hiển thị thông báo đơn giản
                // Bạn có thể thay bằng cách cập nhật giao diện, ví dụ: thêm vào danh sách lịch hẹn
            });
    </script> --}}

    <!-- Container cho Bootstrap Toast -->
    {{-- <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;">
        <div id="appointmentToast" class="toast" role="alert" data-bs-delay="5000">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Thông báo lịch hẹn</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    <!-- Existing content của bạn -->
    <div class="row">
        <!-- Box 1, Box 2, v.v. -->
    </div> --}}
@stop

@vite('resources/js/app.js')
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script>
        // Hàm khởi tạo Echo listener
        function initEchoListener() {
            if (typeof Echo !== 'undefined' && Echo !== null) {
                console.log('Echo is defined:', Echo);
                Echo.channel('appointments')
                    .listen('NewAppointment', (event) => {
                        console.log('New appointment received:', event);
                        alert('Có lịch hẹn mới: ' + event.message);
                    });
            } else {
                console.error('Echo is not defined or null, retrying...');
                setTimeout(initEchoListener, 500); // Thử lại sau 500ms nếu Echo chưa sẵn sàng
            }
        }

        // Chạy sau khi DOM loaded
        document.addEventListener('DOMContentLoaded', initEchoListener);
    </script> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script>
        function initEchoListener() {
            if (typeof Echo !== 'undefined' && Echo !== null) {
                console.log('Echo is defined:', Echo);
                Echo.channel('appointments')
                    .listen('NewAppointment', (event) => {
                        console.log('New appointment received:', event);
                        try {
                            // Tạo Toast mới
                            const toastContainer = document.getElementById('toastContainer');
                            const toastTemplate = document.getElementById('appointmentToastTemplate');
                            const newToast = toastTemplate.cloneNode(true);
                            newToast.id = 'appointmentToast-' + Date.now();
                            newToast.style.display = 'block';

                            // Cập nhật nội dung
                            const toastMessage = newToast.querySelector('#toastMessage');
                            const toastDetailLink = newToast.querySelector('#toastDetailLink');
                            if (!toastMessage || !toastDetailLink) {
                                console.error('Toast elements not found');
                                return;
                            }
                            toastMessage.textContent = event?.message || 'Không có thông tin chi tiết';
                            toastDetailLink.href = `/admin/appointments/${event?.appointment_id || ''}`;

                            // Thêm Toast vào container
                            toastContainer.appendChild(newToast);

                            // Khởi tạo và hiển thị Toast
                            const toast = new bootstrap.Toast(newToast, {
                                delay: 180000 // 3 phút
                            });
                            toast.show();
                            console.log('Toast shown successfully');

                            // Xóa Toast khỏi DOM sau khi ẩn
                            newToast.addEventListener('hidden.bs.toast', () => {
                                newToast.remove();
                            });
                        } catch (error) {
                            console.error('Error showing toast:', error);
                        }
                    })
                    .error((error) => {
                        console.error('Echo channel error:', error);
                    });
            } else {
                console.error('Echo is not defined or null, retrying...');
                setTimeout(initEchoListener, 200);
            }
        }
        document.addEventListener('DOMContentLoaded', initEchoListener);
    </script> --}}
    <script>
        function initEchoListener() {
            if (typeof Echo !== 'undefined' && Echo !== null) {
                console.log('Echo is defined:', Echo);
                const channel = Echo.channel('appointments');
                channel.subscribed(() => {
                    console.log('Subscribed to appointments channel');
                })
                .listen('NewAppointment', (event) => {
                    console.log('New appointment received (NewAppointment):', event);
                    showToast(event);
                })
                .listen('.NewAppointment', (event) => {
                    console.log('New appointment received (.NewAppointment):', event);
                    showToast(event);
                })
                .listen('App\\Events\\NewAppointment', (event) => {
                    console.log('New appointment received (App\\Events\\NewAppointment):', event);
                    showToast(event);
                })
                .listen('.App\\Events\\NewAppointment', (event) => {
                    console.log('New appointment received (.App\\Events\\NewAppointment):', event);
                    showToast(event);
                })
                .error((error) => {
                    console.error('Echo channel error:', error);
                });
            } else {
                console.error('Echo is not defined or null, retrying...');
                setTimeout(initEchoListener, 200);
            }
        }

        function showToast(event) {
            try {
                const toastContainer = document.getElementById('toastContainer');
                const toastTemplate = document.getElementById('appointmentToastTemplate');
                if (!toastContainer || !toastTemplate) {
                    console.error('Toast container or template not found');
                    return;
                }
                const newToast = toastTemplate.cloneNode(true);
                newToast.id = 'appointmentToast-' + Date.now();
                newToast.style.display = 'block';

                const toastMessage = newToast.querySelector('#toastMessage');
                const toastDetailLink = newToast.querySelector('#toastDetailLink');
                if (!toastMessage || !toastDetailLink) {
                    console.error('Toast elements not found');
                    return;
                }
                toastMessage.textContent = event?.message || 'Không có thông tin chi tiết';
                toastDetailLink.href = `/admin/appointments/${event?.appointment_id || ''}`;

                toastContainer.appendChild(newToast);

                const toast = new bootstrap.Toast(newToast, {
                    delay: 180000 // 3 phút
                });
                toast.show();
                console.log('Toast shown successfully');

                newToast.addEventListener('hidden.bs.toast', () => {
                    newToast.remove();
                });
            } catch (error) {
                console.error('Error showing toast:', error);
            }
        }

        document.addEventListener('DOMContentLoaded', initEchoListener);
    </script>

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

@section('css')
    <style>
        .toast {
            min-width: 300px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            margin-top: 10px; /* Khoảng cách giữa các Toast */
        }
        .toast-header {
            font-size: 14px;
            padding: 8px 12px;
        }
        .toast-body {
            font-size: 13px;
            padding: 12px;
        }
        .btn-close {
            background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707A1 1 0 01.293.293z'/%3e%3c/svg%3e") center/1em auto no-repeat;
            width: 1em;
            height: 1em;
            opacity: 0.8;
            border: none;
            padding: 0;
            margin-left: 8px;
        }
        .btn-close:hover {
            opacity: 1;
        }
    </style>
@endsection
