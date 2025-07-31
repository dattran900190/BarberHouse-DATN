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
                        {{ $branch->name ?? 'Không xác định' }}
                    </p>
                </div>

                <h2>{{ $branch->name ?? 'Không xác định' }}</h2>

                <div class="short-description">
                    {{ $branch->address ?? 'Không xác định' }}
                </div>

                <div class="detail-post">
                    <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name ?? 'Không xác định' }}">

                    {!! $branch->content !!}
                </div>

                <br /><br />

                <div style="text-align: center; margin-bottom: 5%;">
                    <h3>{{ $branch->name ?? 'Không xác định' }}</h3>
                    <h3>{{ $branch->address ?? 'Không xác định' }}</h3>
                    <h3>HOTLINE : {{ $branch->phone ?? 'Không xác định' }}</h3>
                    <iframe src="{{ $branch->google_map_url }}" frameborder="0" height="500" width="750"></iframe>

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
