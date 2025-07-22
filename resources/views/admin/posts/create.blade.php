@extends('layouts.AdminLayout')

@section('title', 'Thêm Bài Viết Mới')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tin Tức</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/posts') }}">Bài viết </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/posts/create') }}">Thêm bài viết</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Thêm bài viết</div>
        </div>

        <div class="card-body">
            <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}">
                        @error('title') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (tự sinh nếu để trống)</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}">
                        @error('slug') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <input type="text" name="short_description" class="form-control @error('short_description') is-invalid @enderror" value="{{ old('short_description') }}">
                        @error('short_description') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hình ảnh (tuỳ chọn)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="10">{{ old('content') }}</textarea>
                    @error('content') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tác giả</label>
                        <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                        <input type="hidden" name="author_id" value="{{ Auth::id() }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nổi bật</label>
                        <select class="form-control" name="is_featured">
                            <option value="0" {{ old('is_featured') == 0 ? 'selected' : '' }}>Không nổi bật</option>
                            <option value="1" {{ old('is_featured') == 1 ? 'selected' : '' }}>Nổi bật</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Không hoạt động</option>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                        </select>
                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày xuất bản</label>
                        <input type="date" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at') }}">
                        @error('published_at') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-plus"></i> <span class="ms-2">Thêm</span>
                </button>
                <a href="{{ route('posts.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
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
            paste_data_images: true,
        });
    </script>
@endsection
