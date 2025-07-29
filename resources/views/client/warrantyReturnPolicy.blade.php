@extends('layouts.ClientLayout')

@section('title-page')
    Chính sách bảo hành - đổi trả
@endsection

@section('content')
<main class="container py-5">
    <section class="h-custom">
        <div class="mainWarrantyReturnPolicy mb-4">
            <h1 class="fw-bold text-center">Chính sách bảo hành - đổi trả</h1>
        </div>

        <div class="contentWarrantyReturnPolicy">
            <h2>1. Chính sách bảo hành</h2>
            <p>Chúng tôi cam kết cung cấp sản phẩm/dịch vụ chất lượng và đảm bảo quyền lợi của khách hàng với chính sách bảo hành rõ ràng:</p>
            <ul>
                <li><strong>Thời gian bảo hành:</strong> từ 07 - 30 ngày tùy theo từng sản phẩm/dịch vụ.</li>
                <li><strong>Điều kiện bảo hành:</strong> sản phẩm còn nguyên tem, không có dấu hiệu tác động vật lý hay sử dụng sai cách.</li>
                <li><strong>Không áp dụng bảo hành:</strong> với các lỗi do người dùng gây ra như rơi vỡ, ẩm ướt, cháy nổ do nguồn điện không ổn định,…</li>
                <li><strong>Hình thức bảo hành:</strong> sửa chữa, thay thế hoặc đổi mới tùy theo mức độ hư hỏng và thỏa thuận với khách hàng.</li>
            </ul>
        </div>

        <div class="contentWarrantyReturnPolicy mt-4">
            <h2>2. Chính sách đổi trả</h2>
            <p>Chúng tôi hỗ trợ đổi trả hàng nhằm mang đến trải nghiệm mua sắm an tâm cho khách hàng:</p>
            <ul>
                <li><strong>Thời gian đổi trả:</strong> trong vòng 3 ngày kể từ ngày nhận hàng.</li>
                <li><strong>Điều kiện đổi trả:</strong>
                    <ul>
                        <li>Sản phẩm còn mới, chưa qua sử dụng.</li>
                        <li>Có hóa đơn mua hàng và đầy đủ bao bì, phụ kiện (nếu có).</li>
                        <li>Không áp dụng với các sản phẩm giảm giá hoặc đặt riêng theo yêu cầu.</li>
                    </ul>
                </li>
                <li><strong>Lý do chấp nhận đổi trả:</strong>
                    <ul>
                        <li>Sản phẩm bị lỗi do sản xuất hoặc bị hư hỏng trong quá trình vận chuyển.</li>
                        <li>Sản phẩm giao sai mẫu, sai dịch vụ so với đơn đặt.</li>
                    </ul>
                </li>
                <li><strong>Phí đổi trả:</strong> Miễn phí nếu lỗi do chúng tôi; khách hàng chịu phí vận chuyển trong các trường hợp đổi do thay đổi nhu cầu.</li>
            </ul>
        </div>

        <div class="contentWarrantyReturnPolicy mt-4">
            <h2>3. Quy trình đổi trả/bảo hành</h2>
            <ol>
                <li>Liên hệ bộ phận chăm sóc khách hàng qua số điện thoại hoặc email.</li>
                <li>Cung cấp mã đơn hàng, hình ảnh sản phẩm lỗi hoặc tình trạng cần hỗ trợ.</li>
                <li>Chờ xác nhận từ chúng tôi và tiến hành gửi hàng về địa chỉ quy định.</li>
                <li>Chúng tôi kiểm tra và tiến hành xử lý theo chính sách trong vòng 3–7 ngày làm việc.</li>
            </ol>
        </div>

        <div class="contentWarrantyReturnPolicy mt-4">
            <h2>4. Liên hệ hỗ trợ</h2>
            <p>Nếu có bất kỳ thắc mắc nào liên quan đến chính sách bảo hành và đổi trả, quý khách vui lòng liên hệ:</p>
            <ul>
                <li>Hotline: 0123 456 789</li>
                <li>Email: trandiep490@gmail.com</li>
                <li>Thời gian hỗ trợ: 8:00 – 17:00 (Thứ 2 – Thứ 7)</li>
            </ul>
        </div>
    </section>
</main>


<style>
    #mainNav {
        background-color: #000;
    }
</style>
@endsection

@section('card-footer')
@endsection
