@extends('adminlte::page')

@section('title', 'Chi tiết Chi nhánh')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết Chi nhánh</h3>
        </div>

        <div class="card-body">
            <p><strong>Tên chi nhánh:</strong> {{ $branch->name }}</p>
            <p><strong>Địa chỉ:</strong> {{ $branch->address }}</p>
            <p><strong>Số điện thoại:</strong> {{ $branch->phone }}</p>
            <p><strong>Email:</strong> {{ $branch->email }}</p>
            <p><strong>Ngày tạo:</strong> {{ $branch->created_at->format('d/m/Y H:i') }}</p>

            <a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-warning">sửa</a>
            <a href="{{ route('admin.branches.index') }}" class="btn btn-secondary">Quay lại</a>

        </div>
    </div>
@endsection
