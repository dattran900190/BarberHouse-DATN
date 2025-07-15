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

    <div class="page-header">
        <h3 class="fw-bold mb-3">Sản phẩm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes') }}">Danh sách dung tích</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes/create') }}">Thêm dung tích</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Thêm dung tích sản phẩm</div>
        </div>

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
        <div class="card-body">
        <form action="{{ route('admin.volumes.store') }}" method="POST">
            @csrf

            <div class="form-group mb-3" style="max-width: 400px;">
                <label for="name" class="form-label fw-bold">Tên dung tích <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" name="name" id="name" class="form-control"
                        value="{{ old('name') ? preg_replace('/[^0-9]/', '', old('name')) : '' }}" placeholder="Nhập số"
                        required>
                    <span class="input-group-text">ml</span>
                </div>
            </div>



            <button type="submit" class="btn btn-sm btn-outline-primary">Lưu</button>
            <a href="{{ route('admin.volumes.index') }}" class="btn btn-sm btn-outline-danger">Quay lại</a>

        </form>
        </div>
    </div>
@endsection
