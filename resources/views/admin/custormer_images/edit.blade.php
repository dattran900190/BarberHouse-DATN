@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa Ảnh khách hàng')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Ảnh khách hàng</h3>
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
                <a href="{{ route('customer-images.index') }}">Ảnh khách hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">Chỉnh sửa</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chỉnh sửa ảnh khách hàng</div>
        </div>

        <div class="card-body">
            <form action="{{ route('customer-images.update', $customerImage->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', $customerImage->status) == '1' ? 'selected' : '' }}>Hiển thị</option>
                            <option value="0" {{ old('status', $customerImage->status) == '0' ? 'selected' : '' }}>Ẩn</option>
                        </select>
                        @error('status')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Ảnh mới (nếu muốn thay)</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" onchange="previewImage(this, 'preview')">
                        @error('image')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if ($customerImage->image)
                    <div class="mb-3">
                        <label class="form-label">Ảnh hiện tại</label><br>
                        <img src="{{ asset('storage/' . $customerImage->image) }}" width="120" class="img-thumbnail" id="preview">
                    </div>
                @else
                    <img src="https://via.placeholder.com/120x80?text=No+Image" class="img-thumbnail mb-3" id="preview">
                @endif

                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('customer-images.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
