@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa trạng thái bình luận')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa trạng thái bình luận</h3>
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

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('reviews.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
