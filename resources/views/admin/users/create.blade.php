@extends('layouts.AdminLayout')

@section('title', 'Thêm ' . ($role == 'user' ? 'Người dùng' : 'Quản trị viên') . ' mới')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">{{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/dashboard') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
             <li class="nav-item">
                <a href="{{ route('users.index') }}">Quản lý người dùng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Thêm mới</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Thêm {{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</div>
        </div>

        <div class="card-body">
            <form action="{{ route('users.store', ['role' => $role]) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email') }}">
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="password" name="password">
                        @error('password')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            value="{{ old('phone') }}">
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-control" id="gender" name="gender">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="avatar" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                        @error('avatar')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control" id="address" name="address">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-control" id="role" name="role">
                            <option value="">Chọn vai trò</option>
                            @if ($role == 'user')
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Người dùng</option>
                            @else
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                <option value="admin_branch" {{ old('role') == 'admin_branch' ? 'selected' : '' }}>Quản lý chi nhánh</option>
                            @endif
                        </select>
                        @error('role')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Chọn trạng thái</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tạm khóa</option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @if ($role === 'admin')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="branch_id" class="form-label">Chi nhánh</label>
                            <select class="form-control" id="branch_id" name="branch_id">
                                <option value="">Chọn chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="text-danger">{{ $errors->first('branch_id') }}</div>
                            @enderror

                        </div>
                    </div>
                @endif

                <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-plus"></i> <span class="ms-2">Thêm</span>
                </button>
                <a href="{{ route('users.index', ['role' => $role]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
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

        .equal-height-columns>.col-md-6 {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            min-height: 100%;
        }

        .equal-height-columns>.col-md-6>.mb-3 {
            flex: 0 1 auto;
        }

        .equal-height-columns>.col-md-6>.flex-fill {
            flex: 1 1 auto;
        }

        .form-control,
        .form-control-file,
        .form-control textarea {
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

            .form-control,
            .form-control-file,
            .form-control textarea {
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

            .avatar-placeholder,
            .avatar-img {
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
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
