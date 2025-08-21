@extends('layouts.AdminLayout')

@section('title', 'Quản lý Sản phẩm')
@section('content')

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Sản phẩm</h3>
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


    <!-- Card: Chi tiết sản phẩm -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Thông tin sản phẩm</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-6">
                    <i class="fa fa-tag me-2 text-muted"></i>
                    <strong>Tên:</strong> {{ $product->name }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-boxes me-2 text-primary"></i>
                    <strong>Danh mục:</strong> {{ $product->category->name ?? 'Không có' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-money-bill-wave me-2 text-success"></i>
                    <strong>Giá đại diện:</strong> {{ number_format($product->price) }} VNĐ
                </div>
                <div class="col-md-6">
                    <i class="fa fa-warehouse me-2 text-info"></i>
                    <strong>Tồn kho:</strong> {{ $product->stock }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-align-left me-2 text-warning"></i>
                    <strong>Mô tả:</strong> {{ $product->description ?? 'Không có' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-align-justify me-2 text-muted"></i>
                    <strong>Mô tả dài:</strong> {{ $product->long_description ?? 'Không có' }}
                </div>
                <div class="col-md-6">
                    <i
                        class="fa fa-toggle-{{ $product->deleted_at ? 'off' : 'on' }} me-2 text-{{ $product->deleted_at ? 'danger' : 'success' }}"></i>
                    <strong>Trạng thái:</strong>
                    <span class="badge bg-{{ $product->deleted_at ? 'danger' : 'success' }}">
                        {{ $product->deleted_at ? 'Đã xóa' : 'Hoạt động' }}
                    </span>
                    @if($product->deleted_at)
                        <br><small class="text-muted">Xóa lúc: {{ $product->deleted_at->format('d/m/Y H:i:s') }}</small>
                    @endif
                </div>
                <div class="col-md-12">
                    <i class="fa fa-image me-2 text-primary"></i>
                    <strong>Ảnh chính:</strong><br>
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="Ảnh chính"
                            style="max-height: 120px; width: 120px; object-fit: cover; border-radius: 10px;">
                    @else
                        <span>Không có ảnh chính</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <i class="fa fa-images me-2 text-info"></i>
                    <strong>Ảnh bổ sung:</strong>
                    @if ($product->images->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($product->images as $img)
                                <img src="{{ asset('storage/' . $img->image_url) }}" alt="Ảnh bổ sung"
                                    style="max-height: 100px; width: 100px; object-fit: cover; border-radius: 10px;">
                            @endforeach
                        </div>
                    @else
                        <span>Không có ảnh bổ sung</span>
                    @endif
                </div>
            </div>
            <div class="mt-4">
                <h5 class="text-primary">Biến thể</h5>
                @if ($product->variants->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-bordered mt-2">
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
                                                <img src="{{ asset('storage/' . $variant->image) }}" alt="Ảnh biến thể"
                                                     style="max-height: 80px; width: 80px; object-fit: cover; border-radius: 10px;">
                                            @else
                                                <span>Không có</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>Không có biến thể</p>
                @endif
            </div>

        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                @if($product->deleted_at)
                    <!-- Nếu sản phẩm đã xóa mềm -->
                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-success btn-sm restore-btn"
                        data-id="{{ $product->id }}">
                            
                            <i class="fa fa-undo me-1"></i> Khôi phục
                        </button>
                    </form>
                    <form action="{{ route('admin.products.forceDelete', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"   class="btn btn-outline-danger btn-sm force-delete-btn"
                        data-id="{{ $product->id }}">
                            <i class="fa fa-trash me-1"></i> Xóa vĩnh viễn
                        </button>
                    </form>
                @else
                    <!-- Nếu sản phẩm đang hoạt động -->
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Chỉnh sửa
                    </a>
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm soft-delete-btn"
                        data-id="{{ $product->id }}">
                            <i class="fa fa-trash me-1"></i> Xóa
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
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

