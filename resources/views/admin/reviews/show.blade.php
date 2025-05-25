@extends('adminlte::page')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết Dịch vụ</h3>
        </div>

        <div class="card-body">
            <p><strong>Người bình luận:</strong> {{ $review->user->name}}</p>
            <p><strong>Thợ:</strong> {{ $review->barber->name }}</p>
            <p><strong>Đánh giá:</strong> {{ $review->rating }}</p>
            <p><strong>Bình luận:</strong> {{ $review->comment }}</p>
            <p><strong>Trạng thái:</strong> {{ $review->is_visible == 1 ? "Hiện" : "Ẩn" }}</p>


            <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('reviews.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
@endsection
