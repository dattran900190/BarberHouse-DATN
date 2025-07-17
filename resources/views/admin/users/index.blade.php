@extends('layouts.AdminLayout')

@section('title', 'Quản lý người dùng')

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
    @php
        $currentRole = Auth::user()->role;
    @endphp
    <div class="page-header">
        <h3 class="fw-bold mb-3">{{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}">Quản lý người dùng</a>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <!-- Tabs -->
            <ul class="nav nav-tabs custom-tabs" id="userTabs" role="tablist">
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

            <!-- Tab Content -->
            <div class="tab-content mt-3" id="userTabsContent">
                <!-- Người dùng Tab -->
                <div class="tab-pane fade {{ $role == 'user' ? 'show active' : '' }}" id="users" role="tabpanel"
                    aria-labelledby="users-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h4 class="mb-0">Danh sách Người dùng</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('users.create', ['role' => 'user']) }}"
                                class="btn btn-sm btn-outline-success d-flex align-items-center">
                                <i class="fas fa-plus"></i>
                                <span class="ms-2">Thêm người dùng</span>
                            </a>
                            <a class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                href="{{ route('users.trashed', ['role' => 'user']) }}">
                                <i class="fas fa-trash"></i>
                                <span class="ms-2">Người dùng đã xoá</span>
                            </a>
                        </div>
                    </div>


                    <form action="{{ route('users.index', ['role' => 'user']) }}" method="GET" class="mb-4">
                        <input type="hidden" name="role_filter" value="user">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-left"
                                placeholder="Tìm kiếm theo tên hoặc email..." value="{{ $role == 'user' ? $search : '' }}">
                            <div class="input-group-append">
                                <button type="submit"
                                    class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover custom-table">
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
                                    <th>Hành động</th>
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
                                                        class="img-fluid avatar-img"
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
                                                <span
                                                    class="badge badge-pill badge-status-fixed
                                                    {{ $user->status === 'active' ? 'badge-success' : ($user->status === 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                                                    {{ $user->status === 'active' ? 'Hoạt động' : ($user->status === 'inactive' ? 'Không hoạt động' : 'Bị khóa') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenu{{ $user->id }}" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <div class="dropdown-menu"
                                                            aria-labelledby="dropdownMenuButton{{ $user->id }}">
                                                            <a class="dropdown-item"
                                                                href="{{ route('users.edit', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}">
                                                                <i class="fas fa-edit"></i> Sửa
                                                            </a>
                                                            <a class="dropdown-item"
                                                                href="{{ route('users.show', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}">
                                                                <i class="fas fa-eye"></i> Xem
                                                            </a>
                                                            @if ($currentRole === 'admin')
                                                                <form
                                                                    action="{{ route('users.destroy', ['user' => $user->id, 'role' => $role]) }}"
                                                                    method="POST" style="display:inline;"
                                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger">
                                                                        <i class="fa fa-trash me-1"></i> Xóa
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">Không tìm thấy người dùng nào
                                            phù hợp.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $users->appends(['role' => 'user', 'role_filter' => 'user', 'search' => $role == 'user' ? $search : null])->links() }}
                    </div>
                </div>

                <!-- Quản trị viên Tab -->
                <div class="tab-pane fade {{ $role == 'admin' ? 'show active' : '' }}" id="admins" role="tabpanel"
                    aria-labelledby="admins-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h4 class="mb-0">Danh sách Quản trị viên</h4>
                        <div class="d-flex gap-2 ms-auto">
                            <a href="{{ route('users.create', ['role' => 'admin']) }}"
                                class="btn btn-sm btn-outline-success d-flex align-items-center">
                                <i class="fas fa-plus"></i>
                                <span class="ms-2">Thêm quản trị viên</span>
                            </a>
                            <a class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                href="{{ route('users.trashed', ['role' => 'admin']) }}">
                                <i class="fas fa-trash"></i>
                                <span class="ms-2">Quản trị viên đã xoá</span>
                            </a>
                        </div>
                    </div>


                    <form action="{{ route('users.index', ['role' => 'admin']) }}" method="GET" class="mb-4">
                        <input type="hidden" name="role_filter" value="admin">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-left"
                                placeholder="Tìm kiếm theo tên hoặc email..."
                                value="{{ $role == 'admin' ? $search : '' }}">
                            <div class="input-group-append">
                                <button type="submit"
                                    class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover custom-table">
                            <thead class="thead-light text-center align-middle">
                                <tr>
                                    <th>STT</th>
                                    <th>Ảnh đại diện</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Giới tính</th>
                                    <th>Địa chỉ</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        <td>{{ $admin->role == 'admin' ?? 'admin_branch' ? 'Quản trị viên' : 'Quản lý chi nhánh' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-pill badge-status-fixed
                                                {{ $admin->status === 'active' ? 'badge-success' : ($admin->status === 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $admin->status === 'active' ? 'Hoạt động' : ($admin->status === 'inactive' ? 'Không hoạt động' : 'Bị khóa') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center flex-wrap btn-group-sm">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        id="actionMenu{{ $admin->id }}" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu"
                                                        aria-labelledby="dropdownMenuButton{{ $admin->id }}">
                                                        <a class="dropdown-item"
                                                            href="{{ route('users.edit', ['user' => $admin->id, 'role' => 'admin', 'page' => request('page', 1)]) }}">
                                                            <i class="fas fa-edit"></i> Sửa
                                                        </a>
                                                        <a class="dropdown-item"
                                                            href="{{ route('users.show', ['user' => $admin->id, 'role' => 'admin', 'page' => request('page', 1)]) }}">
                                                            <i class="fas fa-eye"></i> Xem
                                                        </a>
                                                        @if ($currentRole === 'admin')
                                                            <form
                                                                action="{{ route('users.destroy', ['user' => $admin->id, 'role' => $role]) }}"
                                                                method="POST" style="display:inline;"
                                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash me-2"></i> Xoá
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($admins->isEmpty())
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-3">Không có dữ liệu</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $admins->appends(['role' => 'admin', 'role_filter' => 'admin', 'search' => $role == 'admin' ? $search : null])->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
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

        .badge-status-fixed {
            display: inline-block;
            min-width: 80px;
            text-align: center;
            white-space: nowrap;
        }

        .toggle-status-btn {
            min-width: 75px;
            text-align: center;
            white-space: nowrap;
            padding: 6px 10px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease-in-out;
        }

        .toggle-status-btn i {
            margin-right: 6px;
        }
    </style>
@endsection

@section('js')
    <script>
        // Cập nhật URL khi chuyển tab mà không tải lại trang
        document.querySelectorAll('#userTabs .nav-link').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                const role = event.target.id === 'users-tab' ? 'user' : 'admin';
                const url = new URL(window.location);
                url.searchParams.set('role', role);
                window.history.pushState({}, '', url);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Gắn sự kiện cho tất cả các nút toggle trạng thái
            document.querySelectorAll('.toggle-status-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.id;
                    const btn = this;

                    fetch(`/admin/users/${userId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error("Server trả về lỗi");
                            return res.json();
                        })
                        .then(data => {
                            const row = btn.closest('tr');
                            const badge = row.querySelector('.badge');

                            badge.textContent = data.status_label;

                            badge.className = 'badge badge-pill badge-status-fixed ' + data
                                .badge_class;

                            btn.querySelector('span').textContent = data.button_label;
                            btn.dataset.status = data.status;
                        })
                        .catch(err => {
                            console.error('Lỗi cập nhật trạng thái:', err);
                            alert('Cập nhật thất bại. Vui lòng thử lại.');
                        });
                });
            });
        });
    </script>
@endsection
