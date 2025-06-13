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
            <label>Hình ảnh</label>
            <input type="file" name="image" class="form-control-file @error('image') is-invalid @enderror">
            @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Trạng thái</label>
            <select name="is_active" class="form-control @error('is_active') is-invalid @enderror">
            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Ẩn</option>
            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Hiển thị</option>
        </select>
        @error('is_active')
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
