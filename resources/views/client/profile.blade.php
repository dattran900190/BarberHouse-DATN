@extends('layouts.ClientLayout')

@section('title-page')
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
                        <div class="list-group list-group-flush account-settings-links" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="tab-general" data-bs-toggle="list"
                                href="#account-general" role="tab" aria-controls="account-general"
                                data-tab="account-general">
                                Tổng quan
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-password" data-bs-toggle="list"
                                href="#account-change-password" role="tab" aria-controls="account-change-password"
                                data-tab="account-change-password">
                                Đổi mật khẩu
                            </a>
                            <a class="list-group-item list-group-item-action" id="tab-point-history" data-bs-toggle="list"
                                href="#account-point-history" role="tab" aria-controls="account-point-history"
                                data-tab="account-point-history">
                                Lịch sử điểm
                            </a>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="tab-content">
                            <!-- Tổng quan -->
                            <div class="tab-pane fade active show" id="account-general">
                              
                                <div class="card-body pb-2">
                                    @if (session('success-info'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success-info') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <form action="{{ route('client.update') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="tab" value="account-info">
                                        <div class="card-body d-flex align-items-center">
                                            <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : '/default-avatar.png' }}"
                                                alt="Avatar" class="rounded  avatar-preview"
                                                style="width:120px; height:120px; object-fit:cover;">
                                            <div class="ms-4">
                                                <label class="btn btn-outline-primary">
                                                    Tải ảnh mới lên
                                                    <input type="file" name="avatar"
                                                        class="account-settings-fileinput" accept="image/*">
                                                </label>
                                                <button type="button" class="btn btn-default md-btn-flat"
                                                    onclick="resetAvatar(this)">Reset</button>
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
                                            <button type="submit" class="btn-outline-buy">Lưu thay đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Đổi mật khẩu -->
                            <div class="tab-pane fade" id="account-change-password">
                                <div class="card-body pb-2">
                                    @if (session('success-password'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ session('success-password') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                aria-label="Close"></button>
                                        </div>
                                    @endif
                                    <form action="{{ route('client.password') }}" method="POST">
                                        @csrf
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
                                                <input type="password" name="new_password"
                                                    class="form-control input-field" id="new_password"
                                                    placeholder="Nhập mật khẩu mới">
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
                                            <button type="submit" class="btn-outline-buy">Đổi mật khẩu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>                        
                            <!-- Lịch sử điểm -->
                            <div class="tab-pane fade" id="account-point-history">
                                <h4 class="mb-4 fw-bold border-bottom pb-2">Lịch sử điểm</h4>
                                <div class="table-responsive">
                                  <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                      <tr>
                                        <th>Loại</th>
                                        <th>Điểm</th>
                                        <th>Mã</th>
                                        <th>Ngày</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      @forelse($pointHistories as $history)
                                        <tr>
                                          <td>{{ $history->type==='earned'? 'Tích điểm':'Đổi điểm' }}</td>
                                          <td class="fw-bold {{ $history->type==='earned'? 'text-success':'text-danger' }}">
                                            {{ $history->type==='earned'? '+':'-' }}{{ abs($history->points) }}
                                          </td>
                                          <td>
                                            @if($history->type==='earned' && $history->appointment)
                                              #{{ $history->appointment->appointment_code }}
                                            @elseif($history->type==='redeemed' && $history->promotion)
                                              {{ $history->promotion->code }}
                                            @else - @endif
                                          </td>
                                          <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                      @empty
                                        <tr><td colspan="4" class="text-center text-muted">Chưa có lịch sử điểm.</td></tr>
                                      @endforelse
                                    </tbody>
                                  </table>
                                </div>
                                <div class="text-end m-3">
                                  <a href="{{ route('client.redeem') }}" class="btn-outline-buy">Đổi mã giảm giá</a>
                                </div>
                              </div>
                        </div>
                    </div>
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

@section('scripts')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script> --}} --}}
    <script>
        // Xem trước avatar khi chọn file
        document.querySelectorAll('.account-settings-fileinput').forEach(input => {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = input.closest('.card-body').querySelector('.avatar-preview');
                        if (preview) {
                            preview.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Reset ảnh avatar
        function resetAvatar(button) {
            const preview = button.closest('.card-body').querySelector('.avatar-preview');
            const input = button.closest('.card-body').querySelector('.account-settings-fileinput');
            preview.src = '{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : '/default-avatar.png' }}';
            input.value = '';
        }

        // Cập nhật URL khi chuyển tab
        document.querySelectorAll('.account-settings-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                const tab = this.getAttribute('data-tab');
                if (tab) {
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.pushState({}, '', url);
                }
            });
        });

        // Kích hoạt tab dựa trên query parameter khi tải trang
        document.addEventListener("DOMContentLoaded", function() {
            const params = new URLSearchParams(window.location.search);
            const tab = params.get("tab");
            if (tab) {
                document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active', 'show'));
                document.querySelectorAll('.account-settings-links a').forEach(el => el.classList.remove('active'));
                const targetTab = document.querySelector(`a[data-tab="${tab}"]`);
                const targetPane = document.getElementById(tab);
                if (targetTab && targetPane) {
                    targetTab.classList.add('active');
                    targetPane.classList.add('active', 'show');
                }
            }
        });

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

        // Ẩn lỗi khi người dùng nhập lại
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
