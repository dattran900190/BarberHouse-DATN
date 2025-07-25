<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title-page')</title>

    @include('layouts.blocks.includes.link-head')

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        .page-link {
            color: #000;
            border-radius: 5px;
        }

        a {
            text-decoration: none;
        }

        .page-link:hover {
            background-color: #000;
            color: #fff
        }

        .page-item.active .page-link {
            background-color: #000;
            color: white;
            border: 1px solid #000;
        }

        .active .page-link {
            background-color: #000;
            color: white;
            border: 1px solid #000;
        }

        body.client-page .toast-custom-position {
            position: fixed !important;
            top: 15% !important;
            right: 10px !important;
            z-index: 1099 !important;
        }

        .notification-dropdown {
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #fff;
            z-index: 9999 !important;
        }

        .notification-list {
            padding: 10px;
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        .notification-item i {
            margin-right: 10px;
            color: #007bff;
        }

        .notification-item p {
            margin: 0;
            font-size: 0.9rem;
        }

        .notification-item .time {
            font-size: 0.7rem;
            color: #6c757d;
        }

        @keyframes bellShake {
            0% { transform: rotate(0); }
            25% { transform: rotate(15deg); }
            50% { transform: rotate(-15deg); }
            75% { transform: rotate(10deg); }
            100% { transform: rotate(0); }
        }

        .bell-shake {
            animation: bellShake 0.5s ease-in-out;
        }

        .toast {
            border-radius: 8px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
        }

        .toast-success { background-color: #28a745 !important; }
        .toast-error { background-color: #dc3545 !important; }
        .toast-info { background-color: #17a2b8 !important; }
        .toast-warning { background-color: #ffc107 !important; }

        .select2-container--default .select2-selection--single {
            height: 54px !important;
            padding: 10px 14px !important;
            border-radius: 4px !important;
            background-color: #fff !important;
            font-size: 15px !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
        }

        .select2-container--default .select2-selection--single:focus,
        .select2-container--default .select2-selection--single.select2-selection--focus {
            background-color: #fff !important;
            box-shadow: 0 0 0 2px #000 !important;
            outline: none !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px !important;
            right: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
        }

        .select2-container--default .select2-results>.select2-results__options {
            max-height: 240px !important;
            font-size: 15px !important;
        }

        .select2-container--default .select2-results__option--highlighted {
            color: #fff !important;
        }

        .btn-icon-remove {
            background-color: transparent !important;
            border: 1px solid #ccc !important;
            color: #000 !important;
            border-radius: 6px !important;
            width: 36px !important;
            height: 36px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
            cursor: pointer !important;
        }

        .btn-icon-remove:hover {
            background-color: #000 !important;
            color: #fff !important;
        }
    </style>
</head>

<body class="client-page">
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>

    @include('layouts.blocks.includes.link-foot')

    <script>
    let pusherInstance = null;

    document.addEventListener('DOMContentLoaded', function() {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 5000,
            extendedTimeOut: 2000,
            tapToDismiss: true,
            debug: false,
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut',
            target: 'body',
            positionClass: 'toast-custom-position',
        };

        @auth
        if (!pusherInstance) {
            console.log('User đã đăng nhập, khởi tạo Pusher...');
            pusherInstance = new Pusher('124e770f1adf07681023', {
                cluster: 'ap1',
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                }
            });
        }

        const userId = '{{ auth()->id() }}';
        const channel = pusherInstance.subscribe('private-user.' + userId);
        console.log('Đã subscribe vào channel: private-user.' + userId);

        let notifications = JSON.parse(localStorage.getItem('notifications_' + userId)) || [];

        const badge = document.querySelector('#notification-count');
        if (badge) {
            badge.textContent = notifications.length;
        }

        function displayNotifications() {
            const notificationList = document.querySelector('#notification-list');
            if (!notificationList) return;
            
            if (notifications.length === 0) {
                notificationList.innerHTML = '<p class="text-center text-muted">Chưa có thông báo</p>';
            } else {
                notificationList.innerHTML = '';
                notifications.forEach(notification => {
                    const notificationItem = document.createElement('div');
                    notificationItem.className = 'notification-item';
                    notificationItem.innerHTML = `
                        <i class="fas fa-bell"></i>
                        <div>
                            <a href="/appointment/${notification.appointment_id || ''}"><p>${notification.message}</p></a>
                            <span class="time">${notification.time}</span>
                        </div>
                    `;
                    notificationList.appendChild(notificationItem);
                });
            }
        }

        function addNotification(message, appointment_id) {
            const time = new Date().toLocaleString('vi-VN');
            notifications.unshift({ message, time, appointment_id });
            if (notifications.length > 50) {
                notifications = notifications.slice(0, 50);
            }
            localStorage.setItem('notifications_' + userId, JSON.stringify(notifications));
            displayNotifications();

            const badge = document.querySelector('#notification-count');
            if (badge) {
                badge.textContent = notifications.length;
            }

            const bell = document.querySelector('#notification-bell i');
            if (bell) {
                bell.classList.add('bell-shake');
                setTimeout(() => bell.classList.remove('bell-shake'), 500);
            }
        }

        channel.bind('AppointmentStatusUpdated', function(data) {
            toastr.success(data.message);
            addNotification(data.message, data.appointment_id);
        });

        pusherInstance.connection.bind('error', function(err) {
            console.error('Pusher connection error:', err);
        });

        pusherInstance.connection.bind('connected', function() {
            console.log('Pusher connected successfully');
        });

        window.resetNotificationCount = function() {
            notifications = [];
            localStorage.setItem('notifications_' + userId, JSON.stringify(notifications));
            const badge = document.querySelector('#notification-count');
            if (badge) {
                badge.textContent = '0';
            }
            displayNotifications();
        };

        displayNotifications();

        @else
        console.log('User chưa đăng nhập, không khởi tạo Pusher');
        @endauth

        const notificationBell = document.querySelector('#notification-bell');
        if (notificationBell) {
            new bootstrap.Dropdown(notificationBell);
        }
    });

    window.addEventListener('unload', function() {
        if (pusherInstance) pusherInstance.disconnect();
    });
    </script>
</body>

</html>