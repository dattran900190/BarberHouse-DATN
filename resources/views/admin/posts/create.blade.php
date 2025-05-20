@extends('adminlte::page')

@section('title', 'Thêm bài viết')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title">Thêm bài viết mới</h3>
        </div>
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">


                {{-- Tiêu đề --}}
                <div class="form-group">
                    <label>Tiêu đề</label>
                    <input type="text" name="title"
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title') }}">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Slug --}}
                <div class="form-group">
                    <label>Slug (nếu để trống sẽ tạo tự động)</label>
                    <input type="text" name="slug"
                        class="form-control @error('slug') is-invalid @enderror"
                        value="{{ old('slug') }}">
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


               {{-- Nội dung --}}
                <div class="form-group">
                    <label>Nội dung</label>
                    <textarea id="content" name="content"
                        class="form-control @error('content') is-invalid @enderror"
                        rows="10">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                {{-- Ảnh --}}
                <div class="form-group">
                    <label>Hình ảnh (tùy chọn)</label>
                    <input type="file" name="image"
                        class="form-control-file @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tác giả --}}
                <div class="form-group">
                    <label>Tác giả</label>
                    <select name="author_id" class="form-control @error('author_id') is-invalid @enderror">
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('author_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Chưa xuất bản</option>
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Xuất bản</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ngày xuất bản --}}
                <div class="form-group">
                    <label>Ngày xuất bản</label>
                    <input type="date" name="published_at"
                        class="form-control @error('published_at') is-invalid @enderror"
                        value="{{ old('published_at') }}">
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-success">Lưu bài viết</button>
            </div>
        </form>
    </div>
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>

    <script>
        tinymce.init({
            selector: 'textarea#content',
            plugins: 'image link media table code lists',
            toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link media | code',
            height: 400,
            menubar: false,
            branding: false,
        });
    </script>
@endsection

