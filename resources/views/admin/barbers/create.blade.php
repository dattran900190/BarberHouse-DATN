@extends('layouts.AdminLayout')

@section('title', 'Thêm Thợ Cắt Tóc')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thợ cắt tóc</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý chi nhánh</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('barbers.index') }}">Thợ cắt tóc</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Thêm thợ</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Thêm Thợ Cắt Tóc</div>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Họ tên</label>
                        <input type="text" id="name" name="name" class="form-control"
                            placeholder="Nhập họ tên" value="{{ old('name') }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="skill_level" class="form-label">Trình độ</label>
                        <input type="text" id="skill_level" name="skill_level" class="form-control"
                            placeholder="Nhập trình độ" value="{{ old('skill_level') }}">
                        @error('skill_level')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rating_avg" class="form-label">Đánh giá trung bình</label>
                        <input type="number" id="rating_avg" name="rating_avg" class="form-control"
                            step="0.1" min="0" max="5" placeholder="0 - 5"
                            value="{{ old('rating_avg') }}">
                        @error('rating_avg')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="branch_id" class="form-label">Chi nhánh</label>
                        <select id="branch_id" name="branch_id" class="form-control">
                            <option value="">-- Chọn chi nhánh --</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="profile" class="form-label">Hồ sơ</label>
                    <textarea id="profile" name="profile" class="form-control" rows="3"
                        placeholder="Nhập hồ sơ thợ cắt tóc">{{ old('profile') }}</textarea>
                    @error('profile')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="status" value="idle">

                <div class="mb-3">
                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                    @error('avatar')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-plus"></i> <span class="ms-2">Thêm Thợ</span>
                </button>
                <a href="{{ route('barbers.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
@endsection
