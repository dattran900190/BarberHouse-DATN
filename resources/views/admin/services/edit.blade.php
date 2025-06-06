@extends('adminlte::page')

@section('title', 'Chỉnh sửa Dịch vụ')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa Dịch vụ</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="mb-3">
                    <label for="name" class="form-label">Tên dịch vụ</label>
                    <input type="text" class="form-control" id="name" name="name"
                           value="{{ old('name', $service->name) }}">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description">{{ old('description', $service->description) }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price"
                           value="{{ old('price', $service->price) }}">
                    @error('price')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="duration" class="form-label">Thời lượng (phút)</label>
                    <input type="number" class="form-control" id="duration" name="duration"
                           value="{{ old('duration', $service->duration) }}">
                    @error('duration')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Gói combo</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_combo" id="combo_yes" value="1"
                               {{ old('is_combo', $service->is_combo) ? 'checked' : '' }}>
                        <label class="form-check-label" for="combo_yes">Có</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="is_combo" id="combo_no" value="0"
                               {{ old('is_combo', $service->is_combo) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="combo_no">Không</label>
                    </div>
                    @error('is_combo')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh dịch vụ</label>
                    <input type="file" class="form-control" id="image" name="image">
                    @if ($service->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="Ảnh dịch vụ" width="150">
                        </div>
                    @endif
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('services.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
