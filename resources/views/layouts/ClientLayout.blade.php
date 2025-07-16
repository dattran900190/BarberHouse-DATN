<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title-page')</title>
   
    @include('layouts.blocks.includes.link-head')
</head>



<body>
    <div class="site-wrapper">
        @include('layouts.blocks.header')

        @yield('slider')

        <div class="site-content">
            @yield('content')
        </div>

        @include('layouts.blocks.footer')
        {{-- @stack('scripts') --}}
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
   
    @include('layouts.blocks.includes.link-foot')
</body>

</html>
