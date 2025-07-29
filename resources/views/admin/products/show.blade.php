
@extends('layouts.AdminLayout')

@section('title', 'Quản lý Sản phẩm')
@section('content')

<div class="page-header">
    <h3 class="fw-bold mb-3">Sản phẩm</h3>
    <ul class="breadcrumbs mb-3">
        <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
        <li class="separator"><i class="icon-arrow-right"></i></li>
        <li class="nav-item">
            <a href="{{ url('admin/dashboard') }}">Danh sách sản phẩm</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ url('admin/products/' . $product->id) }}">Chi tiết sản phẩm</a>
        </li>
    </ul>
</div>
    <div class="container">

        <h1>Chi tiết sản phẩm: {{ $product->name }}</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Thông tin sản phẩm</h5>
                <br>
                <p><strong>Tên:</strong> {{ $product->name }}</p>
                <p><strong>Danh mục:</strong> {{ $product->category->name ?? 'Không có' }}</p>
                <p><strong>Giá đại diện:</strong> {{ number_format($product->price) }} VNĐ</p>
                <p><strong>Tồn kho:</strong> {{ $product->stock }}</p>
                <p><strong>Mô tả:</strong> {{ $product->description ?? 'Không có' }}</p>
                <p><strong>Mô tả dài:</strong> {{ $product->long_description ?? 'Không có' }}</p>

                <h5 class="mt-4">Ảnh chính</h5>
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" width="200" alt="Ảnh chính" class="mb-3" />
                @else
                    <p>Không có ảnh chính</p>
                @endif

                <h5>Ảnh bổ sung</h5>
                @if ($product->images->isNotEmpty())
                    <div class="d-flex flex-wrap">
                        @foreach ($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_url) }}" width="150" alt="Ảnh bổ sung" class="me-2 mb-2" />
                        @endforeach
                    </div>
                @else
                    <p>Không có ảnh bổ sung</p>
                @endif

                <h5 class="mt-4">Biến thể</h5>
                @if ($product->variants->isNotEmpty())
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Dung tích</th>
                                <th>Giá</th>
                                <th>Tồn kho</th>
                                <th>Ảnh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->variants as $variant)
                                <tr>
                                    <td>{{ $variant->volume->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($variant->price) }} VNĐ</td>
                                    <td>{{ $variant->stock }}</td>
                                    <td>
                                        @if ($variant->image)
                                            <img src="{{ asset('storage/' . $variant->image) }}" width="100" alt="Ảnh biến thể" />
                                        @else
                                            Không có
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Không có biến thể</p>
                @endif

                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-primary btn-sm">Chỉnh sửa</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">Quay lại</a>
            </div>
        </div>
    </div>
@endsection