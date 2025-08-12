@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết chi nhánh Barber House
@endsection

@section('content')
    <main class="branch-detail-container">
        <section class="branch-detail-section">
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
        #mainNav {
            background-color: #000;
        }

        .branch-detail-container {
            width: 100%;
            max-width: 100%;
            margin: 0;
            padding: 0;
        }

        .branch-detail-section {
            width: 100%;
            padding: 0;
        }

        .mainDetailPost {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .branch-date {
            font-size: 0.9rem;
            color: #666;
        }

        .path-post {
            margin-bottom: 1rem;
        }

        .path-post p {
            margin: 0;
            color: #666;
        }

        .path-post a {
            color: #333;
            text-decoration: none;
        }

        .path-post a:hover {
            color: #000;
        }

        .branch-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .short-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .detail-post {
            margin-bottom: 3rem;
            line-height: 1.8;
        }

        .detail-post img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
            display: block;
        }

        .detail-post p {
            margin-bottom: 1rem;
        }

        .branch-info {
            text-align: center;
            margin-bottom: 2rem;
        }

        .branch-info h3 {
            margin-bottom: 0.5rem;
        }

        .map-container {
            position: relative;
            padding-top: 56.25%;
            overflow: hidden;
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

        /* Mobile responsive */
        @media (max-width: 768px) {
            .mainDetailPost {
                max-width: 100%;
                padding: 1rem;
            }

            .branch-title {
                font-size: 2rem;
            }

            .short-description {
                font-size: 1rem;
            }

            .detail-post img {
                border-radius: 0;
                margin: 0;
            }
        }

        @media (max-width: 480px) {
            .mainDetailPost {
                padding: 0.5rem;
                margin-top: 100px;
            }

            .branch-title {
                font-size: 1.8rem;
            }

            .branch-info h3 {
                font-size: 1rem;
            }
        }
    </style>
@endsection

@section('card-footer')
@endsection
