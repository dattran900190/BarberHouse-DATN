@extends('layouts.ClientLayout')

@section('title-page')
    Chi nhánh Barber House
@endsection

@section('slider')
    <section class="hero-slider">
        @foreach ($branches->take(3) as $branch)
            <div class="slide {{ $loop->first ? 'active' : '' }}">
                <img src="{{ asset('storage/' . $branch->image) }}" alt="Slide {{ $loop->iteration }}">
                <div class="overlay">
                    <h4>
                        <a href="{{ route('client.detailBranch', $branch->id) }}">
                            {{ $branch->name }}
                        </a>
                    </h4>
                </div>
            </div>
        @endforeach
        <button class="prev">‹</button>
        <button class="next">›</button>
    </section>
@endsection

@section('content')
    <main class="container">
        <div class="main-branchs">
            <h2>Các chi nhánh của Barber House</h2>

            <div class="branchs">
                @foreach ($branches as $branch)
                    <div class="branch">
                        <div class="image-branch">
                            <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name }}">
                            <div class="overlay">
                                <h4>
                                    <a href="{{ route('client.detailBranch', $branch->id) }}">
                                        {{ $branch->name }}
                                    </a>
                                </h4>
                                <p>
                                    <a href="{{ $branch->google_map_url }}" target="_blank">
                                        📍 Xem bản đồ
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection

@section('card-footer')
@endsection
