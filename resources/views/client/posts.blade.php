@extends('layouts.ClientLayout')

@section('title-page')
    Bài viết Baber House
@endsection

@section('content')
    <main class="container">
        <div class="main-posts">
            <h2>Bài viết</h2>
            <div class="posts-content">
                <div class="post-left">
                    {{-- Bài viết nổi bật đầu tiên --}}
                    <h5>Bài viết mới nhất</h5>
                    @if ($featuredPosts->isNotEmpty())
                    @php $topPost = $featuredPosts->first(); @endphp

                    <div class="post-top">
                        <div class="image-top">
                            <a href="{{ route('client.detailPost', $topPost->slug) }}">
                                <img src="{{ asset('storage/' . $topPost->image) }}" alt="{{ $topPost->title }}" />
                            </a>
                        </div>
                        <h4>
                            <a href="{{ route('client.detailPost', $topPost->slug) }}">
                                {{ $topPost->title }}
                            </a>
                        </h4>
                        <p>{{ Str::limit(strip_tags($topPost->short_description), 100) }}</p>
                    </div>
                @endif

                    {{-- Các bài viết thường --}}
                    <h5>Các bài viết khác</h5>
                    <div class="post-mid">
                        @foreach ($normalPosts as $post)
                            <div class="post">
                                <div class="image-mid">
                                    <a href="{{ route('client.detailPost', $post->slug) }}">
                                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" />
                                    </a>
                                </div>
                                <h4>
                                    <a href="{{ route('client.detailPost', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h4>
                                <p>{{ Str::limit(strip_tags($post->short_description), 100) }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Phân trang nếu có --}}
                    <div class="mt-3">
                        {{ $normalPosts->links() }}
                    </div>
                </div>

                {{-- Sidebar: Bài viết nổi bật --}}
                <div class="post-right">
                    <h5>Bài viết nổi bật</h5>
                    @foreach ($featuredPosts as $post)
                        <div class="post">
                            <div class="image-right">
                                <a href="{{ route('client.detailPost', $post->slug) }}">
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" />
                                </a>
                            </div>
                            <h5>
                                <a href="{{ route('client.detailPost', $post->slug) }}">
                                    {{ Str::limit($post->title, 50) }}
                                </a>
                            </h5>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }

        .main-posts {
            padding: 2rem 0;
        }

        .main-posts h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            text-align: center;
        }

        .posts-content {
            display: flex;
            gap: 10px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .post-left {
            flex: 2;
            max-width: 800px;
        }

        .post-left h5 {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #000;
            padding: 0.5rem 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .post-right {
            flex: 1;
            max-width: 350px;
        }

        /* Featured post styling */
        .post-top {
            margin-bottom: 1rem;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            /* box-shadow: 0 4px 12px rgba(0,0,0,0.1); */
        }

        .post-top:hover {
            transform: translateY(-5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .post-top .image-top {
            width: 100%;
            height: 400px;
            overflow: hidden;
        }

        .post-top .image-top img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
            border-radius: 8px;
        }

        .post-top:hover .image-top img {
            transform: scale(1.05);
        }

        .post-top h4 a {
            color: #000;
        }

        .post-top h4 {
            padding: 10px 10px 0;
            font-size: 1.5rem;
            font-weight: bold;
            /* line-height: 1.3; */
            text-transform: uppercase;
        }

        .post-top p {
            padding: 0 10px 10px;
            color: #666;
            /* line-height: 1.6; */
        }

        /* Grid layout for posts */
        .post-mid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 2rem;
        }

        .post {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            /* box-shadow: 0 2px 8px rgba(0,0,0,0.1); */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .post:hover {
            transform: translateY(-5px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
.post .image-mid {
            width: 100%;
            height: 250px;
            overflow: hidden;
        }

        .post .image-mid img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
            border-radius: 8px;
        }

        .post:hover .image-mid img {
            transform: scale(1.08);
        }

        .post h4 {
            padding: 1rem 1rem 0;
            font-size: 1.1rem;
            font-weight: 600;
            /* line-height: 1.4; */
            text-transform: uppercase;
            margin: 0;
        }

        /* Sidebar styling */
        .post-right h5 {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            color: #000;
            padding: 0.5rem 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .post-right .post {
            margin-bottom: 1.5rem;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            /* box-shadow: 0 2px 8px rgba(0,0,0,0.1); */
        }

        .post-right .post .image-right {
            width: 100%;
            height: 220px;
            overflow: hidden;
        }

        .post-right .post .image-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
            border-radius: 8px;
        }

        .post-right .post:hover .image-right img {
            transform: scale(1.08);
        }

        .post-right .post h5 {
            padding: 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            /* line-height: 1.3; */
            margin: 0;
        }

        .post-right .post h5 a {
            color: #000;
            text-decoration: none;
        }

        .post-right .post h5 a:hover {
            color: #333;
        }

        /* Links styling */
        .post a {
            color: #000;
            text-decoration: none;
        }

        .post a:hover {
            color: #333;
        }

        /* Section headers */
        .mt-4 {
            margin-top: 2rem;
        }

        .mt-3 {
            margin-top: 1.5rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .posts-content {
                flex-direction: column;
                gap: 2rem;
            }

            .post-left, .post-right {
                max-width: 100%;
            }

            .post-mid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .post-top .image-top {
                height: 300px;
            }

            .post .image-mid {
                height: 300px;
            }

            .post-right .post .image-right {
                height: 250px;
            }
        }

        @media (max-width: 480px) {
            .main-posts h2 {
                font-size: 2rem;
            }

            .post-top h4 {
                font-size: 1.3rem;
            }

            .post h4 {
                font-size: 1rem;
            }

            .post-right .post h5 {
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

@section('card-footer')
@endsection
