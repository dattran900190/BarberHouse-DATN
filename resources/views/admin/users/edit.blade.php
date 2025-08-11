@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa ' . ($role == 'user' ? 'Người dùng' : 'Quản trị viên'))

@section('content')
    @php
        $isEditingSelf = Auth::id() === $user->id;
        $isAdmin = Auth::user()->role === 'admin';
        $isEditingAdminBranch = $isAdmin && $user->role === 'admin_branch' && !$isEditingSelf;
          $canEditStatus = $isAdmin && !$isEditingSelf && in_array($user->role, ['user', 'admin_branch']);
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">{{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
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
                <a href="{{ route('users.index') }}">Quản lý chung</a>
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
                <a href="{{ url('admin/users/' . $user->id . '/edit') }}">Sửa người dùng</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Sửa {{ $role == 'user' ? 'người dùng' : 'quản trị viên' }}</div>
        </div>

        <div class="card-body">
            <form action="{{ route('users.update', ['user' => $user->id, 'role' => $role]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                {{-- Hàng 1: Họ tên và Email (chỉ chỉnh sửa nếu là chính mình) --}}

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $user->name) }}"
                            {{ $isEditingSelf && $isAdmin ? '' : 'readonly' }}>

                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control" name="email" value="{{ old('email', $user->email) }}"
                            readonly>
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $user->phone) }}"
                            {{ $isEditingSelf && $isAdmin ? '' : 'readonly' }}>

                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="gender" class="form-label">Giới tính</label>
                        <select class="form-control readonly-select" name="gender" disabled>
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam
                            </option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ
                            </option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác
                            </option>
                        </select>
                        <input type="hidden" name="gender" value="{{ old('gender', $user->gender) }}">

                        @if (!$isEditingSelf)
                            <style>
                                .readonly-select {
                                    pointer-events: none;
                                    /* Không cho người dùng tương tác */
                                    background-color: #e9ecef;
                                    /* Màu nền giống input readonly */
                                    color: #495057;
                                }

                                .readonly-select option {
                                    background-color: white;
                                    /* Đảm bảo text đọc được */
                                }
                            </style>
                        @endif



                        {{-- Nếu bị disable thì thêm hidden input --}}
                        @if (!$isEditingSelf)
                            <input type="hidden" name="gender_hidden" value="{{ old('gender', $user->gender) }}">
                        @endif

                        @error('gender')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>


                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" {{ $isEditingSelf && $isAdmin ? '' : 'readonly' }}>{{ old('address', $user->address) }}</textarea>

                        @error('address')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-control" name="status" {{ $canEditStatus  ? '' : 'disabled' }}>
                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Đang
                                hoạt động</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Chưa kích hoạt</option>
                            <option value="banned" {{ old('status', $user->status) == 'banned' ? 'selected' : '' }}>Đã chặn
                            </option>
                        </select>

                        @if (!$canEditStatus )
                            <input type="hidden" name="status" value="{{ old('status', $user->status) }}">
                        @endif

                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Hàng 4: Vai trò và Chi nhánh/Số điểm --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="role" class="form-label">Vai trò</label>
                        <select class="form-control" name="role" disabled>
                            <option value="">Chọn vai trò</option>
                            @if ($role == 'user')
                                <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Người
                                    dùng</option>
                            @else
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Quản
                                    trị
                                    viên</option>
                                <option value="admin_branch"
                                    {{ old('role', $user->role) == 'admin_branch' ? 'selected' : '' }}>Quản lý chi nhánh
                                </option>
                            @endif
                        </select>
                        <input type="hidden" name="role" value="{{ old('role', $user->role) }}">
                        @error('role')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        {{-- Chi nhánh (hiện khi role là admin_branch và chỉ chỉnh sửa nếu là chính mình) --}}
                        <div id="branch-id-group"
                            style="display: {{ $user->role == 'admin_branch' ? 'block' : 'none' }};">
                            <label for="branch_id" class="form-label">Chi nhánh</label>
                            <select name="branch_id" id="branch_id" class="form-control">
                                <option value="">Chọn chi nhánh</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}</option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Số điểm (chỉ hiện nếu là user, và chỉ đọc) --}}
                        @if ($role == 'user')
                            <label for="points_balance" class="form-label">Số điểm</label>
                            <input type="number" class="form-control" name="points_balance"
                                value="{{ old('points_balance', $user->points_balance) }}" readonly>
                        @endif
                        @error('points_balance')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nút --}}
                <div class="d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-edit me-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('users.index', ['page' => request('page', 1), 'role' => request('role')]) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .form-label {
            font-weight: 600;
        }

        textarea.form-control {
            resize: none;
            min-height: 38px;
        }

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
        function toggleBranchField() {
            const role = document.querySelector('select[name="role"]').value;
            const branchField = document.getElementById('branch-id-group');
            if (role === 'admin_branch') {
                branchField.style.display = 'block';
            } else {
                branchField.style.display = 'none';
            }
        }

        // Gọi khi trang vừa load
        document.addEventListener('DOMContentLoaded', function() {
            toggleBranchField();
            document.querySelector('select[name="role"]').addEventListener('change', toggleBranchField);
        });
    </script>
@endsection
