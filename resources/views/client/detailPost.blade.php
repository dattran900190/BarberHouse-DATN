@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết bài viết Baber House
@endsection

@section('content')
    <main class="detail-container">
        <section class="detail-section">
            <div class="mainDetailPost">
                    <span>{{ $post->created_at->format('d/m/Y') ?? 'Không xác định' }}</span>

                <div class="path-post">
                    <p>
                        <a href="{{ route('client.posts') }}">Bài viết Barber House </a>
                        <i class="fa-solid fa-angle-right"></i>
                        {{ $post->title ?? 'Không xác định' }}
                    </p>
                </div>

                <h2>{{ $post->title ?? 'Không xác định' }}</h2>

                <div class="short-description">
                    {{ $post->short_description ?? 'Không xác định' }}
                </div>

                <div class="detail-post">
                    {!! $post->content !!}
                </div>


                <div class="orther-post">
                    <h3>Mọi người đều đọc</h3>
                    <br>
                    <div class="popular-articles">
                        @foreach ($posts as $post)
                        <div class="article">
                            <span class="number">{{ $loop->iteration }}</span>
                            <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title ?? 'Không xác định' }}" />
                            <a href="{{ route('client.detailPost', $post->slug) }}">{{ $post->title ?? 'Không xác định' }}</a>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>
            <style>
                #mainNav {
                    background-color: #000;
                }

                .detail-container {
                    width: 100%;
                    max-width: 100%;
                    margin: 0;
                    padding: 0;
                }

                .detail-section {
                    width: 100%;
                    padding: 0;
                }

                .mainDetailPost {
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 2rem 1rem;
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

                .mainDetailPost h2 {
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

                .detail-post h1, .detail-post h2, .detail-post h3, .detail-post h4, .detail-post h5, .detail-post h6 {
                    margin-bottom: 1rem;
                }

                .detail-post ul, .detail-post ol {
                    margin-bottom: 1rem;
                }

                .detail-post li {
                    margin-bottom: 0.5rem;
                }

                .orther-post h3 {
                    font-size: 1.8rem;
                    font-weight: bold;
                    margin-bottom: 1rem;
                }

                .popular-articles {
                    display: flex;
                    flex-direction: column;
                    gap: 1.5rem;
                }

                .article {
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    padding: 1rem;
                    background: #fff;
                    border-radius: 8px;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .article:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }

                .article .number {
                    font-size: 1.5rem;
                    font-weight: bold;
                    color: #333;
                    min-width: 30px;
                }

                .article img {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                    border-radius: 8px;
                }

                .article a {
                    color: #333;
                    text-decoration: none;
                    font-weight: 500;
                    line-height: 1.4;
                }

                .article a:hover {
                    color: #000;
                }

                /* Mobile responsive */
                @media (max-width: 768px) {
                    .mainDetailPost {
                        max-width: 100%;
                        padding: 1rem;
                    }

                    .mainDetailPost h2 {
                        font-size: 2rem;
                    }

                    .short-description {
                        font-size: 1rem;
                    }

                    .detail-post img {
                        border-radius: 0;
                        margin: 0;
                    }

                    .article {
                        flex-direction: column;
                        text-align: center;
                        gap: 0.5rem;
                        padding: 0;
                        background: transparent;
                        border-radius: 0;
                    }

                    .article .number {
                        font-size: 1.2rem;
                        min-width: auto;
                    }

                    .article img {
                        width: 100%;
                        height: 250px;
                        border-radius: 0;
                    }

                    .article a {
                        font-size: 0.95rem;
                        padding: 0 1rem;
                    }
                }

                @media (max-width: 480px) {
                    .mainDetailPost {
                        padding: 0.5rem;
                    }

                    .mainDetailPost h2 {
                        font-size: 1.8rem;
                    }

                    .orther-post h3 {
                        font-size: 1.5rem;
                    }

                    .article {
                        padding: 0;
                        margin-bottom: 1rem;
                    }

                    .article img {
                        height: 200px;
                    }

                    .article a {
                        padding: 0 0.5rem;
                    }
                }
            </style>
        </section>
    </main>
@endsection

@section('card-footer')

@endsection
