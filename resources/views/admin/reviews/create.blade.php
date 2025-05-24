@extends('adminlte::page')

@section('title', 'Thêm bình luận mới')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title mb-0">Thêm bình luận</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="user_id" class="form-label">Người bình luận</label>
                    <input type="text" class="form-control" id="user_id" name="user_id" value="{{ old('user_id') }}">
                    @error('user_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                 <div class="mb-3">
                    <label for="barber_id" class="form-label">Thợ</label>
                    <input type="text" class="form-control" id="barber_id" name="barber_id" value="{{ old('barber_id') }}">
                    @error('barber_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="rating" class="form-label">Đánh giá</label>
                    <input type="number" class="form-control" id="rating" name="rating" value="{{ old('rating') }}">
                    @error('rating')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                 <div class="mb-3">
                    <label for="comment" class="form-label">Bình luận</label>
                    <textarea class="form-control" id="comment" name="comment">{{ old('comment') }}</textarea>
                    @error('comment')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                

                <button type="submit" class="btn btn-success">Thêm</button>
                <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
