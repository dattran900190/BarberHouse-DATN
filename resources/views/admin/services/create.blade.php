@extends('adminlte::page')

@section('title', 'Thêm Dịch Vụ Mới')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title mb-0">Thêm Dịch Vụ</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Tên dịch vụ</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá (VNĐ)</label>
                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price') }}">
                    @error('price')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Thời lượng (phút)</label>
                    <input type="number" class="form-control" id="duration" name="duration" value="{{ old('duration') }}">
                    @error('duration')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_combo" class="form-label">Dịch vụ combo?</label>
                    <select class="form-control" id="is_combo" name="is_combo">
                        <option value="0" {{ old('is_combo') == 0 ? 'selected' : '' }}>Không</option>
                        <option value="1" {{ old('is_combo') == 1 ? 'selected' : '' }}>Có</option>
                    </select>
                    @error('is_combo')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh đại diện</label>
                    <input type="file" class="form-control" id="image" name="image">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Thêm</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
