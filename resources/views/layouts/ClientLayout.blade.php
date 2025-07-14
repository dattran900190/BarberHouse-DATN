<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title-page')</title>
    <link rel="stylesheet" href="{{ asset('/css/client.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" type="text/css"
        href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .button-group {
            display: flex;
            justify-content: center;
            gap: 16px;
            /* điều chỉnh khoảng cách tại đây */
            margin-top: 12px;
        }

        .btn-outline-buy {
            padding: 10px 24px;
            border: 2px solid #000;
            border-radius: 6px;
            background-color: transparent;
            font-weight: bold;
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #000;
        }

        a {
            text-decoration: none;
        }

        .btn-outline-cart {
            padding: 13px 24px;
            border: 2px solid #000;
            border-radius: 6px;
            background-color: transparent;
            font-weight: bold;
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #000;
        }

        .btn-outline-booking {
            padding: 13px 24px;
            border: 2px solid #000;
            border-radius: 6px;
            background-color: transparent;
            font-weight: bold;
            text-transform: uppercase;
            font-family: Arial, sans-serif;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            /* display: inline-flex; */
            align-items: center;
            text-align: center
            gap: 10px;
            color: #000;
            width: 100%;
        }

        .btn-outline-buy i {
            font-size: 16px;
        }

        .btn-outline-buy:hover,
        .btn-outline-booking:hover,
        .btn-outline-cart:hover {
            background-color: #000;
            color: #fff;
        }
    </style>
</head>



<body>
    <div class="site-wrapper">
        @include('layouts.blocks.header')

        @yield('slider')

        <div class="site-content">
            @yield('content')
        </div>

        @include('layouts.blocks.footer')
        @stack('scripts')
        <!-- Chat Button -->
        <div class="chat-wrapper">
            <button id="chatToggle" class="chat-button">
                💬
            </button>

            <!-- Chat Box -->
            <div class="chat-box" id="chatBox">
                <div class="chat-header">
                    <span>Hỗ trợ khách hàng</span>
                    <button id="chatClose">×</button>
                </div>
                <div class="chat-body">
                    <p>Xin chào! Tôi có thể giúp gì cho bạn?</p>
                    <!-- Sau này bạn chèn Facebook Chat hoặc Tawk.to tại đây -->
                </div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>



    @yield('scripts')
    <script src="{{ asset('js/client.js') }}"></script>
</body>

</html>
