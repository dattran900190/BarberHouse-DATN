@extends('adminlte::page')

@section('title', 'Chi tiết Banner')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title">Chi tiết Banner</h3>
        </div>
        <div class="card-body">

            <div class="mb-3">
                <strong>Tiêu đề:</strong>
                <p>{{ $banner->title }}</p>
            </div>

            <div class="mb-3">
                <strong>Link:</strong>
                <p>{{ $banner->link_url ?? 'Không có' }}</p>
            </div>

            <div class="mb-3">
                <strong>Hình ảnh:</strong><br>
                @if ($banner->image_url)
                    <img src="{{ asset('storage/' . $banner->image_url) }}" class="img-fluid" style="max-width: 300px;">
                @else
                    <span class="text-muted">Chưa có hình ảnh</span>
                @endif
            </div>

            <div class="mb-3">
                <strong>Trạng thái:</strong>
                <p>
                    @if ($banner->is_active)
                        <span class="badge bg-success">Hiển thị</span>
                    @else
                        <span class="badge bg-secondary">Ẩn</span>
                    @endif
                </p>
            </div>

            <div class="mb-3">
                <strong>Ngày tạo:</strong>
                <p>{{ $banner->created_at->format('d/m/Y H:i') }}</p>
            </div>

        </div>
        <div class="card-footer text-right">
            <a href="{{ route('banners.index') }}" class="btn btn-secondary">Quay lại</a>
           
        </div>
    </div>
@endsection
