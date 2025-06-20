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
                @php
                    $topPost = $posts->first();
                @endphp
                @if ($topPost)
                <div class="post-top">
                    <div class="image-top">
                        <a href="{{ route('client.detailPost', $topPost->slug) }}">
                            <img src="{{ asset('storage/' . $topPost->image) }}" alt="" />
                        </a>
                    </div>
                    <h4>
                        <a href="{{ route('client.detailPost', $topPost->slug) }}">
                            {{ $topPost->title }}
                        </a>
                    </h4>
                    <p>{{ Str::limit(strip_tags($topPost->content), 100) }}</p>
                </div>
                @endif

                {{-- Các bài viết còn lại --}}
                <div class="post-mid">
                    @foreach ($posts->skip(1) as $post)
                    <div class="post">
                        <div class="image-mid">
                            <a href="{{ route('client.detailPost', $post->slug) }}">
                                <img src="{{ asset('storage/' . $post->image) }}" alt="" />
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
            </div>

            {{-- Sidebar bài viết --}}
            <div class="post-right">
                @foreach ($posts->take(5) as $post)
                <div class="post">
                    <div class="image-right">
                        <a href="{{ route('client.detailPost', $post->id) }}">
                            <img src="{{ asset('storage/' . $post->image) }}" alt="" />
                        </a>
                    </div>
                    <h5>
                        <a href="{{ route('client.detailPost', $post->id) }}">
                           
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
