@extends('layouts.ClientLayout')

@section('title-page')
    Chính sách bảo mật
@endsection

@section('content')
<main class="container py-5">
    <section class="h-custom">
        <div class="mainPrivacyPolicy mb-4">
            <h1 class="fw-bold text-center">Chính sách bảo mật thông tin khách hàng</h1>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>1. Mục đích và phạm vi thu thập</h2>
            <p>Website của chúng tôi thu thập thông tin cá nhân của khách hàng nhằm phục vụ các mục đích:</p>
            <ul>
                <li>Hỗ trợ khách hàng trong quá trình đặt lịch và mua hàng</li>
                <li>Giao hàng, cung cấp dịch vụ và xác nhận thông tin đơn hàng</li>
                <li>Liên hệ giải quyết các vấn đề phát sinh liên quan đến đơn hàng</li>
                <li>Gửi thông tin khuyến mãi, chăm sóc khách hàng (nếu được cho phép)</li>
                <li>Nâng cao chất lượng dịch vụ và cải thiện trải nghiệm người dùng</li>
            </ul>
            <p>Các thông tin được thu thập bao gồm: họ tên, số điện thoại, email, địa chỉ, thông tin đăng nhập, lịch sử giao dịch và phản hồi của khách hàng.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>2. Thu thập tự động và cookie</h2>
            <p>Chúng tôi sử dụng cookie và các công nghệ theo dõi để thu thập dữ liệu về hành vi duyệt web của khách hàng như:</p>
            <ul>
                <li>Trang đã xem, thời gian truy cập, khu vực địa lý</li>
                <li>Thiết bị, trình duyệt và hệ điều hành</li>
            </ul>
            <p>Thông tin này giúp chúng tôi hiểu rõ hơn về nhu cầu khách hàng và tối ưu trải nghiệm sử dụng. Khách hàng có thể điều chỉnh cài đặt trình duyệt để từ chối cookie nếu muốn.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>3. Phạm vi sử dụng thông tin</h2>
            <p>Chúng tôi chỉ sử dụng thông tin cá nhân trong nội bộ hệ thống và cho các mục đích:</p>
            <ul>
                <li>Quản lý tài khoản, đơn hàng, lịch đặt của khách hàng</li>
                <li>Gửi email/SMS xác nhận đơn hàng, thông báo lịch hẹn</li>
                <li>Chăm sóc khách hàng sau bán</li>
                <li>Phân tích xu hướng mua sắm để cải thiện dịch vụ</li>
                <li>Gửi bản tin, chương trình khuyến mãi (nếu khách hàng đăng ký)</li>
            </ul>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>4. Chia sẻ thông tin với bên thứ ba</h2>
            <p>Chúng tôi **cam kết không bán hoặc chia sẻ thông tin cá nhân cho bên thứ ba**, ngoại trừ các trường hợp sau:</p>
            <ul>
                <li>Đối tác giao hàng, thanh toán để hoàn thành đơn hàng</li>
                <li>Yêu cầu của cơ quan pháp luật có thẩm quyền</li>
                <li>Đối tác quảng cáo, phân tích dữ liệu nhưng không bao gồm thông tin định danh cá nhân</li>
            </ul>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>5. Bảo mật thông tin cá nhân</h2>
            <p>Chúng tôi áp dụng nhiều biện pháp bảo mật để bảo vệ thông tin khách hàng khỏi truy cập trái phép:</p>
            <ul>
                <li>Dữ liệu truyền qua giao thức bảo mật HTTPS (SSL)</li>
                <li>Mã hóa thông tin lưu trữ trên máy chủ</li>
                <li>Giới hạn truy cập nội bộ chỉ cho nhân sự được phân quyền</li>
                <li>Sao lưu dữ liệu định kỳ</li>
            </ul>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>6. Thời gian lưu trữ thông tin</h2>
            <p>Thông tin khách hàng được lưu trữ trong hệ thống cho đến khi khách hàng yêu cầu xóa hoặc khi chúng tôi không còn cần sử dụng cho các mục đích đã nêu ở trên.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>7. Quyền của khách hàng</h2>
            <p>Khách hàng có quyền:</p>
            <ul>
                <li>Yêu cầu xem, chỉnh sửa hoặc xóa thông tin cá nhân</li>
                <li>Rút lại sự đồng ý về việc sử dụng thông tin</li>
                <li>Gửi khiếu nại về việc lạm dụng thông tin</li>
            </ul>
            <p>Vui lòng gửi yêu cầu qua email hoặc hotline. Chúng tôi sẽ phản hồi trong vòng 48 giờ làm việc.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>8. Bảo vệ thông tin trẻ em</h2>
            <p>Website không cố ý thu thập dữ liệu từ người dùng dưới 16 tuổi. Nếu phát hiện thông tin của trẻ em dưới độ tuổi này được cung cấp không có sự cho phép từ người giám hộ, chúng tôi sẽ xóa ngay lập tức.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>9. Tuân thủ pháp luật</h2>
            <p>Chúng tôi cam kết tuân thủ đầy đủ các quy định pháp luật về bảo vệ thông tin cá nhân theo Luật An ninh mạng, Luật Bảo vệ quyền lợi người tiêu dùng và các văn bản liên quan tại Việt Nam.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>10. Thay đổi chính sách bảo mật</h2>
            <p>Chính sách này có thể được thay đổi để phù hợp với các điều chỉnh trong pháp luật hoặc thay đổi trong hoạt động kinh doanh. Mọi thay đổi sẽ được thông báo tại trang này hoặc qua email nếu cần thiết.</p>
        </div>

        <div class="contentPrivacyPolicy mb-4">
            <h2>11. Thông tin liên hệ</h2>
            <p>Để được hỗ trợ hoặc có thắc mắc liên quan đến Chính sách bảo mật, xin vui lòng liên hệ:</p>
            <ul>
                <li><strong>Email:</strong> trandiep490@gmail.com</li>
                <li><strong>Hotline:</strong> 0123 456 789</li>
                <li><strong>Địa chỉ:</strong> Tòa nhà FPT Polytechnic, P. Trịnh Văn Bô, Xuân Phương, Nam Từ Liêm, Hà Nội

                </li>
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
