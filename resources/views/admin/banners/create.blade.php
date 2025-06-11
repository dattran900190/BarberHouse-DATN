@extends('adminlte::page')

@section('title', 'Thêm Banner')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Thêm Banner mới</h3>
        </div>
        <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="card-body">

        <div class="form-group">
            <label>Tiêu đề</label>
            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}">
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Link</label>
            <input type="text" name="link" class="form-control @error('link') is-invalid @enderror"
                   value="{{ old('link') }}">
            @error('link')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Hình ảnh</label>
            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror">
            @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status" class="form-control @error('status') is-invalid @enderror">
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ẩn</option>
                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hiển thị</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div class="card-footer text-right">
        <button type="submit" class="btn btn-success">Lưu Banner</button>
    </div>
</form>

    </div>
@endsection
