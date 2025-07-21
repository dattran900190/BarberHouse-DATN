@extends('layouts.AdminLayout')

@section('title', 'Quản lý Dịch vụ')

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
        <h3 class="fw-bold mb-3">Dịch vụ cắt tóc</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/services') }}">Dịch vụ</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách dịch vụ</div>

            <a href="{{ route('services.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm dịch vụ</span>
            </a>

        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('services.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">

                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên dịch vụ..."
                        value="{{ request('search') }}" class="form-control pe-5">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>

                <select name="filter" id="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả dịch vụ</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>

            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Stt</th>
                            <th>Tên dịch vụ</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Thời gian</th>
                            <th>Combo?</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($services->count())
                            @foreach ($services as $index => $service)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->description ? $service->description : 'Không có mô tả' }}</td>
                                    <td>{{ number_format($service->price, 0, ',', '.') }}đ</td>
                                    <td>{{ $service->duration }} phút</td>
                                    <td>{{ $service->is_combo ? 'Có' : 'Không' }}</td>
                                    <td>
                                        @if ($service->trashed())
                                            <span class="badge bg-danger">Đã xoá</span>
                                        @else
                                            <span class="badge bg-success">Còn hoạt động</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                id="actionMenu{{ $service->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="actionMenu{{ $service->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('services.show', ['service' => $service->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a>
                                                </li>
                                                @if (!$service->trashed())
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('services.edit', ['service' => $service->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                </li>
                                                @endif
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>

                                                <li>
                                                    @if ($service->trashed())
                                                        <button type="button"
                                                            class="dropdown-item text-success restore-btn"
                                                            data-id="{{ $service->id }}">
                                                            <i class="fas fa-undo me-2"></i> Khôi phục
                                                        </button>
                                                        <button type="button"
                                                            class="dropdown-item text-danger force-delete-btn"
                                                            data-id="{{ $service->id }}">
                                                            <i class="fas fa-trash-alt me-2"></i> Xoá vĩnh viễn
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            class="dropdown-item text-danger soft-delete-btn"
                                                            data-id="{{ $service->id }}">
                                                            <i class="fas fa-times me-2"></i> Xoá mềm
                                                        </button>
                                                    @endif
                                                </li>

                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không tìm thấy dịch vụ nào phù hợp.</td>
                            </tr>
                        @endif
                    </tbody>

                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $services->links() }}
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

        // Xoá mềm
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm dịch vụ',
            text: 'Bạn có chắc chắn muốn xoá mềm dịch vụ này?',
            route: '{{ route('services.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Xoá cứng
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn dịch vụ này? Hành động này không thể hoàn tác.',
            route: '{{ route('services.destroy', ':id') }}',
            method: 'DELETE'
        });

        // Khôi phục
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục dịch vụ',
            text: 'Bạn có chắc chắn muốn khôi phục dịch vụ này?',
            route: '{{ route('services.restore', ':id') }}',
            method: 'POST'
        });
    </script>
@endsection
