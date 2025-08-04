<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title-page')</title>

    @include('layouts.blocks.includes.link-head')


    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto&display=swap" rel="stylesheet">
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
            0% {
                transform: rotate(0);
            }

            25% {
                transform: rotate(15deg);
            }

            50% {
                transform: rotate(-15deg);
            }

            75% {
                transform: rotate(10deg);
            }

            100% {
                transform: rotate(0);
            }
        }

        .bell-shake {
            animation: bellShake 0.5s ease-in-out;
        }

        .toast {
            border-radius: 8px !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
        }

        .toast-success {
            background-color: #28a745 !important;
        }

        .toast-error {
            background-color: #dc3545 !important;
        }

        .toast-info {
            background-color: #17a2b8 !important;
        }

        .toast-warning {
            background-color: #ffc107 !important;
        }

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
        <!-- Nút mở chat -->
        <button id="chatToggle" class="chat-button">
            <i class="fa-solid fa-message"></i>
        </button>

        <!-- Hộp chat -->
        <div class="chat-wrapper">
            <div class="chat-header">
                <i class="fa-solid fa-robot"></i> Barber House
                <span id="chatClose">&times;</span>
            </div>
            <div class="chat-body" id="chatMessages">
                <div class="message-ai">
                    <div class="bubble">
                        <b>BarberHouse:</b> Xin chào! Tôi có thể giúp gì cho bạn?
                    </div>
                </div>
            </div>
            <div class="chat-footer">
                <input id="chatInput" type="text" placeholder="Nhập tin nhắn..." />
                <button id="sendMessage"><i class="fa-solid fa-paper-plane"></i></button>
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

        <style>
            .chat-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background: #000;
                color: #fff;
                border-radius: 50%;
                width: 60px;
                height: 60px;
                border: none;
                font-size: 24px;
                cursor: pointer;
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.3s;
            }

            .chat-button:hover {
                background: #444;
            }

            .chat-wrapper {
                position: fixed;
                bottom: 100px;
                right: 20px;
                width: 350px;
                display: none;
                flex-direction: column;
                border-radius: 10px;
                background: #fff;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 9998;
                overflow: hidden;
                font-family: 'Roboto', sans-serif;
            }

            .chat-wrapper.open {
                display: flex;
            }

            .chat-header {
                background: #000;
                color: #fff;
                padding: 12px;
                font-size: 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            #chatClose {
                cursor: pointer;
                font-size: 20px;
            }

            .chat-body {
                height: 300px;
                overflow-y: auto;
                padding: 10px;
                background: #f5f5f5;
            }

            .message-user,
            .message-ai {
                display: flex;
                margin-bottom: 10px;
            }

            .message-user {
                justify-content: flex-end;
            }

            .message-ai {
                justify-content: flex-start;
            }

            .bubble {
                padding: 10px 14px;
                border-radius: 20px;
                max-width: 75%;
                word-wrap: break-word;
            }

            .message-user .bubble {
                background: #000;
                color: #fff;
                border-bottom-right-radius: 0;
            }

            .message-ai .bubble {
                background: #e0e0e0;
                color: #000;
                border-bottom-left-radius: 0;
            }

            .chat-footer {
                display: flex;
                padding: 10px;
                background: #fff;
                border-top: 1px solid #ddd;
            }

            .chat-footer input {
                flex: 1;
                border-radius: 20px;
                border: 1px solid #ccc;
                padding: 10px 14px;
                outline: none;
            }

            .chat-footer button {
                background: #000;
                color: #fff;
                border: none;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                margin-left: 8px;
                cursor: pointer;
            }

            .chat-footer button:hover {
                background: #444;
            }

            /* Hiệu ứng gõ chữ */
            .typing {
                border-right: .1em solid #000;
                animation: blink-caret .75s step-end infinite;
            }

            @keyframes blink-caret {
                50% {
                    border-color: transparent;
                }
            }
        </style>
        <script>
            let pusherInstance = null;

            document.addEventListener('DOMContentLoaded', function() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    timeOut: 7000,
                    extendedTimeOut: 4000,
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
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
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
                        notifications.forEach((notification, index) => {
                            const notificationItem = document.createElement('div');
                            notificationItem.className = 'notification-item';

                            // Tạo link phù hợp dựa trên loại thông báo
                            let link = '#';
                            if (notification.appointment_id) {
                                link = `/lich-su-dat-lich/${notification.appointment_id}`;
                            } else if (notification.order_id) {
                                link = `/chi-tiet-don-hang/${notification.order_id}`;
                            }

                            notificationItem.innerHTML = `
                        <i class="fas fa-bell"></i>
                        <div>
                            <a href="${link}" style="color: #000" data-index="${index}"><p>${notification.message}</p></a>
                            <span class="time">${notification.time}</span>
                        </div>
                    `;
                            notificationList.appendChild(notificationItem);
                        });

                        // Thêm sự kiện nhấp chuột cho các liên kết thông báo
                        const notificationLinks = notificationList.querySelectorAll('a[data-index]');
                        notificationLinks.forEach(link => {
                            link.addEventListener('click', function() {
                                const index = parseInt(this.getAttribute('data-index'));
                                removeNotification(index);
                            });
                        });
                    }
                }

                function addNotification(message, appointment_id, order_id = null) {
                    const time = new Date().toLocaleString('vi-VN');
                    notifications.unshift({
                        message,
                        time,
                        appointment_id,
                        order_id
                    });
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

                function removeNotification(index) {
                    notifications.splice(index, 1); // Xóa thông báo tại chỉ số index
                    localStorage.setItem('notifications_' + userId, JSON.stringify(notifications));
                    displayNotifications();

                    const badge = document.querySelector('#notification-count');
                    if (badge) {
                        badge.textContent = notifications.length;
                    }
                }

                channel.bind('AppointmentStatusUpdated', function(data) {
                    toastr.success(data.message);
                    addNotification(data.message, data.appointment_id);
                });

                channel.bind('OrderStatusUpdated', function(data) {
                    console.log('OrderStatusUpdated event received:', data);
                    toastr.success(data.message);
                    addNotification(data.message, null, data.order_id);
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

            document.addEventListener('DOMContentLoaded', function() {
                const chatWrapper = document.querySelector('.chat-wrapper');
                const chatToggle = document.querySelector('#chatToggle');
                const chatClose = document.querySelector('#chatClose');
                const input = document.querySelector('#chatInput');
                const sendButton = document.querySelector('#sendMessage');
                const chatMessages = document.querySelector('#chatMessages');

                function toggleChat(open = null) {
                    if (open === true) chatWrapper.classList.add('open');
                    else if (open === false) chatWrapper.classList.remove('open');
                    else chatWrapper.classList.toggle('open');
                }

                chatToggle.addEventListener('click', () => toggleChat(true));
                chatClose.addEventListener('click', () => toggleChat(false));

                sendButton.addEventListener('click', sendMessage);
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') sendMessage();
                });

                // Hàm loại bỏ markdown như #, *, dấu thừa
                function cleanAIResponse(text) {
                    return text
                        .replace(/[#*]+/g, '') // bỏ dấu #, *
                        .replace(/\s{2,}/g, ' ') // bỏ khoảng trắng thừa
                        .trim();
                }


                function appendMessage(content, type) {
                    const div = document.createElement('div');
                    div.className = type === 'user' ? 'message-user' : 'message-ai';
                    div.innerHTML = `<div class="bubble">${content}</div>`;
                    chatMessages.appendChild(div);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    return div.querySelector('.bubble');
                }

                async function sendMessage() {
                    const msg = input.value.trim();
                    if (!msg) return;
                    input.value = '';

                    appendMessage(`<b>Bạn:</b> ${msg}`, 'user');

                    const bubble = appendMessage(`<b>BarberHouse:</b> ...`, 'BarberHouse');

                    try {
                        const response = await fetch('/chat-ai', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                message: msg
                            })
                        });

                        const data = await response.json();

                        // **Làm sạch markdown (#, *, ...)**
                        const cleanReply = cleanAIResponse(data.reply);

                        // **Đưa text đã sạch vào hiển thị**
                        typeText(bubble, `<b>BarberHouse:</b> ${cleanReply}`);
                    } catch (err) {
                        bubble.innerHTML = `<b>BarberHouse:</b> <span style="color:red">Không thể kết nối</span>`;
                    }
                }


                function typeText(element, text, speed = 30) {
                    element.innerHTML = '';
                    let index = 0;
                    const interval = setInterval(() => {
                        element.innerHTML = text.slice(0, index) + '<span class="typing"></span>';
                        index++;
                        if (index > text.length) {
                            clearInterval(interval);
                            element.innerHTML = text;
                        }
                    }, speed);
                }
            });
        </script>
</body>

</html>
