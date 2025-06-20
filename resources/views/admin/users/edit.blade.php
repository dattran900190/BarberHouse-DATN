@extends('adminlte::page')

@section('title', 'Chỉnh sửa ' . ($role == 'user' ? 'Người dùng' : 'Quản trị viên'))

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h3 class="card-title mb-0">Chỉnh sửa {{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('users.update', ['user' => $user->id, 'role' => $role]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">
                <div class="row equal-height-columns">
                    <div class="col-md-6 col-12 d-flex flex-column">
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" readonly>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" readonly>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" readonly>
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="points_balance" class="form-label">Số điểm</label>
                            <input type="number" class="form-control" id="points_balance" name="points_balance" value="{{ old('points_balance', $user->points_balance) }}" min="0" readonly>
                            @error('points_balance')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 flex-fill">
                            <label for="gender" class="form-label">Giới tính</label>
                            <select class="form-control" id="gender" name="gender" readonly>
                                <option value="">Chọn giới tính</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                            @error('gender')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 col-12 d-flex flex-column">
                        <div class="mb-3">
                            <label for="avatar" class="form-label">Ảnh đại diện</label>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                {{-- <input type="file" class="form-control flex-grow-1" id="avatar" name="avatar" accept="image/*"> --}}
                                <div class="avatar-container">
                                    @if ($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Current Avatar" class="rounded-circle img-fluid avatar-img" id="avatar-preview">
                                    @else
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center avatar-placeholder" id="avatar-preview">
                                            <span class="text-white">N/A</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted mt-1">Để trống nếu không muốn thay đổi ảnh hiện tại.</p>
                            @error('avatar')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 flex-fill">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" disabled>{{ old('address', $user->address) }} </textarea>
                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Vai trò</label>
                            <select class="form-control" id="role" name="role">
                                <option value="">Chọn vai trò</option>
                                @if ($role == 'user')
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                                @else
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="admin_branch" {{ old('role', $user->role) == 'admin_branch' ? 'selected' : '' }}>Admin Branch</option>
                                    <option value="super_admin" {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                @endif
                            </select>
                            @error('role')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Chọn trạng thái</option>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Banned</option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('users.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                        lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .card {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        .equal-height-columns {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
        }

        .equal-height-columns > .col-md-6 {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            min-height: 100%;
        }

        .equal-height-columns > .col-md-6 > .mb-3 {
            flex: 0 1 auto;
        }

        .equal-height-columns > .col-md-6 > .flex-fill {
            flex: 1 1 auto;
        }

        .form-control, .form-control-file, .form-control textarea {
            font-size: 0.9rem;
            width: 100%;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        .avatar-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            display: block;
        }

        .avatar-placeholder {
            width: 80px;
            height: 80px;
            font-size: 0.9rem;
            text-align: center;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #6c757d;
        }

        .avatar-placeholder span {
            padding: 0 10px;
            white-space: normal;
            line-height: 1.5;
        }

        .avatar-container {
            flex-shrink: 0;
        }

        .text-danger {
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .d-flex.gap-2 {
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .equal-height-columns {
                display: block;
            }

            .col-md-6 {
                margin-bottom: 1rem;
            }

            .form-control, .form-control-file, .form-control textarea {
                font-size: 0.8rem;
            }

            .d-flex.gap-2 {
                flex-direction: column;
                align-items: stretch;
            }

            .d-flex.gap-2 .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            textarea.form-control {
                min-height: 100px;
            }

            .avatar-placeholder, .avatar-img {
                width: 60px;
                height: 60px;
                font-size: 0.8rem;
            }

            .avatar-placeholder span {
                line-height: 1.5;
            }

            .d-flex.align-items-center.gap-3 {
                flex-direction: column;
                align-items: stretch;
                gap: 0.5rem;
            }

            .avatar-container {
                text-align: center;
            }
        }

        .card-body {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endsection

@section('js')
    <script>
        document.getElementById('avatar').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('avatar-preview');

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview.classList.contains('avatar-placeholder')) {
                        const img = document.createElement('img');
                        img.id = 'avatar-preview';
                        img.className = 'rounded-circle img-fluid avatar-img';
                        img.src = e.target.result;
                        preview.parentNode.replaceChild(img, preview);
                    } else {
                        preview.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);;
            }
        });
    </script>
@endsection