@extends('layouts.AdminLayout')

@section('title', 'Quản lý dung tích')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách dung tích sản phẩm</h3>
            <a href="{{ route('admin.volumes.create') }}"
               class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm sản phẩm</span>
            </a>
        </div>
    <h1>Thêm dung tích mới</h1>

    {{-- Hiển thị lỗi nếu có --}}
    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.volumes.store') }}" method="POST">
        @csrf

        <div style="margin-bottom: 10px;">
            <label for="name">Tên dung tích:</label><br>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   style="padding: 5px; width: 300px;" placeholder="Nhập tên dung tích">
        </div>

<button type="submit" class="btn btn-primary px-3">Lưu</button>
<a href="{{ route('admin.volumes.index') }}" class="btn btn-secondary ms-2">Quay lại</a>

    </form>
@endsection
