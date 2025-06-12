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
</head>

<body>
    <div class="site-wrapper">
        @include('layouts.blocks.header')

        @yield('slider')

        <div class="site-content">
            @yield('content')
        </div>

        @include('layouts.blocks.footer')
        @yield('scripts')
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    <script src="{{ asset('js/client.js') }}"></script>
</body>

</html>
