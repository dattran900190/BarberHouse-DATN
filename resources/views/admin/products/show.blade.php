@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Chi tiết sản phẩm: {{ $product->name }}</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Thông tin sản phẩm</h5>
                <br>
                <p><strong>Tên:</strong> {{ $product->name }}</p>
                <p><strong>Danh mục:</strong> {{ $product->category->name ?? 'Không có' }}</p>
                <p><strong>Giá:</strong> {{ number_format($product->price) }} đ</p>
                <p><strong>Tồn kho:</strong> {{ $product->stock }}</p>
                <p><strong>Mô tả:</strong> {{ $product->description ?? 'Không có' }}</p>

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
                                    <td>{{ number_format($variant->price) }} đ</td>
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

                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">Chỉnh sửa</a>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection