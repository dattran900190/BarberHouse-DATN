
 <link rel="stylesheet" href="{{ asset('css/client.css?v=1.0') }}" />

 @if ($banners->isNotEmpty())
     <link rel="preload" as="image" href="{{ asset('storage/' . $banners->first()->image_url) }}">
 @endif
 
 <link rel="icon" href="{{ asset('images/favicon_logo.png') }}" type="image/png" />

