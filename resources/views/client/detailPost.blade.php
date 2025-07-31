@extends('layouts.ClientLayout')

@section('title-page')
    Chi tiết bài viết Baber House
@endsection

@section('content')
    <main class="container">
        <section class="h-custom">
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
            </style>
        </section>
    </main>
@endsection

@section('card-footer')

@endsection
