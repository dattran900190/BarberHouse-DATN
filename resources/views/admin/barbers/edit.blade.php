@extends('adminlte::page')

@section('title', 'Chỉnh sửa Thợ Cắt Tóc')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h3 class="card-title">Chỉnh sửa Thợ Cắt Tóc</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.update', $barber->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="row">
                    <!-- Phần bên trái: Ảnh đại diện -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="avatar">Ảnh đại diện mới (nếu có)</label>
                            <input type="file" id="avatar" name="avatar" class="form-control-file" accept="image/*">
                            @error('avatar')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        @if ($barber->avatar)
                            <div class="form-group">
                                <label>Ảnh đại diện hiện tại:</label><br>
                                <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar"
                                    style="max-height: 150px;">
                            </div>
                        @endif
                    </div>

                    <!-- Phần bên phải: Các thông tin thợ cắt tóc -->
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="name">Họ tên</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ old('name', $barber->name) }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="skill_level">Trình độ</label>
                            <input type="text" id="skill_level" name="skill_level" class="form-control"
                                value="{{ old('skill_level', $barber->skill_level) }}" required>
                            @error('skill_level')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="rating_avg">Đánh giá trung bình</label>
                            <input type="number" id="rating_avg" name="rating_avg" class="form-control"
                                value="{{ old('rating_avg', $barber->rating_avg) }}" step="0.1" min="0"
                                max="5">
                            @error('rating_avg')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="profile">Hồ sơ</label>
                            <textarea id="profile" name="profile" class="form-control" rows="3">{{ old('profile', $barber->profile) }}</textarea>
                            @error('profile')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="branch_id">Chi nhánh</label>
                            <select id="branch_id" name="branch_id" class="form-control">
                                <option value="">-- Chọn chi nhánh --</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ old('branch_id', $barber->branch_id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('branch_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        @php
                            $isRetired = $barber->status === 'retired';
                        @endphp

                        <div class="form-group">
                            <label for="status">Trạng thái</label>

                            <select id="status" name="status" class="form-control" {{ $isRetired ? 'disabled' : '' }}>
                                <option value="idle" {{ old('status', $barber->status) == 'idle' ? 'selected' : '' }}>
                                    Rảnh (chờ khách)
                                </option>
                                <option value="busy" {{ old('status', $barber->status) == 'busy' ? 'selected' : '' }}>
                                    Đang làm việc
                                </option>
                                <option value="retired"
                                    {{ old('status', $barber->status) == 'retired' ? 'selected' : '' }}>
                                    Nghỉ việc
                                </option>
                            </select>

                            {{-- Khi bị disabled thì phải thêm hidden input để giữ lại dữ liệu --}}
                            @if ($isRetired)
                                <input type="hidden" name="status" value="retired">
                            @endif

                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('barbers.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a>
            </form>
        </div>
    </div>
@endsection
