<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>@yield('title')</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.blocksAdmin.includes.link-head')


    {{-- <script src="https://js.pusher.com/8.2/pusher.min.js"></script> --}}
</head>

<body>
    <div class="wrapper">
       
        {{-- toast --}}
        @include('layouts.blocksAdmin.toast.allToast')

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

    @yield('js')

</body>
@vite(['resources/js/app.js', 'resources/js/bootstrap.js', 'resources/js/admin.js'])
</html>
