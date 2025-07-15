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
        <h3 class="fw-bold mb-3">Sản phẩm</h3>
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

                <div class="form-group d-flex align-items-center">
                    <label for="name" class="me-2">Tên dung tích:</label>
                    <input type="number" name="name" id="name" 
                        value="{{ old('name', preg_replace('/[^0-9]/', '', $volume->name)) }}" 
                        class="form-control w-auto me-2" required placeholder="Nhập số">
                    <span>ml</span>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.volumes.index', ['page' => request()->get('page')]) }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
