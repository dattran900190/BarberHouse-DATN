@extends('layouts.AdminLayout')

@section('title', 'Thêm Ảnh Khách Hàng Mới')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thêm Ảnh Khách Hàng</h3>
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
                <a href="{{ url('admin/customer-images') }}">Ảnh khách hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                Thêm ảnh mới
            </li>
        </ul>
    </div>

    <div class="card">
         <div class="card-header text-white align-items-center">
            <div class="card-title">Thêm ảnh khách khàng</div>
        </div>

        <div class="card-body">
            <form action="{{ route('customer-images.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                     <div class="col-md-6 mb-4">
                        <label class="form-label">Tải lên ảnh</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
             
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ẩn</option>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Hiển thị</option>
                        </select>
                        @error('status') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus"></i> <span class="ms-1">Thêm ảnh</span>
                    </button>
                    <a href="{{ route('customer-images.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
