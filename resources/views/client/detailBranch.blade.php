@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết chi nhánh Barber House
@endsection

@section('content')
    <main class="container branch-detail">
        <section>
            <div class="mainDetailPost">
                <span class="branch-date">{{ $branch->created_at->format('d/m/Y') }}</span>

                <div class="path-post">
                    <p>
                        <a href="{{ route('client.branch') }}">Chi nhánh Barber House</a>
                        <i class="fa-solid fa-angle-right"></i>
                        {{ $branch->name ?? 'Không xác định' }}
                    </p>
                </div>

                <h2 class="branch-title">{{ $branch->name ?? 'Không xác định' }}</h2>

                <div class="short-description">
                    {{ $branch->address ?? 'Không xác định' }}
                </div>

                <div class="detail-post">
                    <img src="{{ asset('storage/' . $branch->image) }}" alt="{{ $branch->name ?? 'Không xác định' }}">

                    {!! $branch->content !!}
                </div>

                <div class="branch-info">
                    <h3>{{ $branch->name ?? 'Không xác định' }}</h3>
                    <h3>{{ $branch->address ?? 'Không xác định' }}</h3>
                    <h3>HOTLINE : {{ $branch->phone ?? 'Không xác định' }}</h3>
                    <div class="map-container">
                        <iframe src="{{ $branch->google_map_url }}" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- CSS Responsive --}}
    <style>
        .branch-detail {
            max-width: 900px;
            margin: auto;
            padding: 15px;
        }

        .branch-date {
            font-size: 14px;
            color: #888;
        }

        .branch-title {
            margin-top: 10px;
            font-size: 28px;
            line-height: 1.3;
        }

        .short-description {
            font-style: italic;
            color: #555;
            margin-bottom: 20px;
        }

        .detail-post img {
            width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .detail-post p {
            line-height: 1.6;
            margin-bottom: 15px;
            text-align: justify;
        }

        .branch-info {
            text-align: center;
            margin: 30px 0;
        }

        .map-container {
            position: relative;
            overflow: hidden;
            padding-top: 56.25%;
            /* tỷ lệ 16:9 */
            border-radius: 8px;
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Mobile */
        @media (max-width: 576px) {
            .branch-title {
                font-size: 22px;
            }

            .branch-info h3 {
                font-size: 16px;
            }
        }
    </style>
@endsection

@section('card-footer')
@endsection
