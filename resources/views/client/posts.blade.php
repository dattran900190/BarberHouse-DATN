@extends('layouts.ClientLayout')

@section('title-page')
    Tin tức Baber House
@endsection

@section('content')
<main class="container">
    <div class="main-posts">
        <h2>Tin tức</h2>
        <div class="posts-content">
            <div class="post-left">

                {{-- Bài viết nổi bật --}}
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

                    {{-- Các bài nổi bật còn lại --}}
                    <div class="post-mid">
                        @foreach ($featuredPosts->skip(1) as $post)
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
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Các bài viết thường --}}
                <h3 class="mt-4">Bài viết khác</h3>
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
                        </div>
                    @endforeach
                </div>

                {{-- Phân trang nếu có --}}
                <div class="mt-3">
                    {{ $normalPosts->links() }}
                </div>
            </div>

            {{-- Sidebar: 5 bài viết mới nhất --}}
            <div class="post-right">
                {{-- <h5>Bài viết mới</h5> --}}
                @foreach ($normalPosts->take(5) as $post)
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
</style>
@endsection

@section('card-footer')
@endsection
