@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Danh mục')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Danh mục sản phẩm</h3>
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
                <a href="{{ url('admin/product_categories') }}">Quản lý đặt hàng</a>
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
                <a href="{{ url('admin/product_categories/' . $category->id) }}">Chi tiết danh mục</a>
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
                    <h4 class="fw-bold mb-3">{{ $category->name }}</h4>

                    <p class="text-muted mb-2">
                        <i class="fa fa-link me-2 text-primary"></i><strong>Slug:</strong> {{ $category->slug }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-info-circle me-2 text-info"></i><strong>Mô tả:</strong>
                        {{ $category->description ?? 'Không có mô tả' }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-calendar me-2 text-muted"></i><strong>Ngày tạo:</strong>
                        {{ $category->created_at->format('d/m/Y H:i') }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-clock me-2 text-secondary"></i><strong>Ngày cập nhật:</strong>
                        {{ $category->updated_at->format('d/m/Y H:i') }}
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-check-circle me-2 text-success"></i><strong>Trạng thái:</strong>
                        @if ($category->deleted_at)
                            <span class="badge bg-danger">Đã xoá mềm</span>
                        @else
                            <span class="badge bg-success">Đang hoạt động</span>
                        @endif
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
                @if (!$category->trashed())
                    <a href="{{ route('product_categories.edit', $category->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                @endif
                {{-- Nút xoá mềm --}}
                @if (!$category->trashed())
                    <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $category->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                @endif

                {{-- Nút khôi phục --}}
                @if ($category->trashed())
                    <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $category->id }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>

                    {{-- Nút xoá vĩnh viễn --}}
                    <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $category->id }}">
                        <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                    </button>
                @endif

                <a href="{{ route('product_categories.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script>
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false,
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const categoryId = this.getAttribute('data-id');

                    const swalOptions = {
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    };

                    if (withInput) {
                        swalOptions.input = 'textarea';
                        swalOptions.inputPlaceholder = inputPlaceholder;
                        if (inputValidator) {
                            swalOptions.inputValidator = inputValidator;
                        }
                    }

                    Swal.fire(swalOptions).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ? JSON.stringify({
                                no_show_reason: result.value || 'Không có lý do'
                            }) : undefined;

                            fetch(route.replace(':id', categoryId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => response.json())
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
                                    console.error('Error:', error);
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
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

        // Khởi tạo các nút
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm danh mục',
            text: 'Bạn có chắc muốn xoá mềm danh mục này?',
            route: '{{ route('product_categories.softDelete', ':id') }}',
            method: 'PATCH'
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục danh mục',
            text: 'Bạn có muốn khôi phục danh mục này?',
            route: '{{ route('product_categories.restore', ':id') }}',
            method: 'POST'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn danh mục',
            text: 'Hành động này không thể khôi phục. Tiếp tục?',
            route: '{{ route('product_categories.destroy', ':id') }}',
            method: 'DELETE',
            onSuccess: () => {
                window.location.href = '{{ route('product_categories.index') }}';
            }
        });
    </script>
@endsection
