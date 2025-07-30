@extends('layouts.ClientLayout')

@section('title-page')
    Chính sách giao dịch
@endsection

@section('content')
<main class="container py-5">
    <section class="h-custom">
        <div class="mainTradingPolicy mb-4">
            <h1 class="fw-bold text-center">Chính sách giao dịch</h1>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>1. Hình thức giao dịch</h2>
            <p>Khách hàng có thể thực hiện giao dịch thông qua các hình thức sau:</p>
            <ul>
                <li>Đặt lịch và mua hàng trực tuyến trên website</li>
                <li>Gọi điện thoại đến số hotline để đặt lịch</li>
                <li>Đặt hàng trực tiếp tại cửa hàng (nếu có)</li>
            </ul>
            <p>Mỗi giao dịch đều được xác nhận qua email, tin nhắn hoặc điện thoại để đảm bảo tính minh bạch.</p>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>2. Quy trình đặt lịch và mua hàng</h2>
            <p>Quy trình giao dịch bao gồm các bước sau:</p>
            <ol>
                <li>Khách hàng chọn dịch vụ hoặc sản phẩm cần mua</li>
                <li>Điền thông tin cần thiết và gửi yêu cầu đặt lịch/mua hàng</li>
                <li>Hệ thống xác nhận thông tin và gửi phản hồi</li>
                <li>Nhân viên liên hệ để xác nhận lại đơn hàng/lịch hẹn</li>
                <li>Thực hiện giao dịch và cung cấp dịch vụ theo thời gian đã hẹn</li>
            </ol>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>3. Hình thức thanh toán</h2>
            <p>Chúng tôi hỗ trợ nhiều hình thức thanh toán linh hoạt:</p>
            <ul>
                <li>Thanh toán tiền mặt khi nhận hàng hoặc sử dụng dịch vụ</li>
                <li>Thanh toán qua ví điện tử (VNPay)</li>
            </ul>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>4. Chi phí và hóa đơn</h2>
            <p>Toàn bộ chi phí sẽ được thông báo rõ ràng trước khi xác nhận giao dịch. Chúng tôi cung cấp hóa đơn giấy hoặc điện tử theo yêu cầu của khách hàng.</p>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>5. Thay đổi hoặc hủy giao dịch</h2>
            <p>Khách hàng có thể yêu cầu thay đổi hoặc hủy giao dịch trong các trường hợp sau:</p>
            <ul>
                <li>Thay đổi thời gian đặt lịch</li>
                <li>Hủy đơn hàng trước khi giao</li>
            </ul>
            <p>Vui lòng gọi điện thoại thông báo đến số hotline <strong>0123 456 789</strong> hoặc gửi email đến <strong>trandiep490@gmail.com</strong> trước ít nhất <strong>2 giờ</strong> trước thời gian hẹn để chúng tôi hỗ trợ kịp thời.</p>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>6. Trách nhiệm của các bên</h2>
            <p><strong>Trách nhiệm của người bán:</strong></p>
            <ul>
                <li>Cung cấp đầy đủ, chính xác thông tin sản phẩm/dịch vụ</li>
                <li>Thực hiện đúng thời gian, chất lượng như đã cam kết</li>
                <li>Giải quyết thắc mắc và khiếu nại của khách hàng</li>
            </ul>

            <p><strong>Trách nhiệm của khách hàng:</strong></p>
            <ul>
                <li>Cung cấp thông tin chính xác khi đặt lịch/giao dịch</li>
                <li>Thanh toán đúng hạn, đầy đủ theo thỏa thuận</li>
                <li>Tuân thủ quy định và hướng dẫn sử dụng dịch vụ</li>
            </ul>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>7. Giải quyết tranh chấp</h2>
            <p>Trong trường hợp có tranh chấp phát sinh từ giao dịch, các bên sẽ ưu tiên giải quyết bằng thương lượng. Nếu không đạt được thỏa thuận, tranh chấp sẽ được đưa ra giải quyết tại cơ quan có thẩm quyền theo quy định của pháp luật Việt Nam.</p>
        </div>

        <div class="contentTradingPolicy mb-4">
            <h2>8. Thông tin liên hệ</h2>
            <p>Mọi thắc mắc về Chính sách giao dịch vui lòng liên hệ:</p>
            <ul>
                <li><strong>Email:</strong> trandiep490@gmail.com</li>
                <li><strong>Hotline:</strong> 0123 456 789</li>
                <li><strong>Địa chỉ:</strong> Tòa nhà FPT Polytechnic, P. Trịnh Văn Bô, Xuân Phương, Nam Từ Liêm, Hà Nội</li>
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
