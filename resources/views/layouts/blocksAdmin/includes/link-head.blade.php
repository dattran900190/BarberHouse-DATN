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
