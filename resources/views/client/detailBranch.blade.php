@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết chi nhánh Barber House
@endsection

@section('content')
    <main class="container">
        <section class="h-custom">
            <div class="mainDetailPost">
                <span>{{ $branch->created_at->format('d/m/Y') }}</span>

                <div class="path-post">
                    <p>
                        <a href="{{ route('client.branch') }}">Chi nhánh Barber House</a>
                        <i class="fa-solid fa-angle-right"></i>
                        {{ $branch->name }}
                    </p>
                </div>

                <h2>{{ $branch->name }}</h2>

                <div class="short-description">
                    {{ $branch->address }}
                </div>

                <div class="detail-post">
                    <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name }}">

                    {!! $branch->content !!}
                </div>

                <br /><br />

                <div style="text-align: center; margin-bottom: 5%;">
                    <h3>{{ $branch->name }}</h3>
                    <h3>{{ $branch->address }}</h3>
                    <h3>HOTLINE : {{ $branch->phone }}</h3>
                    <h3><a href="{{ $branch->google_map_url }}" target="_blank">📍 Xem bản đồ</a></h3>
                </div>
            </div>
        </section>
        <style>
            #mainNav {
                background-color: #000;
            }
        </style>
    </main>
@endsection

@section('card-footer')
@endsection
