@extends('layouts.AdminLayout')

@section('title', 'Thêm Thợ Cắt Tóc')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">Thêm Thợ Cắt Tóc</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="name">Họ tên</label>
                    <input type="text" id="name" name="name" class="form-control"
                        placeholder="Nhập tên thợ cắt tóc" value="{{ old('name') }}">
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="skill_level">Trình độ</label>
                    <input type="text" id="skill_level" name="skill_level" class="form-control"
                        placeholder="Nhập trình độ thợ" value="{{ old('skill_level') }}">
                    @error('skill_level')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="rating_avg">Đánh giá trung bình</label>
                    <input type="number" id="rating_avg" name="rating_avg" class="form-control"
                        placeholder="Nhập đánh giá trung bình" step="0.1" min="0" max="5"
                        value="{{ old('rating_avg') }}">
                    @error('rating_avg')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="profile">Hồ sơ</label>
                    <textarea id="profile" name="profile" class="form-control" placeholder="Nhập hồ sơ thợ cắt tóc" rows="3">{{ old('profile') }}</textarea>
                    @error('profile')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="branch_id">Chi nhánh</label>
                    <select id="branch_id" name="branch_id" class="form-control">
                        <option value="">-- Chọn chi nhánh --</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <input type="hidden" name="status" value="idle">

                <div class="form-group">
                    <label for="avatar">Ảnh đại diện</label>
                    <input type="file" id="avatar" name="avatar" class="form-control-file" accept="image/*">
                    @error('avatar')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Thêm Thợ</button>
                <a href="{{ route('barbers.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
