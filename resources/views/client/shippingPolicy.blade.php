@extends('layouts.ClientLayout')

@section('title-page')
    Chính sách vận chuyển
@endsection

@section('content')
<main class="container py-5">
    <section class="h-custom">
        <div class="mainShippingPolicy mb-4">
            <h1 class="fw-bold text-center">Chính sách vận chuyển</h1>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>1. Phạm vi áp dụng</h2>
            <p>Chính sách này áp dụng cho tất cả đơn hàng được đặt qua website của chúng tôi, bao gồm giao hàng nội địa và (nếu có) giao hàng quốc tế.</p>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>2. Thời gian xử lý đơn hàng</h2>
            <ul>
                <li><strong>Ngày làm việc:</strong> Thứ 2 – Thứ 7 (8:00 – 18:00).</li>
                <li>Đơn hàng đặt trước 16:00 sẽ được chuyển cho đơn vị vận chuyển trong ngày.</li>
                <li>Đơn hàng đặt sau 16:00 hoặc vào Chủ nhật, ngày lễ sẽ được xử lý vào ngày làm việc kế tiếp.</li>
            </ul>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>3. Phương thức và đối tác vận chuyển</h2>
            <p>Chúng tôi hợp tác với các đơn vị uy tín sau:</p>
            <ul>
                <li>Giao hàng tiết kiệm (GHN, GHTK)</li>
                <li>Viettel Post</li>
                <li>VNPost</li>
            </ul>
            <p>Khách hàng có thể chọn đơn vị vận chuyển phù hợp hoặc để hệ thống tự động gợi ý dựa trên khu vực và chi phí.</p>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>4. Phí vận chuyển</h2>
            <p>Phí vận chuyển được tính dựa trên:</p>
            <ul>
                <li>Cân nặng, kích thước kiện hàng</li>
                <li>Khoảng cách từ kho đến địa chỉ giao hàng</li>
                <li>Hình thức giao nhanh (nếu khách chọn)</li>
            </ul>
            <p>Phí cụ thể sẽ hiển thị khi khách hàng nhập địa chỉ giao nhận trước khi thanh toán.</p>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>5. Thời gian giao hàng</h2>
            <ul>
                <li><strong>Nội thành:</strong> 1–2 ngày làm việc</li>
                <li><strong>Liên tỉnh:</strong> 2–4 ngày làm việc</li>
                <li><strong>Giao nhanh:</strong> 4–8 tiếng (tùy khu vực, áp dụng phí cao hơn)</li>
            </ul>
            <p>Thời gian trên chưa bao gồm thời gian xử lý đơn hàng (mục 2).</p>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>6. Theo dõi đơn hàng</h2>
            <p>Sau khi giao cho đối tác, chúng tôi sẽ gửi <strong>mã vận đơn (tracking number)</strong> qua email. Khách hàng có thể theo dõi trực tiếp trên website của đơn vị vận chuyển.</p>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>7. Trường hợp đặc biệt</h2>
            <ul>
                <li><strong>Hàng cồng kềnh:</strong> Liên hệ hotline để báo giá vận chuyển riêng.</li>
                <li><strong>Khu vực hẻo lánh:</strong> Thời gian giao có thể chậm hơn 1–2 ngày so với dự kiến.</li>
                <li><strong>Ngày lễ, Tết:</strong> Vận chuyển có thể chậm trễ, xin khách hàng thông cảm.</li>
            </ul>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>8. Đổi trả & hoàn phí vận chuyển</h2>
            <p>Trong trường hợp:</p>
            <ul>
                <li>Hàng bị lỗi, vỡ, thiếu: Chúng tôi chịu phí vận chuyển đổi trả và hỗ trợ nhanh nhất.</li>
                <li>Khách hàng từ chối nhận hàng không có lý do chính đáng: Phí ship hai chiều sẽ tính theo quy định.</li>
            </ul>
            <p>Chi tiết xem tại <a href="#">Chính sách đổi trả</a>.</p>
        </div>

        <div class="contentShippingPolicy mb-4">
            <h2>9. Liên hệ hỗ trợ vận chuyển</h2>
            <p>Mọi vấn đề phát sinh trong quá trình giao hàng, xin vui lòng liên hệ:</p>
            <ul>
                <li><strong>Email:</strong> trandiep490@gmail.com</li>
                <li><strong>Hotline:</strong> 0123 456 789</li>
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
