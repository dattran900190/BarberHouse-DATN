@extends('layouts.AdminLayout')

@section('title', 'Danh sách Chi nhánh')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @php
        $currentRole = Auth::user()->role;
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3">Danh sách Chi nhánh</h3>
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
                <a href="{{ url('admin/branches') }}">Quản lý Chi nhánh</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách chi nhánh</div>
            @if ($currentRole == 'admin')
                <a href="{{ route('branches.create') }}"
                    class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                    <i class="fas fa-plus"></i>
                    <span class="ms-2">Thêm chi nhánh</span>
                </a>
            @endif
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('branches.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">

                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên chi nhánh..."
                        value="{{ request('search') }}" class="form-control pe-5">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>

                <select name="filter" id="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả chi nhánh</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>STT</th>
                            <th>Ảnh</th>
                            <th>Tên chi nhánh</th>
                            <th>Địa chỉ</th>
                            <th>SĐT</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $index => $branch)
                            <tr>
                                <td>{{ ($branches->currentPage() - 1) * $branches->perPage() + $loop->iteration }}</td>
                                <td>
                                    @if ($branch->image)
                                        <img src="{{ asset('storage/' . $branch->image) }}" alt="Ảnh"
                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->phone }}</td>
                                <td>
                                    @if ($branch->trashed())
                                        <span class="badge bg-danger">Đã xoá</span>
                                    @else
                                        <span class="badge bg-success">Còn hoạt động</span>
                                    @endif
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="branchActions{{ $branch->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="branchActions{{ $branch->id }}">
                                            {{-- <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('branches.show', ['branch' => $branch->id, 'page' => request('page', 1)]) }}"> --}}

                                            <li>
                                                @if ($branch->trashed())
                                                    <button type="button" class="dropdown-item text-success restore-btn"
                                                        data-id="{{ $branch->id }}">
                                                        <i class="fas fa-undo me-2"></i> Khôi phục
                                                    </button>
                                                    <button type="button"
                                                        class="dropdown-item text-danger force-delete-btn"
                                                        data-id="{{ $branch->id }}">
                                                        <i class="fas fa-trash-alt me-2"></i> Xoá vĩnh viễn
                                                    </button>
                                                @else
                                                    <a class="dropdown-item"
                                                        href="{{ route('branches.show', ['branch' => $branch->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('branches.edit', ['branch' => $branch->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                    <hr class="dropdown-divider">
                                                    <button type="button" class="dropdown-item text-danger soft-delete-btn"
                                                        data-id="{{ $branch->id }}">
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
                                <td colspan="7" class="text-muted">Không tìm thấy chi nhánh phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
        }

        .dropdown-menu i {
            width: 20px;
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
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const branchId = this.getAttribute('data-id');
                    Swal.fire({
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        width: '400px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                            fetch(route.replace(':id', branchId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error'
                                    }).then(() => {
                                        if (data.success) onSuccess();
                                    });
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error'
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
            title: 'Xoá mềm chi nhánh',
            text: 'Bạn có chắc chắn muốn xoá mềm chi nhánh này?',
            route: '{{ route('branches.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Xoá cứng
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn chi nhánh này? Hành động này không thể hoàn tác.',
            route: '{{ route('branches.destroy', ':id') }}',
            method: 'DELETE'
        });

        // Khôi phục
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục chi nhánh',
            text: 'Bạn có chắc chắn muốn khôi phục chi nhánh này?',
            route: '{{ route('branches.restore', ':id') }}',
            method: 'POST'
        });
    </script>
@endsection
