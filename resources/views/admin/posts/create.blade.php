@extends('layouts.AdminLayout')

@section('title', 'Thêm Bài Viết Mới')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Bài Viết</h3>
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
                <a href="{{ url('admin/posts') }}">Quản lý chung</a>
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
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}">
                        @error('title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ngày xuất bản</label>
                        <input type="date" name="published_at"
                            class="form-control @error('published_at') is-invalid @enderror"
                            value="{{ old('published_at') }}">
                        @error('published_at')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="10">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    {{-- Mô tả ngắn bên trái --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mô tả ngắn</label>
                        <textarea name="short_description" rows="5" class="form-control @error('short_description') is-invalid @enderror">{{ old('short_description') }}</textarea>
                        @error('short_description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Trạng thái và Nổi bật bên phải --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Đang hoạt
                                    động</option>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Không hoạt động
                                </option>

                                </option>
                            </select>
                            @error('status')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nổi bật</label>
                            <select name="is_featured" class="form-control @error('is_featured') is-invalid @enderror">
                                <option value="1" {{ old('is_featured', '0') == '1' ? 'selected' : '' }}>Nổi bật
                                </option>
                                <option value="0" {{ old('is_featured') == '0' ? 'selected' : '' }}>Không nổi bật
                                </option>

                            </select>
                            @error('is_featured')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Ảnh --}}
                <div class="mb-3">
                    <label class="form-label">Ảnh đại diện (tùy chọn)</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tác giả --}}
                <div class="mb-3">
                    <label class="form-label">Tác giả</label>
                    <input type="text" class="form-control" value="{{ Auth::user()->name }}" disabled>
                    <input type="hidden" name="author_id" value="{{ Auth::id() }}">
                </div>

                <button type="submit" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-plus me-1"></i> Thêm
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
