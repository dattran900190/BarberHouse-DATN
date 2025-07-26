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
                <a href="{{ url('admin/products') }}">Sản phẩm</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 flex-grow-1">Danh sách Sản phẩm</h3>
            <a href="{{ route('admin.products.create') }}"
               class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm sản phẩm</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <div class="input-group" style="flex: 1;">
                        <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên sản phẩm..."
                            value="{{ request()->get('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                    <select name="filter" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="all" {{ request('filter', 'all') == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="active" {{ request('filter', 'active') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                        <option value="deleted" {{ request('filter', 'deleted') == 'deleted' ? 'selected' : '' }}>Đã xóa</option>
                    </select>
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
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $index => $product)
                        <tr>
                            <td>{{ $products->firstItem() + $index }}</td>
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
                            <td>
                                @if ($product->trashed())
                                    <span class="badge bg-danger">Đã xóa</span>
                                @else
                                    <span class="badge bg-success">Hoạt động</span>
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
                                        @if ($product->trashed())
                                            <li>
                                                <button type="button" class="dropdown-item text-success restore-btn"
                                                    data-id="{{ $product->id }}">
                                                    <i class="fas fa-undo me-2"></i> Khôi phục
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger force-delete-btn"
                                                    data-id="{{ $product->id }}">
                                                    <i class="fas fa-trash-alt me-2"></i> Xóa vĩnh viễn
                                                </button>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('admin.products.show', $product->id) }}" class="dropdown-item">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.products.edit', $product->id) }}" class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> Sửa
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger soft-delete-btn"
                                                    data-id="{{ $product->id }}">
                                                    <i class="fas fa-trash me-2"></i> Xóa mềm
                                                </button>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-3">
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

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function handleSwalAction({
        selector,
        title,
        text,
        route,
        method = 'POST'
    }) {
        document.querySelectorAll(selector).forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const recordId = this.getAttribute('data-id');

                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    width: '400px',
                }).then((result) => {
                    if (result.isConfirmed) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = route.replace(':id', recordId);
                        
                        let csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);

                        if (method.toUpperCase() !== 'POST') {
                            let methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = method;
                            form.appendChild(methodInput);
                        }

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    }

    handleSwalAction({
        selector: '.soft-delete-btn',
        title: 'Xoá mềm Sản phẩm',
        text: 'Bạn có chắc muốn xoá mềm sản phẩm này?',
        route: '{{ route("admin.products.destroy", ":id") }}',
        method: 'DELETE'
    });

    handleSwalAction({
        selector: '.restore-btn',
        title: 'Khôi phục Sản phẩm',
        text: 'Khôi phục sản phẩm đã xoá?',
        route: '{{ route("admin.products.restore", ":id") }}',
        method: 'POST'
    });

    handleSwalAction({
        selector: '.force-delete-btn',
        title: 'Xoá vĩnh viễn Sản phẩm',
        text: 'Bạn có chắc muốn xoá vĩnh viễn? Hành động không thể hoàn tác!',
        route: '{{ route("admin.products.forceDelete", ":id") }}',
        method: 'DELETE'
    });
</script>
@endsection
