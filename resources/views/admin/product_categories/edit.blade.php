@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Danh Mục')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Danh mục sản phẩm</h3>
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
                <a href="{{ url('admin/product_categories') }}">Quản lý đặt hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('product_categories.index') }}">Danh mục</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('product_categories.edit', $product_category->id) }}">Sửa danh mục</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Sửa danh mục</div>
        </div>

        <div class="card-body">
            <form action="{{ route('product_categories.update', $product_category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $product_category->name) }}">
                        @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (tự sinh nếu để trống)</label>
                        <input type="text" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $product_category->slug) }}">
                        @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="4">{{ old('description', $product_category->description) }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('product_categories.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
@endsection
