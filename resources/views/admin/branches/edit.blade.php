@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Chi nhánh')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi nhánh</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Quản lý chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('branches.index') }}">Danh sách chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chỉnh sửa</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chỉnh sửa Chi nhánh</div>
        </div>

        <div class="card-body">
            <form action="{{ route('branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Tên chi nhánh</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $branch->name) }}">
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" value="{{ old('phone', $branch->phone) }}">
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $branch->address) }}</textarea>
                        @error('address')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="google_map_url" class="form-label">Link Google Maps</label>
                        <input type="text" class="form-control @error('google_map_url') is-invalid @enderror"
                            id="google_map_url" name="google_map_url"
                            value="{{ old('google_map_url', $branch->google_map_url) }}">
                        @error('google_map_url')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh chi nhánh</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                        name="image">
                    @if ($branch->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $branch->image) }}" alt="Branch Image" width="120"
                                class="rounded">
                        </div>
                    @endif
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Giới thiệu</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="10">{{ old('content', $branch->content) }}</textarea>
                    @error('content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-sm btn-outline-warning">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}"
                    class="btn btn-sm btn-outline-secondary">
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
        });
    </script>
@endsection
