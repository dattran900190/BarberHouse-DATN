@extends('layouts.AdminLayout')

@section('title', 'Danh sách dung tích')

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
        <h3 class="fw-bold mb-3">Dung tích</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes') }}">Quản lý đặt hàng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/volumes') }}">Dung tích</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0  flex-grow-1">Danh sách dung tích sản phẩm</h3>
            <a href="{{ route('admin.volumes.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm dung tích sản phẩm</span>
            </a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.volumes.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <select name="filter" id="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Stt</th>
                            <th>Tên</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($volumes as $volume)
                            <tr>
                                <td>{{ $loop->iteration + $volumes->firstItem() - 1 }}</td>
                                <td>{{ $volume->name }}</td>
                                <td>
                                    @if ($volume->trashed())
                                        <span class="badge bg-danger">Đã xoá</span>
                                    @else
                                        <span class="badge bg-success">Còn hoạt động</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="actionMenu{{ $volume->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="actionMenu{{ $volume->id }}">
                                            <li>
                                                <a href="{{ route('admin.volumes.edit', $volume) }}?page={{ request()->get('page') }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-edit me-2"></i> Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            @if ($volume->trashed())
                                                <li>
                                                    <button type="button" class="dropdown-item text-success restore-btn" data-id="{{ $volume->id }}">
                                                        <i class="fas fa-undo me-2"></i> Khôi phục
                                                    </button>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.volumes.forceDelete', $volume->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="dropdown-item text-danger force-delete-btn"
                                                            data-id="{{ $volume->id }}">
                                                            <i class="fas fa-trash-alt me-2"></i> Xóa vĩnh viễn
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form
                                                        action="{{ route('admin.volumes.destroy', $volume) }}?page={{ request()->get('page') }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="dropdown-item text-danger soft-delete-btn"
                                                            data-id="{{ $volume->id }}">
                                                            <i class="fas fa-times me-2"></i> Xóa mềm
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Không có dữ liệu dung tích.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $volumes->appends(request()->query())->links() }}
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
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                                         const recordId = this.getAttribute('data-id');

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
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                                                         fetch(route.replace(':id', recordId), {
                                     method,
                                     headers: {
                                         'Accept': 'application/json',
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                     }
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
            title: 'Xoá mềm Dung tích',
            text: 'Bạn có chắc muốn xoá mềm dung tích này?',
            route: '{{ route('admin.volumes.destroy', ':id') }}',
            method: 'DELETE',
            successMessage: 'Đã xóa mềm dung tích thành công!'
        });
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn Dung tích',
            text: 'Bạn có chắc muốn xoá vĩnh viễn? Hành động không thể hoàn tác!',
            route: '{{ route('admin.volumes.forceDelete', ':id') }}',
            method: 'DELETE',
            successMessage: 'Đã xóa vĩnh viễn dung tích thành công!'
        });
        
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục Dung tích',
            text: 'Bạn có chắc muốn khôi phục dung tích này?',
            route: '{{ route('admin.volumes.restore', ':id') }}',
            method: 'POST',
            successMessage: 'Đã khôi phục dung tích thành công!'
        });
    </script>
@endsection
