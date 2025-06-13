@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Cài đặt tài khoản
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container light-style flex-grow-1 container-p-y">


            <div class="card overflow-hidden">
                <h4 class="font-weight-bold text-center py-3 mb-4">
                    Cài đặt tài khoản
                </h4>
                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        {{-- Thêm role="tablist" --}}
                        <div class="list-group list-group-flush account-settings-links" role="tablist">
                            {{-- Chú ý: data-bs-toggle, role="tab", aria-controls và id cho liên kết --}}
                            <a class="list-group-item list-group-item-action active" id="tab-general" data-bs-toggle="list"
                                href="#account-general" role="tab" aria-controls="account-general">
                                Tổng quan
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-password" data-bs-toggle="list"
                                href="#account-change-password" role="tab" aria-controls="account-change-password">
                                Đổi mật khẩu
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-info" data-bs-toggle="list"
                                href="#account-info" role="tab" aria-controls="account-info">
                                Thông tin
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-notifications" data-bs-toggle="list"
                                href="#account-notifications" role="tab" aria-controls="account-notifications">
                                Thông báo
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-point-history" data-bs-toggle="list"
                                href="#account-point-history" role="tab" aria-controls="account-point-history">
                                Lịch sử điểm
                            </a>

                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="account-general">

                                <div class="card-body d-flex align-items-center">
                                    {{-- Avatar --}}
                                    <img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="Avatar"
                                        class="rounded-circle" style="width:80px; height:80px; object-fit:cover;">

                                    {{-- Nội dung bên phải avatar --}}
                                    <div class="ms-4">
                                        <label class="btn btn-outline-primary">
                                            Tải ảnh mới lên
                                            <input type="file" class="account-settings-fileinput" hidden>
                                        </label>
                                        <button type="button" class="btn btn-default md-btn-flat">Reset</button>

                                        <div class="text-dark small mt-1">
                                            Cho phép JPG, GIF or PNG.
                                        </div>
                                    </div>
                                </div>

                                <hr class="border-light m-0">

                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="form-label">Tên tài khoản</label>
                                        <input type="text" class="form-control mb-1" value="nmaxwell">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Tên người dùng</label>
                                        <input type="text" class="form-control" value="Nelle Maxwell">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control mb-1" value="nmaxwell@mail.com">
                                        <div class="alert alert-warning mt-3">
                                            Your email is not confirmed. Please check your inbox.<br>
                                            <a href="javascript:void(0)">Resend confirmation</a>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="tab-pane fade" id="account-change-password">
                                <div class="card-body pb-2">

                                    <div class="form-group">
                                        <label class="form-label">Mật khẩu cũ</label>
                                        <input type="password" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Nhập lại mật khẩu mới</label>
                                        <input type="password" class="form-control">
                                    </div>

                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-info">
                                <div class="card-body pb-2">

                                    <div class="form-group">
                                        <label class="form-label">Số điện thoại</label>
                                        <input type="text" class="form-control" value="+0 (123) 456 7891">
                                    </div>


                                    <div class="form-group">
                                        <label class="form-label">Địa chỉ</label>
                                        <textarea class="form-control" rows="5">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris nunc arcu, dignissim sit amet sollicitudin iaculis, vehicula id urna. Sed luctus urna nunc. Donec fermentum, magna sit amet rutrum pretium, turpis dolor molestie diam, ut lacinia diam risus eleifend sapien. Curabitur ac nibh nulla. Maecenas nec augue placerat, viverra tellus non, pulvinar risus.</textarea>
                                    </div>
                                    <div class="form-group">
                                        {{-- ko sửa đc điểm tích luỹ --}}
                                        <label class="form-label">Điểm tích luỹ</label>
                                        <input type="text" class="form-control" value="63">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Giới tính</label>
                                        <select class="form-select">
                                            <option selected="">Nam</option>
                                            <option>Nữ</option>
                                            <option>Khác</option>
                                        </select>
                                    </div>


                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-notifications">
                                <div class="card-body pb-2">

                                    <h6 class="mb-4">Hoạt động</h6>

                                    <div class="form-group">
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher-input" checked="">
                                            <span class="switcher-indicator">
                                                <span class="switcher-yes"></span>
                                                <span class="switcher-no"></span>
                                            </span>
                                            <span class="switcher-label">Gửi email cho tôi khi ai đó bình luận về bài viết
                                                của tôi</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label class="switcher">
                                            <input type="checkbox" class="switcher-input" checked="">
                                            <span class="switcher-indicator">
                                                <span class="switcher-yes"></span>
                                                <span class="switcher-no"></span>
                                            </span>
                                            <span class="switcher-label">Gửi email cho tôi khi ai đó trả lời trên chủ đề
                                                diễn đàn của tôi</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="account-point-history" role="tabpanel">
                                <div class="card-body pb-2">
                                    <h3 class="mb-4">Lịch sử điểm</h3>
                                    <p><strong>Số điểm hiện có:</strong> {{ $user->points_balance }} điểm</p>
                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif


                                    @if ($pointHistories->isEmpty())
                                        <p>Chưa có lịch sử điểm nào.</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Loại</th>
                                                        <th>Điểm</th>
                                                        <th>Mã giảm giá</th>
                                                        <th>Ngày</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pointHistories as $history)
                                                        <tr>
                                                            <td>{{ $history->type === 'earned' ? 'Tích điểm' : 'Đổi điểm' }}
                                                            </td>
                                                            <td>{{ $history->points }} điểm</td> 
                                                            <td>{{ $history->promotion->code ?? '-' }}</td>
                                                            <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                    {{-- Nút chuyển sang trang đổi mã giảm giá --}}
                                    <div class="text-end mb-3">
                                        <a href="{{ route('client.redeem') }}" class="btn btn-primary">
                                            Đổi mã giảm giá
                                        </a>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right mt-3">
                <button type="button" class="btn btn-primary">Lưu thay đổi</button>&nbsp;
                <button type="button" class="btn btn-default">Huỷ</button>
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
    {{-- {{ $sanPhams->links() }} --}}
@endsection

<script>
    const nav = document.getElementById("mainNav");

    window.addEventListener("scroll", () => {
        if (window.scrollY = 100) {
            nav.classList.add("scrolled");
        } else {
            nav.classList.remove("scrolled");
        }
    });

    const icon = document.getElementById("search-icon");
    const overlay = document.getElementById("search-overlay");
    const closeBtn = document.querySelector(".close-btn");
    if (icon && overlay) {
        icon.addEventListener("click", e => {
            e.preventDefault();
            overlay.style.display = "flex";
        });
        // đóng
        closeBtn?.addEventListener("click", () => overlay.style.display = "none");
        overlay.addEventListener("click", e => {
            if (!e.target.closest(".search-content")) overlay.style.display = "none";
        });
        document.addEventListener("keydown", e => {
            if (e.key === "Escape") overlay.style.display = "none";
        });
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get("tab");

        if (tab) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active', 'show'));
            document.querySelectorAll('.account-settings-links a').forEach(el => el.classList.remove('active'));

            const targetTab = document.querySelector(`a[href="#${tab}"]`);
            const targetPane = document.getElementById(tab);

            if (targetTab && targetPane) {
                targetTab.classList.add('active');
                targetPane.classList.add('active', 'show');
            }
        }
    });
</script>
