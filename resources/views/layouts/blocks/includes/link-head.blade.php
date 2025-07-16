 {{-- <link rel="stylesheet" href="{{ asset('/css/client.css') }}" /> --}}
 <link rel="stylesheet" href="{{ asset('css/client.css?v=1.0') }}" />
 {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" /> --}}
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
 <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

 {{-- <link rel="stylesheet" type="text/css"
        href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" /> --}}

 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
 <meta name="csrf-token" content="{{ csrf_token() }}">

 <link rel="icon" href="{{ asset('images/favicon_logo.png') }}" type="image/png" />

 @if ($banners->isNotEmpty())
     <link rel="preload" as="image" href="{{ asset('storage/' . $banners->first()->image_url) }}">
 @endif

 <link rel="preload" as="image" href="{{ asset('images/favicon_logo.png') }}">

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
 </style>
