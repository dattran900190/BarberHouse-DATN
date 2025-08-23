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
        <h3 class="fw-bold mb-3 text-uppercase">Sản phẩm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/products') }}">Danh sách sản phẩm</a>
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
            <h3 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm</h3>
            <a href="{{ route('admin.products.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm sản phẩm</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.products.index') }}" method="GET" class="mb-3">
                <div class="d-flex flex-wrap gap-2">
                    <div class="position-relative" style="flex: 1; min-width: 200px">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm kiếm theo tên sản phẩm..." value="{{ request()->get('search') }}">
                        <button type="submit"
                            class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <select name="filter" id="filter" class="form-select pe-5"
                        style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả sản phẩm</option>
                        <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                        <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
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
                            <td>{{ number_format($product->price) }} VNĐ</td>
                            <td>{{ $product->stock }}</td>
                            <td>{{ $product->category->name ?? 'Không có' }}</td>
                            <td class="text-center">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;"
                                        alt="Ảnh chính" />
                                @else
                                    Không có
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($product->images->isNotEmpty())
                                    @foreach ($product->images as $image)
                                        <img src="{{ asset('storage/' . $image->image_url) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;" alt="Ảnh bổ sung" class="me-2" />
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
                                                <img src="{{ asset('storage/' . $variant->image) }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 10px;" alt="Ảnh biến thể" />
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
                                        id="actionMenu{{ $product->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="actionMenu{{ $product->id }}">
                                        @if ($product->trashed())
                                        <li>
                                            <a href="{{route('admin.products.show', $product->id)}}"    class="dropdown-item">
                                                <i class="fas fa-eye me-2"></i>Xem
                                            </a>
                                        </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-success restore-btn"
                                                    data-id="{{ $product->id }}">
                                                    <i class="fas fa-undo me-2"></i> Khôi phục
                                                </button>
                                            </li>
                                        
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger force-delete-btn"
                                                    data-id="{{ $product->id }}">
                                                    <i class="fas fa-trash-alt me-2"></i> Xóa vĩnh viễn
                                                </button>
                                            </li>
                                        @else
                                            <li>
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
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
            <div class="d-flex justify-content-center mt-3">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
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
            method = 'POST',
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const promoId = this.getAttribute('data-id');

                    Swal.fire({
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                            fetch(route.replace(':id', promoId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) onSuccess();
                                    });
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra. Vui lòng thử lại sau.',
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }

        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm Sản phẩm',
            text: 'Bạn có chắc muốn xoá mềm sản phẩm này?',
            route: '{{ route('admin.products.destroy', ':id') }}',
            method: 'DELETE'
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục Sản phẩm',
            text: 'Khôi phục sản phẩm đã xoá?',
            route: '{{ route('admin.products.restore', ':id') }}',
            method: 'POST'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn Sản phẩm',
            text: 'Bạn có chắc muốn xoá vĩnh viễn? Hành động không thể hoàn tác!',
            route: '{{ route('admin.products.forceDelete', ':id') }}',
            method: 'DELETE'
        });
    </script>
@endsection
