@extends('adminlte::page')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết Dịch vụ</h3>
        </div>

        <div class="card-body">
            <p><strong>Tên dịch vụ:</strong> {{ $service->name }}</p>
            <p><strong>Mô tả:</strong> {{ $service->description }}</p>
            <p><strong>Giá:</strong> {{ number_format($service->price, 0, ',', '.') }} VNĐ</p>
            <p><strong>Thời lượng:</strong> {{ $service->duration }} phút</p>
            <p><strong>Loại dịch vụ:</strong> {{ $service->is_combo ? 'Combo' : 'Thông thường' }}</p>

            @if ($service->image)
                <img src="{{ asset('storage/' . $service->image) }}" alt="Ảnh dịch vụ" width="150">
            @endif


            <p><strong>Ngày tạo:</strong> 
    {{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : 'Không xác định' }}
</p>

            <a href="{{ route('services.edit', $service->id) }}" class="btn btn-warning">Sửa</a>
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </div>
@endsection
