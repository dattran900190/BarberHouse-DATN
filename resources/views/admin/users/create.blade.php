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

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                        @error('name')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                        @error('email')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" name="password">
                        @error('password')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                        @error('phone')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-control" name="gender">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="avatar" class="form-label">Ảnh đại diện</label>
                        <input type="file" class="form-control" name="avatar" id="avatar" accept="image/*">
                        @error('avatar')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" rows="1">{{ old('address') }}</textarea>
                        @error('address')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" name="status">
                            {{-- <option value="">Chọn trạng thái</option> --}}
                            <option value="active"  {{ old('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            {{-- <option value="banned" {{ old('status') == 'banned' ? 'selected' : '' }}>Bị khóa</option> --}}
                        </select>
                        @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-control" name="role" id="role-select">
                            <option value="">Chọn vai trò</option>
                            @if ($role == 'user')
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                    Người dùng</option>
                            @else
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
                                <option value="admin_branch" {{ old('role') == 'admin_branch' ? 'selected' : '' }}>Quản lý chi nhánh</option>
                            @endif
                        </select>
                        @error('role')<div class="text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <div id="branch-id-group" style="display: none;">
                            <label for="branch_id" class="form-label">Chi nhánh</label>
                            <select class="form-control" name="branch_id" id="branch_id">
                                <option value="">Chọn chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>

                        <div id="points-group" style="display: none;">
                            <label for="points_balance" class="form-label">Số điểm</label>
                            <input type="number" class="form-control" name="points_balance" value="{{ old('points_balance', 0) }}" readonly>
                            @error('points_balance')<div class="text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-plus me-1"></i> Thêm
                    </button>
                    <a href="{{ route('users.index', ['page' => request('page', 1), 'role' => request('role')]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .avatar-img {
            width: 80px !important;
            height: 80px !important;
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .avatar-img {
                width: 60px !important;
                height: 60px !important;
            }
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
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        function toggleFields() {
            const role = document.getElementById('role-select').value;
            const branchField = document.getElementById('branch-id-group');
            const pointsField = document.getElementById('points-group');

            branchField.style.display = (role === 'admin_branch') ? 'block' : 'none';
            pointsField.style.display = (role === 'user') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            document.getElementById('role-select').addEventListener('change', toggleFields);
        });
    </script>
@endsection
