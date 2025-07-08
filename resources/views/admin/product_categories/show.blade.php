@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Danh mục')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết danh mục</h3>
        </div>

        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Tên danh mục:</dt>
                <dd class="col-sm-9">{{ $product_category->name }}</dd>

                <dt class="col-sm-3">Slug:</dt>
                <dd class="col-sm-9">{{ $product_category->slug }}</dd>

                <dt class="col-sm-3">Mô tả:</dt>
                <dd class="col-sm-9">{{ $product_category->description ?? 'Không có mô tả' }}</dd>

                <dt class="col-sm-3">Ngày tạo:</dt>
                <dd class="col-sm-9">{{ $product_category->created_at->format('d/m/Y H:i') }}</dd>

                <dt class="col-sm-3">Ngày cập nhật:</dt>
                <dd class="col-sm-9">{{ $product_category->updated_at->format('d/m/Y H:i') }}</dd>
            </dl>

            <div class="d-flex justify-content-between">
                <a href="{{ route('product_categories.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
               
            </div>
        </div>
    </div>
@endsection
