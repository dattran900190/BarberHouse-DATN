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


    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    /* Highlight cho row mới */
    .highlight-new-appointment {
        animation: highlightPulse 2s ease-in-out;
        border-left: 4px solid #28a745 !important;
    }

    @keyframes highlightPulse {

        0%,
        100% {
            background-color: #fff3cd;
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }

        50% {
            background-color: #d4edda;
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
    }

    /* Toast template từ allToast.blade.php */
    #toastContainer .toast {
        min-width: 350px;
        max-width: 450px;
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    #toastContainer .toast-header {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-bottom: none;
        padding: 12px 15px;
    }

    #toastContainer .toast-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    #toastContainer .toast-header .btn-close:hover {
        opacity: 1;
    }

    #toastContainer .toast-body {
        padding: 15px;
        font-size: 14px;
        line-height: 1.5;
    }

    #toastContainer .toast-body p {
        margin-bottom: 15px;
        font-size: 15px;
        font-weight: 500;
        color: #495057;
    }

    #toastContainer .btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 13px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    #toastContainer .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        text-decoration: none;
    }

    /* Responsive cho mobile */
    @media (max-width: 768px) {
        #toastContainer .toast {
            min-width: 300px;
            max-width: 350px;
        }
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


<style>
    .required {
        color: #ff4444;
    }

    .select2-container--default .select2-selection--single {
        height: 40px !important;
        padding: 5px 14px !important;
        border-radius: 5px !important;
        background-color: #fff !important;
        font-size: 1rem !important;
        border-color: #ebedf2 !important;
        outline: none !important;
        border: 2px solid #e9ecef;
        color: #333;
    }

    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single.select2-selection--focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 2px #007bff !important;
    }

     .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 8px !important;
            right: 8px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
        }

    /* Branch Cards Styles */
    .branch-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 10px;
    }

    .branch-card {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 5px;
        background: #fff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .branch-card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        transform: translateY(-2px);
    }

    .branch-card.selected {
        border-color: #28a745;
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
    }

    .branch-card.selected::after {
        content: '✓';
        position: absolute;
        top: 10px;
        right: 10px;
        background: #28a745;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .branch-icon-wrapper {
        margin-right: 15px;
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        /* background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .branch-icon {
        color: #000;
        font-size: 20px;
        transition: all 0.3s ease;
    }

    .branch-icon:hover {
        border-color: #007bff;
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
        transform: translateY(-2px);
        background: linear-gradient(135deg, #f8f9ff 0%, #e8f0ff 100%);
    }

    .branch-info {
        flex: 1;
    }

    .branch-name {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .branch-address,
    .branch-hours {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        font-size: 12px;
        color: #6c757d;
    }

    .branch-address i,
    .branch-hours i {
        margin-right: 5px;
        font-size: 10px;
    }
</style>



<!-- CSS Files -->
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/bootstrap.min.css') }}" />
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/plugins.min.css') }}" />
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/kaiadmin.min.css') }}" />

<!-- CSS Just for demo purpose, don't include it in your project -->
<link rel="stylesheet" href="{{ asset('kaiadmin-lite-1.2.0/assets/css/demo.css') }} " />
