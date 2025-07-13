<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.blocksAdmin.includes.link-head')

     <style>
        .toast {
            min-width: 300px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-top: 10px;
            /* Khoảng cách giữa các Toast */
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

    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
{{-- <script src="{{ asset('js/bootstrap.js') }}"></script> --}}

</head>

<body>
    <div class="wrapper">
        <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;"
            id="toastContainer">
            <!-- Toast mẫu (sẽ được clone động) -->
            <div id="appointmentToastTemplate" class="toast" role="alert" data-bs-delay="180000"
                style="display: none;">
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
        <!-- Sidebar -->
        @include('layouts.blocksAdmin.sidebar')
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                </div>
                <!-- Navbar Header -->
                @include('layouts.blocksAdmin.header')
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    @yield('content')
                </div>
            </div>

            @include('layouts.blocksAdmin.footer')
        </div>
    </div>

    @include('layouts.blocksAdmin.includes.link-foot')

    {{-- js thông báo real-time --}}
    {{-- <script src="{{ asset('js/admin.js') }}"></script> --}}

    @yield('js')

</body>
@vite(['resources/js/app.js', 'resources/js/bootstrap.js', 'resources/js/admin.js'])
</html>
