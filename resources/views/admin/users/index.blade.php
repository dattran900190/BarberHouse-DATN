@extends('adminlte::page')

@section('title', 'Quản lý Người dùng')

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

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Người dùng</h3>
            <a href="{{ route('admin.users.create') }}"
                class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm người dùng</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.users.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

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
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Số điểm</th>
                        <th>Ngày tạo</th>
                        <th>Ngày cập nhật</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td class="text-center">
                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" width="50" height="50"
                                        class="rounded-circle">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'Không có' }}</td>
                            <td>{{ $user->gender ?? 'Không xác định' }}</td>
                            <td>{{ $user->address ?? 'Không có' }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                <span class="badge 
                                    {{ $user->status == 'active' ? 'badge-success' : 
                                       ($user->status == 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ $user->status }}
                                </span>
                            </td>
                            <td>{{ $user->points_balance }}</td>
                            <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                        class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                        class="d-inline m-0"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá người dùng này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                            <i class="fas fa-trash"></i> <span>Xoá</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($users->isEmpty())
                        <tr>
                            <td colspan="15" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->withQueryString()->links() }}
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

        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
        }

        .badge-danger {
            background-color: #dc3545;
        }
    </style>
@endsection