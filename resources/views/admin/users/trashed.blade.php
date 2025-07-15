@extends('layouts.AdminLayout')

@section('title', 'Người dùng đã xoá')

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
    <div class="page-header mb-3">
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
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.trashed', ['role' => $role]) }}">Người dùng đã xoá</a>
            </li>
        </ul>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <!-- Tabs -->
            <ul class="nav nav-tabs custom-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $role === 'user' ? 'active' : '' }}"
                        href="{{ route('users.trashed', ['role' => 'user']) }}">
                        <i class="fas fa-users me-1"></i> Người dùng
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $role === 'admin' ? 'active' : '' }}"
                        href="{{ route('users.trashed', ['role' => 'admin']) }}">
                        <i class="fas fa-user-shield me-1"></i> Quản trị viên
                    </a>
                </li>
            </ul>

            <!-- Tìm kiếm -->
            <form method="GET" action="{{ route('users.trashed') }}" class="mb-3">
                <input type="hidden" name="role" value="{{ $role }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..."
                        value="{{ $search }}">
                    <button class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            @if ($trashedUsers->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover custom-table align-middle text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>STT</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Ngày xoá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trashedUsers as $index => $user)
                                <tr>
                                    <td>{{ $trashedUsers->firstItem() + $index }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $user->role === 'user' ? 'Người dùng' : 'Quản trị viên' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->deleted_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <form method="POST"
                                            action="{{ route('users.restore', ['id' => $user->id, 'role' => $role]) }}"
                                            onsubmit="return confirm('Khôi phục người dùng này?')">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-undo"></i> Khôi phục
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $trashedUsers->appends(['role' => $role, 'search' => $search])->links() }}
                </div>
            @else
                <div class="alert alert-warning text-center">
                    Không có người dùng nào đã xoá.
                </div>
            @endif
        </div>
    </div>
@endsection

@section('css')
    <style>
        .custom-table td,
        .custom-table th {
            vertical-align: middle !important;
        }
    </style>
@endsection
