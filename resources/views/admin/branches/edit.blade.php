@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Chi nhánh')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa Chi nhánh</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('branches.update', $branch->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                {{-- Tên chi nhánh --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Tên chi nhánh</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ old('name', $branch->name) }}">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Địa chỉ --}}
                <div class="mb-3">
                    <label for="address" class="form-label">Địa chỉ</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address">{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Số điện thoại --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                        name="phone" value="{{ old('phone', $branch->phone) }}">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Google Maps URL --}}
                <div class="mb-3">
                    <label for="google_map_url" class="form-label">Link Google Maps</label>
                    <input type="text" class="form-control @error('google_map_url') is-invalid @enderror"
                        id="google_map_url" name="google_map_url"
                        value="{{ old('google_map_url', $branch->google_map_url) }}">
                    @error('google_map_url')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ảnh hiện tại --}}
                @if ($branch->image)
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại</label><br>
                        <img src="{{ asset('storage/' . $branch->image) }}" alt="Branch Image" width="200">
                    </div>
                @endif

                {{-- Ảnh mới --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Ảnh mới</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image"
                        name="image">
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nội dung mô tả --}}
                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung mô tả</label>
                    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror" rows="10">{{ old('content', $branch->content) }}</textarea>
                    @error('content')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a>
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
