@extends('layouts.ClientLayout')

@section('title-page')
    Cài đặt tài khoản
@endsection

@section('content')
    <main style="padding: 10%; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
        <div class="container light-style flex-grow-1 container-p-y">
            <div class="card overflow-hidden shadow-lg border-0 rounded-4 animate__animated animate__fadeIn">
                <h4 class="font-weight-bold text-center py-3 mb-4 bg-white text-dark">
                    Cài đặt tài khoản
                </h4>
                <div class="row no-gutters row-bordered row-border-light">
                    <div class="col-md-3 pt-0">
                        <div class="list-group list-group-flush account-settings-links" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="tab-general" data-bs-toggle="list"
                                href="#account-general" role="tab" aria-controls="account-general">
                                <i class="bi bi-person-circle me-2"></i> Tổng quan
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-password" data-bs-toggle="list"
                                href="#account-change-password" role="tab" aria-controls="account-change-password">
                                <i class="bi bi-lock-fill me-2"></i> Đổi mật khẩu
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-info" data-bs-toggle="list"
                                href="#account-info" role="tab" aria-controls="account-info">
                                <i class="bi bi-info-circle-fill me-2"></i> Thông tin
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-notifications" data-bs-toggle="list"
                                href="#account-notifications" role="tab" aria-controls="account-notifications">
                                <i class="bi bi-bell-fill me-2"></i> Thông báo
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-point-history" data-bs-toggle="list"
                                href="#account-point-history" role="tab" aria-controls="account-point-history">
                                <i class="bi bi-clock-history me-2"></i> Lịch sử điểm
                            </a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="account-general">
                                <div class="card-body d-flex align-items-center">

                                </div>
                                <hr class="border-light m-0">
                                <div class="card-body">
                                    <div class="form-group mb-3">
                                        <label class="form-label fw-bold">Tên tài khoản (Email)</label>
                                        <input type="text" class="form-control rounded-3"
                                            value="{{ auth()->user()->email }}" disabled>
                                    </div>
                                    <form action="{{ route('client.account.update') }}" method="POST">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Tên người dùng</label>
                                            <input type="text" name="name" class="form-control rounded-3"
                                                value="{{ auth()->user()->name }}" disabled>
                                        </div>
                                        <div class="text-end mt-3">

                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-change-password">
                                <div class="card-body pb-2">
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <form action="{{ route('client.account.password') }}" method="POST">
                                        @csrf
                                        <!-- Thêm trường ẩn để lưu tab hiện tại -->
                                        <input type="hidden" name="tab" value="account-change-password">
                                        <div class="form-group mb-3 position-relative">
                                            <label class="form-label fw-bold">Mật khẩu cũ</label>
                                            <div style="position: relative;">
                                                <input type="password" name="current_password"
                                                    class="form-control input-field" id="current_password"
                                                    placeholder="Nhập mật khẩu cũ">
                                                <span class="toggle-password" data-target="current_password"
                                                    style="position:absolute; right:10px; top:50%; transform: translateY(-50%); cursor:pointer;">
                                                    <i class="fa-solid fa-eye" id="icon_current_password"></i>
                                                </span>
                                            </div>
                                            @error('current_password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3 position-relative">
                                            <label class="form-label fw-bold">Mật khẩu mới</label>
                                            <div style="position: relative;">
                                                <input type="password" name="new_password" class="form-control input-field"
                                                    id="new_password" placeholder="Nhập mật khẩu mới">
                                                <span class="toggle-password" data-target="new_password"
                                                    style="position:absolute; right:10px; top:50%; transform: translateY(-50%); cursor:pointer;">
                                                    <i class="fa-solid fa-eye" id="icon_new_password"></i>
                                                </span>
                                            </div>
                                            @error('new_password')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3 position-relative">
                                            <label class="form-label fw-bold">Nhập lại mật khẩu mới</label>
                                            <div style="position: relative;">
                                                <input type="password" name="new_password_confirmation"
                                                    class="form-control input-field" id="new_password_confirmation"
                                                    placeholder="Nhập lại mật khẩu">
                                                <span class="toggle-password" data-target="new_password_confirmation"
                                                    style="position:absolute; right:10px; top:50%; transform: translateY(-50%); cursor:pointer;">
                                                    <i class="fa-solid fa-eye" id="icon_new_password_confirmation"></i>
                                                </span>
                                            </div>
                                            @error('new_password_confirmation')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary rounded-pill">Đổi mật
                                                khẩu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="account-info">
                                <div class="card-body pb-2">
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <form action="{{ route('client.account.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <!-- Thêm trường ẩn để lưu tab hiện tại -->
                                        <input type="hidden" name="tab" value="account-info">
                                        <div class="card-body d-flex align-items-center">
                                            <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : '/' }}"
                                                alt="Avatar" class="rounded-circle" id="avatar-preview"
                                                style="width:80px; height:80px; object-fit:cover;">
                                            <div class="ms-4">
                                                <label class="btn btn-outline-primary">
                                                    Tải ảnh mới lên
                                                    <input type="file" name="avatar"
                                                        class="account-settings-fileinput" accept="image/*">
                                                </label>
                                                <button type="button" class="btn btn-default md-btn-flat"
                                                    onclick="resetAvatar()">Reset</button>
                                                <div class="text-dark small mt-1">Cho phép JPG, GIF hoặc PNG. Kích thước
                                                    tối đa 2MB.</div>
                                                @error('avatar')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Tên người dùng</label>
                                            <input type="text" name="name" class="form-control rounded-3"
                                                value="{{ old('name', auth()->user()->name) }}">
                                            @error('name')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Số điện thoại</label>
                                            <input type="text" name="phone" class="form-control rounded-3"
                                                value="{{ old('phone', auth()->user()->phone) }}">
                                            @error('phone')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Địa chỉ</label>
                                            <textarea name="address" class="form-control rounded-3" rows="5">{{ old('address', auth()->user()->address) }}</textarea>
                                            @error('address')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Giới tính</label>
                                            <select name="gender" class="form-select rounded-3">
                                                <option value="male"
                                                    {{ old('gender', auth()->user()->gender) === 'male' ? 'selected' : '' }}>
                                                    Nam</option>
                                                <option value="female"
                                                    {{ old('gender', auth()->user()->gender) === 'female' ? 'selected' : '' }}>
                                                    Nữ</option>
                                                <option value="other"
                                                    {{ old('gender', auth()->user()->gender) === 'other' ? 'selected' : '' }}>
                                                    Khác</option>
                                            </select>
                                            @error('gender')
                                                <div class="text-danger small">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Điểm tích lũy</label>
                                            <input type="text" class="form-control rounded-3"
                                                value="{{ auth()->user()->points_balance }}" disabled>
                                        </div>
                                        <div class="text-end mt-3">
                                            <button type="submit" class="btn btn-primary rounded-pill">Lưu thay
                                                đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="account-notifications">
                            <div class="card-body pb-2">
                                <h6 class="mb-4 fw-bold">Hoạt động</h6>
                                <div class="form-group mb-3">
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher-input" checked>
                                        <span class="switcher-indicator">
                                            <span class="switcher-yes"></span>
                                            <span class="switcher-no"></span>
                                        </span>
                                        <span class="switcher-label">Gửi email cho tôi khi ai đó bình luận về bài viết
                                            của tôi</span>
                                    </label>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="switcher">
                                        <input type="checkbox" class="switcher-input" checked>
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
                                <h6 class="mb-4 fw-bold">Lịch sử điểm</h6>
                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endif
                                @if ($pointHistories->isEmpty())
                                    <p class="text-muted">Chưa có lịch sử điểm nào.</p>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-primary">
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
                                                        <td>{{ $history->points }}</td>
                                                        <td>{{ $history->promotion->code ?? '-' }}</td>
                                                        <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                <div class="text-end mb-3">
                                    <a href="{{ route('client.redeem') }}" class="btn btn-primary rounded-pill">
                                        Đổi mã giảm giá
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
    <style>
        /* Custom Styles */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .list-group-item {
            border: none;
            padding: 15px 20px;
            transition: background-color 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #e9ecef;
        }

        .list-group-item.active {
            background-color: #007bff !important;
            color: white !important;
        }

        .form-control,
        .form-select,
        textarea {
            border-radius: 8px;
            box-shadow: none;
            transition: border-color 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 8px 20px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-outline-primary {
            border-color: #007bff;
            color: #007bff;
        }

        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .tab-pane {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .switcher-input:checked+.switcher-indicator .switcher-yes {
            background-color: #007bff;
        }

        .switcher-indicator {
            border: 1px solid #ddd;
        }

        #mainNav {
            background-color: #000;
            transition: background-color 0.3s ease;
        }

        #mainNav.scrolled {
            background-color: #1a1a1a !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
    </style>
@endsection

@section('card-footer')
    {{-- {{ $sanPhams->links() }} --}}
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xem trước avatar khi chọn file
        document.querySelector('.account-settings-fileinput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('#avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Reset ảnh avatar
        function resetAvatar() {
            document.querySelector('#avatar-preview').src = '/';
            document.querySelector('.account-settings-fileinput').value = '';
        }

        // Kích hoạt tab dựa trên query parameter
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

        // Các script khác (giữ nguyên)
        const nav = document.getElementById("mainNav");
        if (nav) {
            window.addEventListener("scroll", () => {
                if (window.scrollY >= 100) {
                    nav.classList.add("scrolled");
                } else {
                    nav.classList.remove("scrolled");
                }
            });
        }
        // Hiển thị/ẩn mật khẩu
       document.querySelectorAll('.toggle-password').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const passwordInput = document.getElementById(targetId);
            const icon = document.getElementById('icon_' + targetId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Ẩn lỗi khi người dùng bắt đầu gõ lại
    document.querySelectorAll('.input-field').forEach(function(input) {
        input.addEventListener('input', function() {
            const errorId = 'error-' + input.id;
            const error = document.getElementById(errorId);
            if (error) {
                error.style.display = 'none';
            }
        });
    });
    </script>
@endsection
