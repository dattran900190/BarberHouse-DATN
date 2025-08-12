@extends('layouts.AdminLayout')

@section('title', 'Quản lý người dùng')

@section('content')
    <div id="alerts-container">
        @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
            @if (session($key))
                <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                    {{ session($key) }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        @endforeach
    </div>

    @php
        $currentRole = Auth::user()->role;
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">{{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
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
                <a href="{{ url('admin/users') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}">Quản lý người dùng</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách {{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</div>
            <div class="d-flex gap-2 ms-auto">
                @if ($currentRole == 'admin')
                    <a href="{{ route('users.create', ['role' => $role]) }}"
                        class="btn btn-sm btn-outline-success d-flex align-items-center">
                        <i class="fas fa-plus"></i>
                        <span class="ms-2">Thêm {{ $role == 'user' ? 'người dùng' : 'quản trị viên' }}</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            <!-- Tabs -->
            <ul class="nav nav-tabs custom-tabs mb-4" id="userTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $role == 'user' ? 'active' : '' }}"
                        href="{{ route('users.index', array_merge(request()->except('role', 'page'), ['role' => 'user'])) }}">
                        <i class="fas fa-users mr-1"></i> Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $role == 'admin' ? 'active' : '' }}"
                        href="{{ route('users.index', array_merge(request()->except('role', 'page'), ['role' => 'admin'])) }}">
                        <i class="fas fa-user-shield mr-1"></i> Quản trị viên
                    </a>
                </li>
            </ul>

            <!-- Search and Filter Form -->
            <form method="GET" action="{{ route('users.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <input type="hidden" name="role" value="{{ $role }}">

                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên hoặc email..."
                        value="{{ $search }}" class="form-control pe-5">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>

                <select name="filter" id="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả người dùng</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Chưa kích hoạt</option>
                    <option value="banned" {{ request('filter') == 'banned' ? 'selected' : '' }}>Đã chặn</option>
                </select>
            </form>

            <!-- Tab Content -->
            <div class="tab-content" id="userTabsContent">
                <!-- Người dùng Tab -->
                <div class="tab-pane fade {{ $role == 'user' ? 'show active' : '' }}" id="users" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light text-center align-middle">
                                <tr>
                                    <th>STT</th>
                                    <th>Ảnh đại diện</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Giới tính</th>
                                    <th>Địa chỉ</th>
                                    <th>Trạng thái</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if ($users->count())
                                    @foreach ($users as $index => $user)
                                        <tr>
                                            <td>{{ $users->firstItem() + $index }}</td>
                                            <td class="text-center">
                                                @if ($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                                        class="img-thumbnail"
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <div class="bg-secondary d-flex align-items-center justify-content-center avatar-placeholder"
                                                        style="width: 40px; height: 40px; border-radius: 5px;">
                                                        <span class="text-white" style="font-size: 0.8rem;">N/A</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?? 'Không có' }}</td>
                                            <td>{{ $user->gender == 'male' ? 'Nam' : ($user->gender == 'female' ? 'Nữ' : 'Khác') }}
                                            </td>
                                            <td>{{ $user->address ?? 'Không có' }}</td>
                                            <td>
                                                @if ($user->trashed())
                                                    <span class="badge bg-danger">Đã chặn</span>
                                                @else
                                                    <span
                                                        class="badge {{ $user->status === 'active' ? 'bg-success' : ($user->status === 'inactive' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $user->status === 'active' ? 'Hoạt động' : ($user->status === 'inactive' ? 'Chưa kích hoạt' : 'Đã chặn') }}
                                                    </span>
                                                @endif
                                            </td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenu{{ $user->id }}" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenu{{ $user->id }}">

                                                            @if ($currentRole === 'admin')
                                                                @if ($user->trashed())
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.show', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}">
                                                                            <i class="fas fa-eye me-2"></i> Xem
                                                                        </a>
                                                                    </li>
                                                                    <hr class="dropdown-divider">
                                                                    <li>
                                                                        <button type="button"
                                                                            class="dropdown-item text-success restore-btn"
                                                                            data-id="{{ $user->id }}">
                                                                            <i class="fas fa-undo me-2"></i> Bỏ chặn
                                                                        </button>
                                                                        {{-- <button type="button"
                                                                            class="dropdown-item text-danger force-delete-btn"
                                                                            data-id="{{ $user->id }}"
                                                                            data-role="{{ $currentRole }}">
                                                                            <i class="fas fa-trash-alt me-2"></i> Xóa vĩnh
                                                                            viễn
                                                                        </button> --}}
                                                                    </li>
                                                                @else
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.show', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}">
                                                                            <i class="fas fa-eye me-2"></i> Xem
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.edit', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}">
                                                                            <i class="fas fa-edit me-2"></i> Sửa
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <button type="button"
                                                                            class="dropdown-item text-danger soft-delete-btn"
                                                                            data-id="{{ $user->id }}">
                                                                            <i class="fas fa-times me-2"></i> Chặn
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Không tìm thấy người dùng nào
                                            phù hợp.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->appends(['role' => 'user', 'role_filter' => 'user', 'search' => $search])->links() }}
                    </div>
                </div>

                <!-- Quản trị viên Tab -->
                <div class="tab-pane fade {{ $role == 'admin' ? 'show active' : '' }}" id="admins" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="thead-light text-center align-middle">
                                <tr>
                                    <th>STT</th>
                                    <th>Ảnh đại diện</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Giới tính</th>
                                    <th>Địa chỉ</th>
                                    <th>Trạng thái</th>
                                    @if ($currentRole == 'admin')
                                        <th class="text-center">Hành động</th>
                                    @endif

                                </tr>
                            </thead>
                            <tbody>
                                @if ($admins->count())
                                    @foreach ($admins as $index => $admin)
                                        <tr>
                                            <td>{{ $admins->firstItem() + $index }}</td>
                                            <td class="text-center">
                                                @if ($admin->avatar)
                                                    <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar"
                                                        class="img-fluid avatar-img"
                                                        style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                                @else
                                                    <div class="bg-secondary d-flex align-items-center justify-content-center avatar-placeholder"
                                                        style="width: 40px; height: 40px; border-radius: 5px;">
                                                        <span class="text-white" style="font-size: 0.8rem;">N/A</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>{{ $admin->phone ?? 'Không có' }}</td>
                                            <td>{{ $admin->gender == 'male' ? 'Nam' : ($admin->gender == 'female' ? 'Nữ' : 'Khác') }}
                                            </td>
                                            <td>{{ $admin->address ?? 'Không có' }}</td>
                                            <td>
                                                @if ($admin->trashed())
                                                    <span class="badge bg-danger">Đã chặn</span>
                                                @else
                                                    <span
                                                        class="badge {{ $admin->status === 'active' ? 'bg-success' : ($admin->status === 'inactive' ? 'bg-warning' : 'bg-danger') }}">
                                                        {{ $admin->status === 'active' ? 'Hoạt động' : ($admin->status === 'inactive' ? 'Không hoạt động' : 'Đã chặn') }}
                                                    </span>
                                                @endif
                                            </td>
                                            @if ($currentRole == 'admin')
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenu{{ $admin->id }}" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenu{{ $admin->id }}">

                                                            @if ($currentRole === 'admin')
                                                                @if ($admin->trashed())
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.show', ['user' => $admin->id, 'role' => 'admin', 'page' => request('page', 1)]) }}">
                                                                            <i class="fas fa-eye me-2"></i> Xem
                                                                        </a>
                                                                    </li>
                                                                    <hr class="dropdown-divider">
                                                                    <li>
                                                                        <button type="button"
                                                                            class="dropdown-item text-success restore-btn"
                                                                            data-id="{{ $admin->id }}">
                                                                            <i class="fas fa-undo me-2"></i> Bỏ chặn
                                                                        </button>
                                                                    </li>
                                                                    {{-- <button type="button"
                                                                        class="dropdown-item text-danger force-delete-btn"
                                                                        data-id="{{ $admin->id }}"
                                                                        data-role="{{ $currentRole }}">
                                                                        <i class="fas fa-trash-alt me-2"></i> Xóa vĩnh
                                                                        viễn
                                                                    </button> --}}
                                                                @else
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.show', ['user' => $admin->id, 'role' => 'admin', 'page' => request('page', 1)]) }}">
                                                                            <i class="fas fa-eye me-2"></i> Xem
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item"
                                                                            href="{{ route('users.edit', ['user' => $admin->id, 'role' => 'admin', 'page' => request('page', 1)]) }}">
                                                                            <i class="fas fa-edit me-2"></i> Sửa
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <button type="button"
                                                                            class="dropdown-item text-danger soft-delete-btn"
                                                                            data-id="{{ $admin->id }}">
                                                                            <i class="fas fa-times me-2"></i> Chặn
                                                                        </button>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-3">Không có dữ liệu</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $admins->appends(['role' => 'admin', 'role_filter' => 'admin', 'search' => $search])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <form id="forceDeleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/user.css') }}">
    <style>
        img.avatar-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
        }

        .avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 5px;
            font-size: 0.8rem;
        }

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
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const userId = this.getAttribute('data-id');

                    Swal.fire({
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Vui lòng chờ...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading(),
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });

                            fetch(route.replace(':id', userId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(async response => {
                                    const contentType = response.headers.get(
                                        'content-type');

                                    let data = {};
                                    if (contentType && contentType.includes(
                                            'application/json')) {
                                        data = await response.json();
                                    } else {
                                        const text = await response.text();
                                        data.message = text ||
                                            'Lỗi không xác định từ server.';
                                    }

                                    Swal.close();

                                    if (response.ok) {
                                        Swal.fire({
                                            icon: data.success ? 'success' :
                                                'error',
                                            title: data.success ? 'Thành công' :
                                                'Thất bại',
                                            text: data.message ||
                                                'Thao tác đã hoàn tất.',
                                            customClass: {
                                                popup: 'custom-swal-popup'
                                            }
                                        }).then(() => {
                                            if (data.success) onSuccess();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: `Lỗi ${response.status}`,
                                            text: data.message ||
                                                'Đã xảy ra lỗi không xác định.',
                                            customClass: {
                                                popup: 'custom-swal-popup'
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: error.message ||
                                            'Đã xảy ra lỗi không xác định.',
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
            title: 'Chặn người dùng',
            text: 'Bạn có chắc chắn muốn chặn người dùng này?',
            route: '{{ route('users.softDelete', ':id') }}',
            method: 'DELETE'
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Bỏ chặn người dùng',
            text: 'Bỏ chặn người dùng này?',
            route: '{{ route('users.restore', ':id') }}',
            method: 'POST'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xóa vĩnh viễn',
            text: 'Hành động này sẽ xóa vĩnh viễn người dùng. Không thể khôi phục.',
            route: '{{ route('users.destroy', ':id') }}',
            method: 'DELETE'
        });
    </script>
@endsection
