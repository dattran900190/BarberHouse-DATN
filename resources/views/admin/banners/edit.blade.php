@extends('adminlte::page')

@section('title', 'Chỉnh sửa Banner')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h3 class="card-title">Chỉnh sửa Banner</h3>
        </div>
        <form action="{{ route('banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="form-group">
                    <label>Tiêu đề</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $banner->title) }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                

                <div class="form-group">
                    <label>Hình ảnh hiện tại</label><br>
                    @if ($banner->image_url)
                        <img src="{{ asset('storage/' . $banner->image_url) }}" width="120" class="img-thumbnail mb-2">
                    @else
                        <p class="text-muted">Chưa có ảnh</p>
                    @endif
                    <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Trạng thái</label>
                     <select name="is_active" class="form-control @error('is_active') is-invalid @enderror">
                    <option value="0" {{ old('is_active', $banner->is_active) == '0' ? 'selected' : '' }}>Ẩn</option>
                    <option value="1" {{ old('is_active', $banner->is_active) == '1' ? 'selected' : '' }}>Hiển thị</option>
                </select>

                @error('is_active')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                </div>

            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
            </div>
        </form>
    </div>
@endsection
