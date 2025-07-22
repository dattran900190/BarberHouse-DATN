<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title-page')</title>

    @include('layouts.blocks.includes.link-head')

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="site-wrapper">
        @include('layouts.blocks.header')

        @yield('slider')

        <div class="site-content">
            @yield('content')
        </div>

        @include('layouts.blocks.footer')

        <!-- Chat Button -->
        <div class="chat-wrapper">
            <button id="chatToggle" class="chat-button">
                💬
            </button>
            <div class="chat-box" id="chatBox">
                <div class="chat-header">
                    <span>Hỗ trợ khách hàng</span>
                    <button id="chatClose">×</button>
                </div>
                <div class="chat-body">
                    <p>Xin chào! Tôi có thể giúp gì cho bạn?</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Thêm jQuery trước toastr -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @include('layouts.blocks.includes.link-foot')

    <script>
        // Cấu hình toastr
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo Pusher
            const pusher = new Pusher('124e770f1adf07681023', {
                cluster: 'ap1',
                encrypted: true
            });

            // Subscribe vào channel 'appointments'
            const channel = pusher.subscribe('appointments');

            // Lắng nghe event 'AppointmentStatusUpdated'
            channel.bind('AppointmentStatusUpdated', function(data) {
                // Hiển thị thông báo bằng toastr
                toastr.success(data.message);

                // Cập nhật số lượng thông báo trên chuông
                const badge = document.querySelector('#notification-count');
                let currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = currentCount + 1;
            });

            // Lắng nghe event 'NewAppointment'
            channel.bind('NewAppointment', function(data) {
                // Hiển thị thông báo bằng toastr
                toastr.success(data.message);

                // Cập nhật số lượng thông báo trên chuông
                const badge = document.querySelector('#notification-count');
                let currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = currentCount + 1;
            });

            // Hàm reset số lượng thông báo
            function resetNotificationCount() {
                const badge = document.querySelector('#notification-count');
                badge.textContent = '0';
            }
        });
    </script>
</body>
</html>