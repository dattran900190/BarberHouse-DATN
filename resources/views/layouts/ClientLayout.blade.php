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

        /* Desktop Menu Styles */
        @media (min-width: 992px) {
            .mobile-menu-buttons {
                display: none !important;
            }
            
            .mobile-menu-overlay {
                display: none !important;
            }
        }
        
        /* Mobile Menu Styles */
        @media (max-width: 991.98px) {
            .desktop-menu {
                display: none !important;
            }
            
            .navbar-brand {
                margin: 0 !important;
            }
            
            .mobile-menu-buttons {
                display: flex !important;
                align-items: center;
                gap: 10px;
            }
            
            .mobile-nav-btn {
                background: none;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                color: #fff;
                font-size: 18px;
                cursor: pointer;
                border-radius: 8px;
                transition: all 0.3s ease;
                position: relative;
            }
            
            .mobile-nav-btn:hover {
                background: rgba(255, 255, 255, 0.1);
                color: #fff;
            }
            
            /* Mobile User Dropdown */
            .mobile-user-dropdown {
                position: absolute;
                top: 100%;
                right: 0;
                width: 280px;
                background: #fff;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
                z-index: 1000;
                display: none;
                opacity: 0;
                transform: translateY(-10px);
                transition: all 0.3s ease;
                margin-top: 10px;
            }
            
            .mobile-user-dropdown.show {
                display: block;
                opacity: 1;
                transform: translateY(0);
            }
            
            .mobile-user-dropdown::before {
                content: '';
                position: absolute;
                top: -8px;
                right: 20px;
                width: 0;
                height: 0;
                border-left: 8px solid transparent;
                border-right: 8px solid transparent;
                border-bottom: 8px solid #fff;
            }
            
            .mobile-user-header {
                display: flex;
                align-items: center;
                padding: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .mobile-user-avatar {
                width: 50px;
                height: 50px;
                background: #f8f9fa;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                font-size: 20px;
                color: #6c757d;
            }
            
            .mobile-user-info h6 {
                margin: 0;
                color: #333;
                font-weight: 600;
                font-size: 16px;
            }
            
            .mobile-user-info small {
                color: #6c757d;
                font-size: 12px;
            }
            
            .mobile-user-menu {
                padding: 10px 0;
            }
            
            .mobile-menu-item {
                display: flex;
                align-items: center;
                padding: 12px 20px;
                color: #333;
                text-decoration: none;
                transition: background 0.3s ease;
                font-size: 14px;
                border: none;
                background: none;
                width: 100%;
                text-align: left;
                cursor: pointer;
            }
            
            .mobile-menu-item:hover {
                background: #f8f9fa;
                color: #333;
            }
            
            .mobile-menu-item i {
                margin-right: 12px;
                width: 16px;
                text-align: center;
                color: #007bff;
                font-size: 14px;
            }
            
            .mobile-logout-btn {
                color: #dc3545 !important;
            }
            
            .mobile-logout-btn i {
                color: #dc3545 !important;
            }
            
                    .mobile-menu-divider {
            height: 1px;
            background: #eee;
            margin: 10px 0;
        }
        
        /* Desktop User Dropdown */
        .desktop-user-dropdown {
            min-width: 280px;
            padding: 0;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            margin-top: 10px;
        }
        
        .desktop-user-dropdown .dropdown-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 10px 10px 0 0;
        }
        
        .desktop-user-dropdown .user-info {
            display: flex;
            align-items: center;
            padding: 0;
            border: none;
            background: none;
        }
        
        .desktop-user-dropdown .user-avatar {
            width: 50px;
            height: 50px;
            background: #e9ecef;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
            color: #6c757d;
        }
        
        .desktop-user-dropdown .user-details h6 {
            margin: 0;
            color: #333;
            font-weight: 600;
            font-size: 16px;
        }
        
        .desktop-user-dropdown .user-details small {
            color: #6c757d;
            font-size: 12px;
        }
        
        .desktop-user-dropdown .dropdown-item {
            padding: 12px 20px;
            color: #333;
            font-size: 14px;
            transition: background 0.3s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .desktop-user-dropdown .dropdown-item:hover {
            background: #f8f9fa;
            color: #333;
        }
        
        .desktop-user-dropdown .dropdown-item i {
            color: #007bff;
            font-size: 14px;
        }
        
        .desktop-user-dropdown .logout-btn {
            color: #dc3545 !important;
        }
        
        .desktop-user-dropdown .logout-btn i {
            color: #dc3545 !important;
        }
        
        .desktop-user-dropdown .dropdown-divider {
            margin: 0;
            border-color: #eee;
        }
        
        /* Mobile Menu Overlay */
            .mobile-menu-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .mobile-menu-overlay.show {
                display: flex;
                opacity: 1;
            }
            
            .mobile-menu-sidebar {
                position: absolute;
                top: 0;
                left: 0;
                width: 280px;
                height: 100%;
                background: #fff;
                color: #333;
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            }
            
            .mobile-menu-overlay.show .mobile-menu-sidebar {
                transform: translateX(0);
            }
            
            .mobile-menu-header {
                display: flex;
                justify-content: flex-end;
                padding: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .mobile-menu-close {
                background: none;
                border: none;
                color: #333;
                font-size: 24px;
                cursor: pointer;
                padding: 5px;
                border-radius: 50%;
                transition: background 0.3s ease;
            }
            
            .mobile-menu-close:hover {
                background: #f5f5f5;
            }
            
            .mobile-search-section {
                padding: 20px;
                border-bottom: 1px solid #eee;
            }
            
            .search-container {
                position: relative;
                display: flex;
                align-items: center;
            }
            
            .mobile-search-input {
                width: 100%;
                padding: 12px 45px 12px 15px;
                border: 1px solid #ddd;
                border-radius: 25px;
                font-size: 14px;
                outline: none;
                transition: border-color 0.3s ease;
            }
            
            .mobile-search-input:focus {
                border-color: #000;
            }
            
            .mobile-search-btn {
                position: absolute;
                right: 5px;
                top: 50%;
                transform: translateY(-50%);
                background: #000;
                color: #fff;
                border: none;
                border-radius: 50%;
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.3s ease;
            }
            
            .mobile-search-btn:hover {
                background: #333;
            }
            
            .mobile-menu-body {
                padding: 20px;
            }
            
            .mobile-nav-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .mobile-nav-item {
                margin-bottom: 5px;
            }
            
            .mobile-nav-link {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 15px 0;
                color: #333;
                text-decoration: none;
                font-weight: 500;
                font-size: 16px;
                border-bottom: 1px solid #f5f5f5;
                transition: color 0.3s ease;
            }
            
            .mobile-nav-link:hover {
                color: #000;
            }
            
            .mobile-nav-link.active {
                color: #000;
                font-weight: 600;
            }
            
            .new-badge {
                background: #dc3545;
                color: #fff;
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                font-weight: bold;
                margin-right: 10px;
            }
            
            .mobile-nav-link i {
                color: #999;
                font-size: 14px;
            }
            
            .mobile-cart-badge {
                background: #dc3545;
                color: #fff;
                padding: 2px 6px;
                border-radius: 10px;
                font-size: 10px;
                font-weight: bold;
                margin-left: 5px;
            }
            
            .mobile-user-section {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }
            
            .mobile-auth-buttons {
                display: flex;
                gap: 10px;
                margin-bottom: 20px;
            }
            
            .mobile-user-info {
                display: flex;
                align-items: center;
                margin-bottom: 20px;
                padding: 15px;
                background: #f8f9fa;
                border-radius: 10px;
            }
            
            .user-avatar {
                width: 50px;
                height: 50px;
                background: #e9ecef;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-right: 15px;
                font-size: 20px;
                color: #6c757d;
            }
            
            .user-details h6 {
                margin: 0;
                color: #333;
                font-weight: 600;
            }
            
            .user-details small {
                color: #6c757d;
            }
            
            .mobile-user-menu {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            
            .mobile-user-menu li {
                margin-bottom: 5px;
            }
            
            .mobile-user-menu a {
                display: flex;
                align-items: center;
                padding: 12px 15px;
                color: #333;
                text-decoration: none;
                border-radius: 8px;
                transition: background 0.3s ease;
                font-size: 14px;
            }
            
            .mobile-user-menu a:hover {
                background: #f8f9fa;
                color: #333;
            }
            
            .mobile-user-menu i {
                margin-right: 10px;
                width: 16px;
                text-align: center;
                color: #6c757d;
            }
            
            .mobile-logout-btn {
                background: none;
                border: none;
                color: #dc3545;
                display: flex;
                align-items: center;
                padding: 12px 15px;
                width: 100%;
                text-align: left;
                border-radius: 8px;
                transition: background 0.3s ease;
                font-size: 14px;
            }
            
            .mobile-logout-btn:hover {
                background: #f8f9fa;
                color: #dc3545;
            }
            
            .mobile-social-section {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #eee;
            }
            
            .mobile-section-title {
                color: #333;
                margin-bottom: 15px;
                font-size: 14px;
                text-align: center;
                font-weight: 600;
                padding: 10px 0;
                border-bottom: 1px solid #eee;
            }
            
            .mobile-notifications-section {
                margin: 20px 0;
                padding: 15px 0;
                border-top: 1px solid #eee;
            }
            
            .mobile-notifications-list {
                padding: 10px 0;
            }
            
            .mobile-notification-item {
                display: flex;
                align-items: flex-start;
                padding: 12px 0;
                border-bottom: 1px solid #f0f0f0;
            }
            
            .mobile-notification-item:last-child {
                border-bottom: none;
            }
            
            .mobile-notification-item .notification-icon {
                margin-right: 12px;
                color: #007bff;
                font-size: 14px;
                margin-top: 2px;
                flex-shrink: 0;
            }
            
            .mobile-notification-item .notification-text {
                flex: 1;
            }
            
            .mobile-notification-item .notification-text p {
                margin: 0;
                font-size: 13px;
                color: #333;
                line-height: 1.4;
            }
            
            .mobile-notification-item .notification-time {
                font-size: 11px;
                color: #6c757d;
                margin-top: 4px;
                display: block;
            }
            
            .mobile-notifications-list p {
                font-size: 12px;
                color: #6c757d;
                margin: 0;
            }
            
            .mobile-notification-dropdown {
                width: 280px;
                max-height: 300px;
                overflow-y: auto;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                background-color: #fff;
                z-index: 9999 !important;
                padding: 0;
            }

            .mobile-notification-dropdown .notification-item {
                display: flex;
                align-items: flex-start;
                padding: 12px 15px;
                border-bottom: 1px solid #eee;
                transition: background-color 0.2s;
            }

            .mobile-notification-dropdown .notification-item:hover {
                background-color: #f8f9fa;
            }

            .mobile-notification-dropdown .notification-item i {
                margin-right: 12px;
                color: #007bff;
                font-size: 14px;
                margin-top: 2px;
                flex-shrink: 0;
            }

            .mobile-notification-dropdown .notification-item p {
                margin: 0;
                font-size: 13px;
                color: #333;
                line-height: 1.4;
            }

            .mobile-notification-dropdown .notification-item .time {
                font-size: 11px;
                color: #6c757d;
                margin-top: 4px;
                display: block;
            }
            
            .mobile-social-section h6 {
                color: #333;
                margin-bottom: 15px;
                font-size: 14px;
                text-align: center;
                font-weight: 600;
            }
            
            .social-icons {
                display: flex;
                gap: 15px;
                justify-content: center;
            }
            
            .social-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: #f5f5f5;
                border-radius: 50%;
                color: #333;
                text-decoration: none;
                transition: all 0.3s ease;
                font-size: 16px;
            }
            
            .social-icon:hover {
                background: #000;
                color: #fff;
                transform: translateY(-2px);
            }
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
                <i><img src="{{ asset('storage/' . ($imageSettings['white_logo'] ?? 'default-images/white_logo.png')) }}"
                    height="30" alt="Logo"></i> Barber House
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

            // Mobile Menu JavaScript
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                const mobileUserToggle = document.getElementById('mobileUserToggle');
                const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
                const mobileMenuClose = document.getElementById('mobileMenuClose');
                const mobileSearchBtn = document.querySelector('.mobile-search-btn');
                const mobileSearchInput = document.querySelector('.mobile-search-input');
                const mobileUserDropdown = document.getElementById('mobileUserDropdown');

                // Toggle mobile user dropdown
                if (mobileUserToggle) {
                    mobileUserToggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Toggle dropdown
                        if (mobileUserDropdown) {
                            mobileUserDropdown.classList.toggle('show');
                        }
                    });
                }

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (mobileUserDropdown && !mobileUserToggle.contains(e.target) && !mobileUserDropdown.contains(e.target)) {
                        mobileUserDropdown.classList.remove('show');
                    }
                });

                // Toggle mobile menu overlay (for navigation menu)
                if (mobileMenuBtn) {
                    mobileMenuBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        mobileMenuOverlay.classList.add('show');
                        document.body.style.overflow = 'hidden';
                    });
                }

                // Close mobile menu
                if (mobileMenuClose) {
                    mobileMenuClose.addEventListener('click', function() {
                        mobileMenuOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    });
                }

                // Close mobile menu when clicking outside
                if (mobileMenuOverlay) {
                    mobileMenuOverlay.addEventListener('click', function(e) {
                        if (e.target === mobileMenuOverlay) {
                            mobileMenuOverlay.classList.remove('show');
                            document.body.style.overflow = '';
                        }
                    });
                }

                // Mobile search functionality
                if (mobileSearchBtn && mobileSearchInput) {
                    mobileSearchBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const searchTerm = mobileSearchInput.value.trim();
                        if (searchTerm) {
                            window.location.href = `{{ route('client.product') }}?search=${encodeURIComponent(searchTerm)}`;
                        }
                    });

                    // Search on Enter key
                    mobileSearchInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            const searchTerm = this.value.trim();
                            if (searchTerm) {
                                window.location.href = `{{ route('client.product') }}?search=${encodeURIComponent(searchTerm)}`;
                            }
                        }
                    });
                }

                // Close menu on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        mobileMenuOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
                
                // Initialize mobile notifications
                loadMobileNotifications();
            });
            
            // Function to load mobile notifications
            function loadMobileNotifications() {
                const mobileNotificationList = document.getElementById('mobile-notification-list');
                const mobileNotificationCount = document.getElementById('mobile-notification-count');
                if (!mobileNotificationList) return;
                
                // Get notifications from localStorage (same as desktop)
                const userId = '{{ auth()->id() }}';
                const notifications = JSON.parse(localStorage.getItem('notifications_' + userId)) || [];
                
                // Update notification count
                if (mobileNotificationCount) {
                    mobileNotificationCount.textContent = notifications.length;
                }
                
                if (notifications.length === 0) {
                    mobileNotificationList.innerHTML = '<p class="text-center text-muted">Chưa có thông báo</p>';
                } else {
                    mobileNotificationList.innerHTML = '';
                    notifications.slice(0, 5).forEach((notification, index) => {
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
                                <a href="${link}" style="color: #333; text-decoration: none;">
                                    <p>${notification.message}</p>
                                </a>
                                <span class="time">${notification.time}</span>
                            </div>
                        `;
                        mobileNotificationList.appendChild(notificationItem);
                        
                        // Thêm sự kiện click để xóa thông báo
                        const notificationLink = notificationItem.querySelector('a');
                        notificationLink.addEventListener('click', function() {
                            removeMobileNotification(index);
                        });
                    });
                }
            }
            
            // Function to remove mobile notification
            function removeMobileNotification(index) {
                const userId = '{{ auth()->id() }}';
                let notifications = JSON.parse(localStorage.getItem('notifications_' + userId)) || [];
                notifications.splice(index, 1);
                localStorage.setItem('notifications_' + userId, JSON.stringify(notifications));
                
                // Reload mobile notifications
                loadMobileNotifications();
                
                // Update desktop notification count too
                const desktopBadge = document.querySelector('#notification-count');
                if (desktopBadge) {
                    desktopBadge.textContent = notifications.length;
                }
            }
            
            // Function to reset mobile notification count
            function resetMobileNotificationCount() {
                const userId = '{{ auth()->id() }}';
                localStorage.removeItem('notifications_' + userId);
                loadMobileNotifications();
                
                // Update desktop notification count too
                const desktopBadge = document.querySelector('#notification-count');
                if (desktopBadge) {
                    desktopBadge.textContent = '0';
                }
            }
        </script>
</body>

</html>
