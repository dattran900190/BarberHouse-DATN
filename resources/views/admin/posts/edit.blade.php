@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Bài viết')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa Bài viết</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" class="form-control" id="title" name="title"
                           value="{{ old('title', $post->title) }}">
                    @error('title')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="short_description" class="form-label">Mô tả</label>
                    <input type="text" class="form-control" id="short_description" name="short_description"
                           value="{{ old('short_description', $post->short_description) }}">
                    @error('short_description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung</label>
                    <textarea class="form-control" id="content" name="content" rows="10">{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-control" id="status" name="status">
                        <option value="1" {{ $post->status ? 'selected' : '' }}>Xuất bản</option>
                        <option value="0" {{ !$post->status ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="published_at" class="form-label">Ngày xuất bản</label>
                    <input type="date" class="form-control" id="published_at" name="published_at"
                           value="{{ old('published_at', $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('Y-m-d') : '') }}">
                    @error('published_at')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                @if ($post->image)
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại</label><br>
                        <img src="{{ asset('storage/' . $post->image) }}" width="120" class="img-thumbnail">
                    </div>
                @endif

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh mới (nếu muốn thay)</label>
                    <input type="file" class="form-control" id="image" name="image">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('posts.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
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
