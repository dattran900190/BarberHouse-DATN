<link rel="icon" href="{{ asset('images/favicon_logo.png') }}" type="image/png" />


<style>
    .sidebar-wrapper.scrollbar-inner {
        overflow-y: auto;
        /* Cho phép cuộn dọc */
        scrollbar-width: none;
        /* Ẩn scrollbar ở Firefox */
        -ms-overflow-style: none;
        /* IE 10+ */
    }

    /* Ẩn scrollbar ở Chrome, Edge, Safari */
    .sidebar-wrapper.scrollbar-inner::-webkit-scrollbar {
        width: 0px;
        background: transparent;
        /* Optional: ẩn màu nền scrollbar */
    }

    .custom-swal-popup {
        width: 550px !important;
        max-width: 550px !important;
        max-height: 450px !important;
        height: 450px !important;
    }

    /* Tăng kích thước vòng xoáy loading */
    .swal2-loading {
        font-size: 1.5rem !important;
    }

    .custom-swal-popup .swal2-title {
        margin-top: 15px !important;
        font-size: 1.5rem !important;

    }

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

<!-- Fonts and icons -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/webfont/webfont.min.js') }}"></script>
<script>
    WebFont.load({
        google: {
            families: ["Public Sans:300,400,500,600,700"]
        },
        custom: {
            families: [
                "Font Awesome 5 Solid",
                "Font Awesome 5 Regular",
                "Font Awesome 5 Brands",
                "simple-line-icons",
            ],
            urls: ["{{ asset('kaiadmin-lite-1.2.0/assets/css/fonts.min.css') }}"],
        },
        active: function() {
            sessionStorage.fonts = true;
        },
    });
</script>

<!-- CSS Files -->
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/plugins.min.css') }}" />
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/kaiadmin.min.css') }}" />

<!-- CSS Just for demo purpose, don't include it in your project -->
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/demo.css') }} " />
