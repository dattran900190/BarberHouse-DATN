@extends('layouts.AdminLayout')

@section('title', 'Cài đặt tài khoản')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Cài đặt tài khoản</h3>
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
                <a href="{{ url('admin/profile') }}">Cài đặt tài khoản</a>
            </li>

        </ul>
    </div>
    <div class="card">
        <div class="container py-4">
            <h3 class="mb-4 fw-bold">Cài đặt tài khoản</h3>

            {{-- Thông báo thành công --}}
            @if (session('success-info'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success-info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success-password'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success-password') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @php
                $tab = old('tab') ?? request('tab', 'account-info');
            @endphp


            <ul class="nav nav-tabs mb-3" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'account-info' ? 'active' : '' }}" id="info-tab"
                        data-bs-toggle="tab" data-bs-target="#info" type="button">Thông tin cá nhân</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $tab == 'account-change-password' ? 'active' : '' }}" id="password-tab"
                        data-bs-toggle="tab" data-bs-target="#password" type="button">Đổi mật khẩu</button>
                </li>
            </ul>

            <div class="tab-content" id="profileTabsContent">
                <div class="tab-pane fade {{ $tab == 'account-info' ? 'show active' : '' }}" id="info">
                    <form action="{{ route('admin.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="tab" value="account-info">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Giới tính</label>
                                <select name="gender" class="form-select" @readonly(true)>
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Nam</option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ</option>
                                    <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                value="{{ old('address', $user->address) }}">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($user->avatar)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar"
                                        class="rounded" width="100" height="80">
                                </div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-edit me-1"></i> Cập nhật
                        </button>
                    </form>
                </div>

                {{-- Tab đổi mật khẩu --}}
                <div class="tab-pane fade {{ $tab == 'account-change-password' ? 'show active' : '' }}" id="password">
                    <form action="{{ route('admin.password') }}" method="POST">
                        @csrf
                        @if (old('tab') == 'account-change-password' && $errors->any())
                        @endif


                        {{-- Đảm bảo giữ tab khi submit --}}
                        <input type="hidden" name="tab" value="account-change-password">

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="new_password_confirmation"
                                class="form-control @error('new_password_confirmation') is-invalid @enderror">
                            @error('new_password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-edit me-1"></i> Cập nhật
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
