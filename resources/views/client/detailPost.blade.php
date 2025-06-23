@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Chi tiết tin tức Baber House
@endsection

@section('content')
    <main class="container">
        <section class=" h-custom">
            <div class="mainDetailPost">
                <span>7/2/2005</span>

                <div class="path-post">
                    <p>
                        <a href="{{ route('client.posts') }}">Tin tức Barber House </a>
                        <i class="fa-solid fa-angle-right"></i>
                        Cắt Tóc Gió ở Sài Gòn: Nét Văn Hóa Vỉa Hè Xưa
                    </p>
                </div>

                <h2>{{ $post->title }}</h2>

                <div class="short-description">
                    "Cắt tóc gió" là một thuật ngữ dân gian, thường được dùng để chỉ
                    việc cắt tóc rất nhanh, bình dân, người thợ thường cắt ở địa điểm
                    ngoài trời, có thể là bên gốc cây, hoặc vỉa hè nào đó.
                </div>

                <div class="detail-post">
                    {!! $post->content !!}
                </div>


                <div class="orther-post">
                    <h3>Mọi người đều đọc</h3>
                    <br>
                    <div class="popular-articles">
                        <div class="article">
                            <span class="number">1</span>
                            <img src="https://4rau.vn/thumb/810x560/1/upload/news/z6553461600146_db1ad63e00baaae63d8873cc50220319-9902.jpg"
                                alt="Cắt Tóc Gió" />
                            <a href="{{ route('client.detailPost', 'slug-cua-bai-viet') }} ">Cắt Tóc Gió ở Sài Gòn: Nét Văn Hóa Vỉa Hè Xưa</a>
                        </div>

                        <div class="article">
                            <span class="number">2</span>
                            <img src="https://4rau.vn/thumb/810x560/1/upload/news/z6553461600146_db1ad63e00baaae63d8873cc50220319-9902.jpg"
                                alt="4RAU TÂN ĐỊNH" />
                            <a href="{{ route('client.detailPost', 'slug-cua-bai-viet') }}">Con đường Trần Quang Khải nay đã có thêm tên của 4RAU TÂN
                                ĐỊNH từ ngày 6/4/2025</a>
                        </div>


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
