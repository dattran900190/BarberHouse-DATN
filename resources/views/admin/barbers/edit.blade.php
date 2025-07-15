@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Thợ Cắt Tóc')

@section('content')
    @php
        $currentRole = Auth::user()->role;
        $isRetired = $barber->status === 'retired';
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3">Thợ Cắt Tóc</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Quản lý nhân sự</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/barbers') }}">Thợ cắt tóc</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chỉnh sửa</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chỉnh sửa Thợ Cắt Tóc</div>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.update', $barber->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Họ tên</label>
                        <input type="text" id="name" name="name" class="form-control"
                            value="{{ old('name', $barber->name) }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="skill_level" class="form-label">Trình độ</label>
                        <input type="text" id="skill_level" name="skill_level" class="form-control"
                            value="{{ old('skill_level', $barber->skill_level) }}">
                        @error('skill_level')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="rating_avg" class="form-label">Đánh giá trung bình</label>
                        <input type="number" id="rating_avg" name="rating_avg" class="form-control" step="0.1"
                            min="0" max="5" value="{{ old('rating_avg', $barber->rating_avg) }}">
                        @error('rating_avg')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="branch_id" class="form-label">Chi nhánh</label>
                        @if ($currentRole === 'admin_branch')
                            <select id="branch_id" name="branch_id" class="form-control" disabled>
                            @else
                                <select id="branch_id" name="branch_id" class="form-control">
                        @endif
                        <option value="">-- Chọn chi nhánh --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ old('branch_id', $barber->branch_id) == $branch->id ? 'selected' : '' }}>
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
                    <textarea id="profile" name="profile" class="form-control" rows="3">{{ old('profile', $barber->profile) }}</textarea>
                    @error('profile')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="avatar" class="form-label">Ảnh đại diện</label>
                    <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*">
                    @if ($barber->avatar)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar" width="120"
                                class="rounded">
                        </div>
                    @endif
                    @error('avatar')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select id="status" name="status" class="form-control" {{ $isRetired ? 'disabled' : '' }}>
                        <option value="idle" {{ old('status', $barber->status) == 'idle' ? 'selected' : '' }}>
                            Đang hoạt động
                        </option>
                        <option value="busy" {{ old('status', $barber->status) == 'busy' ? 'selected' : '' }}>
                            Không nhận lịch
                        </option>
                        <option value="retired" {{ old('status', $barber->status) == 'retired' ? 'selected' : '' }}>
                            Đã nghỉ việc
                        </option>
                    </select>
                    @if ($isRetired)
                        <input type="hidden" name="status" value="retired">
                    @endif
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('barbers.index', ['page' => request('page', 1)]) }}"
                    class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
@endsection
