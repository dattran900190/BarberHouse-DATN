@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa dung tích')

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
        <h3 class="fw-bold mb-3 text-uppercase">Dung tích</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes') }}">Danh sách sản phẩm</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes') }}">Quản lý đặt hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes/' . $volume->id .'/edit') }}">Chỉnh sửa dung tích</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chỉnh sửa dung tích</div>
        </div>
        <div class="card-body">
            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.volumes.update', $volume) }}?page={{ request()->get('page') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-3" style="max-width: 400px;">
                    <label for="name" class="form-label fw-bold">Dung tích </label>
                    <div class="input-group">
                       <input type="number" name="name" id="name" 
                        value="{{ old('name', preg_replace('/[^0-9]/', '', $volume->name)) }}" 
                        class="form-control"  placeholder="Nhập số">
                        <span class="input-group-text">ml</span>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-edit me-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.volumes.index', ['page' => request()->get('page')]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
