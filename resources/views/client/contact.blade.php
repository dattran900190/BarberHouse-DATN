@extends('layouts.ClientLayout')

@section('title-page')
    Liên hệ Barber House
@endsection

@section('content')
    <main class="container py-4">
        <section class="mt-5 text-center">
            <h1 class="fw-bold">Liên hệ Barber House</h1>
            <p class="text-muted">Kết nối với chúng tôi để được tư vấn và hỗ trợ nhanh chóng.</p>
        </section>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="fw-bold mb-3">Thông tin liên hệ</h2>
                <ul class="list-unstyled fs-5">
                    <li><strong>Địa chỉ:</strong> Tòa nhà FPT Polytechnic, P. Trịnh Văn Bô, Xuân Phương, Nam Từ Liêm, Hà Nội</li>
                    <li><strong>Điện thoại:</strong> <a href="tel:0123 456 789">0123 456 789</a></li>
                    <li><strong>Email:</strong> <a href="mailto:trandiep490@gmail.com">trandiep490@gmail.com</a></li>
                    <li><strong>Giờ làm việc:</strong> Thứ 2 – Chủ nhật (08:00 – 19:30)</li>
                </ul>

                {{-- Google Map --}}
                <div class="mt-4">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2891.729759192177!2d105.74663568150406!3d21.03721287590285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455305afd834b%3A0x17268e09af37081e!2sT%C3%B2a%20nh%C3%A0%20FPT%20Polytechnic.!5e1!3m2!1svi!2s!4v1754931533993!5m2!1svi!2s" 
                        width="100%" height="500" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <style>
            #mainNav {
                background-color: #000;
            }
        </style>
    </main>
@endsection

@section('card-footer')
@endsection
