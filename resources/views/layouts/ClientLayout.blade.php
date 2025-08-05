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
            height: 50px !important;
            padding: 10px 14px !important;
            border-radius: 5px !important;
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


        <!-- Nút mở chat mới -->
        <button id="newChatToggle" class="new-chat-button" style="display: none;">
            <i class="fa-solid fa-headset"></i>
        </button>

        <!-- Hộp chat mới -->
        <div class="new-chat-container" id="newChatContainer">
            <div class="new-chat-header">
                <div class="new-chat-title">
                    <i class="fa-solid fa-headset"></i>
                    <span>Hỗ trợ khách hàng</span>
                </div>
                <button id="newChatClose" class="new-chat-close">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="new-chat-messages" id="newChatMessages">
                <div class="new-message new-message-ai">
                    <div class="new-bubble">
                        <strong>BarberHouse:</strong> Xin chào! Tôi có thể giúp gì cho bạn?
                    </div>
                </div>
            </div>
            <div class="new-chat-input-area">
                <input id="newChatInput" type="text" placeholder="Nhập tin nhắn..." />
                <button id="newSendMessage" class="new-send-btn">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
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
            /* Chat button mới */
            .new-chat-button {
                position: fixed !important;
                bottom: 20px !important;
                right: 20px !important;
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
                color: #fff !important;
                border-radius: 50% !important;
                width: 65px !important;
                height: 65px !important;
                border: none !important;
                font-size: 24px !important;
                cursor: pointer !important;
                z-index: 9999 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4) !important;
            }

            .new-chat-button:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(0, 123, 255, 0.6);
            }

            /* Chat container mới */
            .new-chat-container {
                position: fixed !important;
                bottom: 100px !important;
                right: 20px !important;
                width: 380px !important;
                height: 500px !important;
                display: none !important;
                flex-direction: column !important;
                border-radius: 15px !important;
                background: #fff !important;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2) !important;
                z-index: 9998 !important;
                overflow: hidden !important;
                font-family: 'Roboto', sans-serif !important;
                opacity: 0 !important;
                transform: translateY(30px) scale(0.9) !important;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }

            .new-chat-container.open {
                display: flex !important;
                opacity: 1 !important;
                transform: translateY(0) scale(1) !important;
            }

            /* Header mới */
            .new-chat-header {
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                color: #fff;
                padding: 15px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-radius: 15px 15px 0 0;
            }

            .new-chat-title {
                display: flex;
                align-items: center;
                gap: 10px;
                font-weight: 600;
                font-size: 16px;
            }

            .new-chat-close {
                background: rgba(255, 255, 255, 0.2);
                color: #fff;
                border: none;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.3s;
            }

            .new-chat-close:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            /* Messages area mới */
            .new-chat-messages {
                height: 350px;
                overflow-y: auto;
                padding: 20px 15px;
                background: #f8f9fa;
                flex: 1;
            }

            .new-message {
                display: flex;
                margin-bottom: 20px;
                align-items: flex-end;
            }

            .new-message-user {
                justify-content: flex-end;
                margin-left: 60px;
            }

            .new-message-ai {
                justify-content: flex-start;
                margin-right: 60px;
            }

            .new-bubble {
                padding: 12px 16px;
                border-radius: 18px;
                max-width: 70%;
                word-wrap: break-word;
                font-size: 14px;
                line-height: 1.5;
                position: relative;
            }

            .new-message-user .new-bubble {
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                color: #fff;
                border-bottom-right-radius: 5px;
                margin-left: auto;
            }

            .new-message-ai .new-bubble {
                background: #fff;
                color: #333;
                border: 1px solid #e0e0e0;
                border-bottom-left-radius: 5px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                margin-right: auto;
            }

            /* Input area mới */
            .new-chat-input-area {
                display: flex;
                padding: 15px 20px;
                background: #fff;
                border-top: 1px solid #e0e0e0;
                gap: 10px;
            }

            .new-chat-input-area input {
                flex: 1;
                border-radius: 25px;
                border: 2px solid #e0e0e0;
                padding: 12px 18px;
                outline: none;
                font-size: 14px;
                transition: border-color 0.3s;
            }

            .new-chat-input-area input:focus {
                border-color: #007bff;
            }

            .new-send-btn {
                background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
                color: #fff;
                border: none;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
            }

            .new-send-btn:hover {
                transform: scale(1.1);
            }

            /* Typing indicator mới */
            .new-typing-indicator {
                display: inline-block;
                width: 40px;
                text-align: left;
            }

            .new-typing-indicator span {
                display: inline-block;
                width: 6px;
                height: 6px;
                margin-right: 3px;
                background: #007bff;
                border-radius: 50%;
                opacity: 0.6;
                animation: new-blink 1.4s infinite ease-in-out;
            }

            .new-typing-indicator span:nth-child(2) {
                animation-delay: 0.2s;
            }

            .new-typing-indicator span:nth-child(3) {
                animation-delay: 0.4s;
            }

            @keyframes new-blink {
                0%, 80%, 100% {
                    opacity: 0.6;
                    transform: scale(1);
                }
                40% {
                    opacity: 1;
                    transform: scale(1.2);
                }
            }

            /* Scrollbar cho messages */
            .new-chat-messages::-webkit-scrollbar {
                width: 6px;
            }

            .new-chat-messages::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 3px;
            }

            .new-chat-messages::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 3px;
            }

        </style>
            .new-chat-messages::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
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
                const newChatContainer = document.querySelector('#newChatContainer');
                const newChatToggle = document.querySelector('#newChatToggle');
                const newChatClose = document.querySelector('#newChatClose');
                const newChatInput = document.querySelector('#newChatInput');
                const newSendMessage = document.querySelector('#newSendMessage');
                const newChatMessages = document.querySelector('#newChatMessages');

                // Đảm bảo chat không hiện khi load trang và CSS đã load
                newChatContainer.classList.remove('open');
                newChatContainer.style.display = 'none';

                // Đảm bảo button có style đúng và hiển thị sau khi CSS load
                if (newChatToggle) {
                    setTimeout(() => {
                        newChatToggle.style.display = 'flex';
                        newChatToggle.style.alignItems = 'center';
                        newChatToggle.style.justifyContent = 'center';
                    }, 100);
                }

                function toggleNewChat(open = null) {
                    if (open === true) {
                        newChatContainer.style.display = 'flex';
                        // Delay để animation hoạt động
                        setTimeout(() => {
                            newChatContainer.classList.add('open');
                        }, 10);
                    } else if (open === false) {
                        newChatContainer.classList.remove('open');
                        // Delay để animation hoàn thành trước khi ẩn
                        setTimeout(() => {
                            newChatContainer.style.display = 'none';
                        }, 400);
                    } else {
                        if (newChatContainer.classList.contains('open')) {
                            toggleNewChat(false);
                        } else {
                            toggleNewChat(true);
                        }
                    }
                }

                newChatToggle.addEventListener('click', () => toggleNewChat(true));
                newChatClose.addEventListener('click', () => toggleNewChat(false));

                newSendMessage.addEventListener('click', sendNewMessage);
                newChatInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') sendNewMessage();
                });

                // Hàm loại bỏ markdown như #, *, dấu thừa
                function cleanAIResponse(text) {
                    return text
                        .replace(/[#*]+/g, '') // bỏ dấu #, *
                        .replace(/\s{2,}/g, ' ') // bỏ khoảng trắng thừa
                        .trim();
                }

                function appendNewMessage(content, type) {
                    const div = document.createElement('div');
                    div.className = type === 'user' ? 'new-message new-message-user' : 'new-message new-message-ai';

                    // Thêm avatar cho AI message
                    if (type === 'ai') {
                        div.innerHTML = `
                            <div class="new-bubble">${content}</div>
                        `;
                    } else {
                        div.innerHTML = `<div class="new-bubble">${content}</div>`;
                    }

                    newChatMessages.appendChild(div);
                    newChatMessages.scrollTop = newChatMessages.scrollHeight;
                    return div.querySelector('.new-bubble');
                }

                async function sendNewMessage() {
                    const msg = newChatInput.value.trim();
                    if (!msg) return;
                    newChatInput.value = '';

                    appendNewMessage(`<strong>Bạn:</strong> ${msg}`, 'user');

                    const bubble = appendNewMessage(
                        `<strong>BarberHouse:</strong> <span class="new-typing-indicator">
                            <span></span><span></span><span></span>
                        </span>`,
                        'ai'
                    );

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

                        // Làm sạch markdown (#, *, ...)
                        const cleanReply = cleanAIResponse(data.reply);

                        // Đưa text đã sạch vào hiển thị
                        typeNewText(bubble, `<strong>BarberHouse:</strong> ${cleanReply}`);
                    } catch (err) {
                        bubble.innerHTML = `<strong>BarberHouse:</strong> <span style="color:red">Không thể kết nối</span>`;
                    }
                }

                function typeNewText(element, text, speed = 30) {
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
