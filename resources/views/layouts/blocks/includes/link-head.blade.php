 {{-- <link rel="stylesheet" href="{{ asset('/css/client.css') }}" /> --}}
 <link rel="stylesheet" href="{{ asset('css/client.css?v=1.0') }}" />
 {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" /> --}}
 {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" /> --}}
 {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
 {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

 {{-- <link rel="stylesheet" type="text/css"
        href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" /> --}}

 {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> --}}
 {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

 @if ($banners->isNotEmpty())
     <link rel="preload" as="image" href="{{ asset('storage/' . $banners->first()->image_url) }}">
 @endif
 
 <link rel="icon" href="{{ asset('images/favicon_logo.png') }}" type="image/png" />


{{-- 
 <style>
     .page-link {
         color: #000;
         border-radius: 5px;
         /* border: 1px solid #000; */
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

     /* Giao diện chính của select2 */
     .select2-container--default .select2-selection--single {
         height: 44px;
         padding: 8px 14px;
         /* border: 2px solid #000; */
         border-radius: 4px;
         background-color: #fff;
         font-size: 15px;
         font-weight: 500;
         color: #000;
         transition: all 0.3s ease;
     }

     /* Khi focus */
     .select2-container--default .select2-selection--single:focus,
     .select2-container--default .select2-selection--single.select2-selection--focus {
         border-color: #000;
         box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
         outline: none;
     }

     /* Mũi tên chọn */
     .select2-container--default .select2-selection--single .select2-selection__arrow {
         top: 8px;
         right: 8px;
     }

     /* Text hiển thị */
     .select2-container--default .select2-selection--single .select2-selection__rendered {
         color: #000;
         line-height: 28px;
     }

     /* Dropdown list */
     .select2-container--default .select2-results>.select2-results__options {
         max-height: 240px;
         border: 1px solid #000;
         border-radius: 6px;
         font-size: 15px;
     }

     .select2-container--default .select2-results__option--highlighted {
         background-color: #000;
         color: #fff;
     }

     .btn-icon-remove {
         background-color: transparent;
         border: 1px solid #ccc;
         color: #000;
         border-radius: 6px;
         width: 36px;
         height: 36px;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 14px;
         transition: all 0.3s ease;
         cursor: pointer;
     }

     .btn-icon-remove:hover {
         background-color: #000;
         color: #fff;
     }
 </style> --}}
