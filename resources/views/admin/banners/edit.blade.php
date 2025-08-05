@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Banner')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Banner</h3>
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
                <a href="{{ url('admin/banners') }}">Quản lý chung</a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/banners') }}">Banner</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/banners/' . $banner->id . '/edit') }}">Chỉnh sửa Banner</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chỉnh sửa Banner</div>
        </div>

        <div class="card-body">
            <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $banner->title) }}">
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                            <option value="0" {{ old('is_active', $banner->is_active) == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            <option value="1" {{ old('is_active', $banner->is_active) == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                        </select>
                        @error('is_active') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Liên kết (tuỳ chọn)</label>
                    <input type="text" name="link_url"
                        class="form-control @error('link_url') is-invalid @enderror"
                        value="{{ old('link_url', $banner->link_url) }}">
                    @error('link_url') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                @if ($banner->image_url)
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại</label><br>
                        <img src="{{ asset('storage/' . $banner->image_url) }}" width="120" class="img-thumbnail">
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Ảnh mới (nếu muốn thay)</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('banners.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
@endsection
