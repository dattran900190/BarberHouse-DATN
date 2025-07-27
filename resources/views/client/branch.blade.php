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
                            <a style="color: white" href="{{ route('client.detailBranch', $branch->id) }}">
                                <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name }}">
                            </a>
                            <div class="overlaybranch"></div>
                            <div class="overlay">
                                <h4>
                                    <a style="color: white" href="{{ route('client.detailBranch', $branch->id) }}">
                                        {{ $branch->name }}
                                    </a>
                                </h4>
                                <h6>
                                    <a style="color: white" href="{{ route('client.detailBranch', $branch->id) }}">
                                        {{ $branch->address }}
                                    </a>
                                </h6>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
    <style>
        .branchs .branch {
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #eee;
            overflow: hidden;
        }

        .branchs .branch:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection

@section('card-footer')
@endsection
