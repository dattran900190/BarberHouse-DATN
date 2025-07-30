@extends('layouts.AdminLayout')

@section('title', 'Quản lý Danh mục sản phẩm')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
                <a href="{{ url('admin/product_categories') }}">Quản lý đặt hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('product_categories.index') }}">Danh mục</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách danh mục</div>
            <a href="{{ route('product_categories.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm danh mục</span>
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('product_categories.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">

                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên hoặc slug..."
                        value="{{ request('search') }}" class="form-control pe-5">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>

                <select name="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả danh mục</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>

            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center align-middle">
                        <tr>
                            <th>STT</th>
                            <th>Tên danh mục</th>
                            <th>Slug</th>
                            <th>Mô tả</th>
                            <th>Ngày tạo</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $index => $category)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ Str::limit($category->description, 100) }}</td>
                                <td class="text-center">{{ $category->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if ($category->trashed())
                                        <span class="badge bg-danger">Đã xoá</span>
                                    @else
                                        <span class="badge bg-success">Còn hoạt động</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="actionMenu{{ $category->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="actionMenu{{ $category->id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('product_categories.show', $category->id) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('product_categories.edit', $category->id) }}">
                                                    <i class="fas fa-edit me-2"></i> Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                @if ($category->trashed())
                                                    <button type="button" class="dropdown-item text-success restore-btn"
                                                        data-id="{{ $category->id }}">
                                                        <i class="fas fa-undo me-2"></i> Khôi phục
                                                    </button>
                                                    <button type="button"
                                                        class="dropdown-item text-danger force-delete-btn"
                                                        data-id="{{ $category->id }}">
                                                        <i class="fas fa-trash-alt me-2"></i> Xoá vĩnh viễn
                                                    </button>
                                                @else
                                                    <button type="button" class="dropdown-item text-danger soft-delete-btn"
                                                        data-id="{{ $category->id }}">
                                                        <i class="fas fa-times me-2"></i> Xoá mềm
                                                    </button>
                                                @endif
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Chưa có danh mục nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $categories->links() }}
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
    </style>
@endsection

@section('js')
    <script>
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false, // Thêm tùy chọn hiển thị input
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload() // Thay reload bằng callback linh hoạt
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const appointmentId = this.getAttribute('data-id');

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
                                    popup: 'custom-swal-popup' // CSS
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ? JSON.stringify({
                                no_show_reason: result.value || 'Không có lý do'
                            }) : undefined;

                            fetch(route.replace(':id', appointmentId), {
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
            text: 'Khôi phục danh mục đã xoá?',
            route: '{{ route('product_categories.restore', ':id') }}',
            method: 'POST'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc muốn xoá vĩnh viễn? Hành động không thể hoàn tác!',
            route: '{{ route('product_categories.destroy', ':id') }}',
            method: 'DELETE'
        });
    </script>
@endsection
