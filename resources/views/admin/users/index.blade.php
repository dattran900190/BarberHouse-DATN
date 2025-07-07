@extends('layouts.AdminLayout')

@section('title', 'Quản lý Người dùng & Quản trị viên')

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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h3 class="card-title mb-0">Quản lý Người dùng & Quản trị viên</h3>
        </div>

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
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Danh sách Người dùng</h4>
                        <a href="{{ route('users.create', ['role' => 'user']) }}"
                            class="btn btn-success btn-sm btn-icon-toggle">
                            <i class="fas fa-plus mr-1"></i> Thêm người dùng
                        </a>
                    </div>

                    <form action="{{ route('users.index', ['role' => 'user']) }}" method="GET" class="mb-4">
                        <input type="hidden" name="role_filter" value="user">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-left"
                                placeholder="Tìm kiếm theo tên hoặc email..." value="{{ $role == 'user' ? $search : '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary rounded-right" type="submit">
                                    <i class="fas fa-search mr-1"></i> Tìm
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
                                @if ($users->count())
                                    @foreach ($users as $index => $user)
                                        <tr>
                                            <td>{{ $users->firstItem() + $index }}</td>
                                            <td class="text-center">
                                                @if ($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                                                        class="rounded-circle img-fluid avatar-img">
                                                @else
                                                    <div
                                                        class="rounded-circle bg-secondary d-flex align-items-center justify-content-center avatar-placeholder">
                                                        <span class="text-white">N/A</span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?? 'Không có' }}</td>
                                            <td>{{ $user->gender ?? 'Không xác định' }}</td>
                                            <td>{{ $user->address ?? 'Không có' }}</td>
                                            <td>{{ $user->role }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-pill
                                                {{ $user->status == 'active'
                                                    ? 'badge-success'
                                                    : ($user->status == 'inactive'
                                                        ? 'badge-warning'
                                                        : 'badge-danger') }}">
                                                    {{ $user->status }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="{{ route('users.show', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}"
                                                        class="btn btn-info btn-sm d-inline-flex align-items-center">
                                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                                    </a>
                                                    <a href="{{ route('users.edit', ['user' => $user->id, 'role' => 'user', 'page' => request('page', 1)]) }}"
                                                        class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-secondary btn-sm toggle-status-btn d-inline-flex align-items-center"
                                                        data-id="{{ $user->id }}" data-status="{{ $user->status }}"
                                                        data-role="{{ $user->role }}">
                                                        <i class="fas fa-ban mr-1"></i>
                                                        <span>{{ $user->status === 'active' ? 'Chặn' : 'Bỏ chặn' }}</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Không tìm thấy người dùng nào phù
                                            hợp.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->appends(['role' => 'user', 'role_filter' => 'user', 'search' => $role == 'user' ? $search : null])->links() }}
                    </div>
                </div>

                <!-- Quản trị viên Tab -->
                <div class="tab-pane fade {{ $role == 'admin' ? 'show active' : '' }}" id="admins" role="tabpanel"
                    aria-labelledby="admins-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Danh sách Quản trị viên</h4>
                        <a href="{{ route('users.create', ['role' => 'admin']) }}"
                            class="btn btn-success btn-sm btn-icon-toggle">
                            <i class="fas fa-plus mr-1"></i> Thêm quản trị viên
                        </a>
                    </div>

                    <form action="{{ route('users.index', ['role' => 'admin']) }}" method="GET" class="mb-4">
                        <input type="hidden" name="role_filter" value="admin">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control rounded-left"
                                placeholder="Tìm kiếm theo tên hoặc email..."
                                value="{{ $role == 'admin' ? $search : '' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary rounded-right" type="submit">
                                    <i class="fas fa-search mr-1"></i> Tìm
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
                                                    class="rounded-circle img-fluid avatar-img">
                                            @else
                                                <div
                                                    class="rounded-circle bg-secondary d-flex align-items-center justify-content-center avatar-placeholder">
                                                    <span class="text-white">N/A</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $admin->name }}</td>
                                        <td>{{ $admin->email }}</td>
                                        <td>{{ $admin->phone ?? 'Không có' }}</td>
                                        <td>{{ $admin->gender ?? 'Không xác định' }}</td>
                                        <td>{{ $admin->address ?? 'Không có' }}</td>
                                        <td>{{ $admin->role }}</td>
                                        <td>
                                            <span
                                                class="badge badge-pill
                                                {{ $admin->status == 'active'
                                                    ? 'badge-success'
                                                    : ($admin->status == 'inactive'
                                                        ? 'badge-warning'
                                                        : 'badge-danger') }}">
                                                {{ $admin->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center flex-wrap btn-group-sm">
                                                <a href="{{ route('users.show', ['user' => $admin->id, 'role' => 'admin']) }}"
                                                    class="btn btn-info btn-sm d-inline-flex align-items-center">
                                                    <i class="fas fa-eye"></i> <span>Xem</span>
                                                </a>
                                                <a href="{{ route('users.edit', ['user' => $admin->id, 'role' => 'admin']) }}"
                                                    class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                                    <i class="fas fa-edit"></i> <span>Sửa</span>
                                                </a>
                                                {{-- <form
                                                    action="{{ route('users.destroy', ['user' => $admin->id, 'role' => 'admin']) }}"
                                                    method="POST" class="d-inline m-0"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá quản trị viên này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                                        <i class="fas fa-trash"></i> <span>Xoá</span>
                                                    </button>
                                                </form> --}}
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

                    <div class="mt-3">
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
                            // Cập nhật text trong badge
                            const row = btn.closest('tr');
                            const badge = row.querySelector('.badge');
                            badge.textContent = data.status;
                            badge.className = 'badge badge-pill ' + data.badge_class;

                            // Cập nhật text của nút
                            btn.querySelector('span').textContent = data.button_label;
                            btn.dataset.status = data.status;
                        })
                        .catch(err => {
                            console.error('Lỗi cập nhật trạng thái:', err);
                            alert('Cập nhật thất bại. Vui lòng thử lại.');
                        });
                    const badge = row.querySelector('.badge');

                    // Xóa các màu cũ
                    badge.classList.remove('badge-success', 'badge-warning', 'badge-danger');

                    // Thêm màu mới
                    badge.classList.add(data.badge_class);

                    // Cập nhật nội dung
                    badge.textContent = data.status;
                });
            });
        });
    </script>


@endsection
