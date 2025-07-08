@extends('layouts.AdminLayout')

@section('title', 'Tạo Danh mục mới')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Tạo Danh mục mới</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('product_categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug(nếu để trống sẽ tạo tự động)</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                    @error('slug')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('product_categories.index') }}" class="btn btn-secondary">Quay lại</a>
                    <button type="submit" class="btn btn-success">Tạo mới</button>
                </div>
            </form>
        </div>
    </div>
@endsection
