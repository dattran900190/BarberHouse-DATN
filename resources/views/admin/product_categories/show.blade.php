@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Danh mục')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Danh mục sản phẩm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('product_categories.index') }}">Danh mục</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Chi tiết danh mục</a>
            </li>
        </ul>
    </div>

    <!-- Card: Thông tin danh mục -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết danh mục</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-12">
                    <h4 class="fw-bold mb-3">{{ $product_category->name }}</h4>

                    <p class="text-muted mb-2">
                        <i class="fa fa-link me-2 text-primary"></i><strong>Slug:</strong> {{ $product_category->slug }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-info-circle me-2 text-info"></i><strong>Mô tả:</strong>
                        {{ $product_category->description ?? 'Không có mô tả' }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-calendar me-2 text-muted"></i><strong>Ngày tạo:</strong>
                        {{ $product_category->created_at->format('d/m/Y H:i') }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-clock me-2 text-secondary"></i><strong>Ngày cập nhật:</strong>
                        {{ $product_category->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('product_categories.edit', $product_category->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <a href="{{ route('product_categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
@endsection
