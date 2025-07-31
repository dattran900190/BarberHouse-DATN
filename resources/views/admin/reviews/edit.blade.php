@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa trạng thái bình luận')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Bình luận</h3>
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
                <a href="{{ url('admin/reviews') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews') }}">Bình luận</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews/' . $review->id . '/edit') }}">Sửa bình luận</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Sửa trạng thái bình luận</div>
        </div>

        <div class="card-body">
            <form action="{{ route('reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                <div class="mb-3">
                    <label for="is_visible" class="form-label">Trạng thái</label>
                    <select class="form-control" id="is_visible" name="is_visible">
                        <option value="1" {{ old('is_visible', $review->is_visible) == 1 ? 'selected' : '' }}>Hiện
                        </option>
                        <option value="0" {{ old('is_visible', $review->is_visible) == 0 ? 'selected' : '' }}>Ẩn
                        </option>
                    </select>

                    @error('is_visible')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fa fa-edit me-1"></i> Cập nhật
                </button>
                <a href="{{ route('reviews.index', ['page' => request('page', 1)]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
@endsection
