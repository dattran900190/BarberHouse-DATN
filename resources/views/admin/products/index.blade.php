@extends('layouts.AdminLayout')

@section('title', 'Quản lý Sản phẩm')

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
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Sản phẩm</h3>
            <a href="{{ route('admin.products.create') }}"
               class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm sản phẩm</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên sản phẩm..."
                           value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Danh mục</th>
                        <th>Ảnh chính</th>
                        <th>Ảnh bổ sung</th>
                        <th>Biến thể</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ number_format($product->price) }} đ</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ $product->category->name ?? 'Không có' }}</td>
                            <td class="text-center">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" width="50" height="50" alt="Ảnh chính" />
                                @else
                                    Không có
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($product->images->isNotEmpty())
                                    @foreach ($product->images as $image)
                                        <img src="{{ asset('storage/' . $image->image_url) }}" width="50" height="50" alt="Ảnh bổ sung" class="me-2" />
                                    @endforeach
                                @else
                                    Không có
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($product->variants->isNotEmpty())
                                    @foreach ($product->variants as $variant)
                                        <div>
                                            {{ $variant->volume->name ?? 'N/A' }}:
                                            @if ($variant->image)
                                                <img src="{{ asset('storage/' . $variant->image) }}" width="50" height="50" alt="Ảnh biến thể" />
                                            @else
                                                Không có
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    Không có biến thể
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenu{{ $product->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="actionMenu{{ $product->id }}">
                                                    <li> <a href="{{ route('admin.products.show', $product->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a></li>
                                                    <li>  <a href="{{ route('admin.products.edit', $product->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a></li>
                                  <li>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                          class="d-inline m-0"
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="dropdown-item text-danger">
                                            <i class="fas fa-trash-alt"></i> <span>  Xóa </span>
                                        </button>
                                    </form>
                                </li>
                                </div>
                            </td>
                        </tr>
                        
                    @endforeach
                    @if ($products->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
    {{ $products->withQueryString()->links() }}
</div>
@endsection

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }
        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
        img {
            object-fit: cover;
            border-radius: 5px;
            margin: 5px;
        }
    </style>
@endsection
