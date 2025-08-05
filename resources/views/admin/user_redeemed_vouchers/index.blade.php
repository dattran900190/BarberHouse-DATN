@extends('layouts.AdminLayout')

@section('title', 'Quản lý Voucher đã đổi')

@section('content')
    {{-- THÔNG BÁO --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Lịch sử đổi Voucher</h3>
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
                <a href="{{ url('admin/user_redeemed_vouchers') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/user_redeemed_vouchers') }}">Lịch sử đổi Voucher</a>
            </li>
        </ul>
        
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg- text-black">
            <h4 class="mb-0"></i>Danh sách người dùng đã đổi Voucher</h4>
        </div>

        <div class="card-body">
                 <form method="GET" action="{{ route('user_redeemed_vouchers.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">

                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên người dùng..."
                        value="{{ request('search') }}">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Tổng voucher đã đổi</th>
                            <th>Voucher đã sử dụng</th>
                            <th>Voucher chưa sử dụng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($users) > 0)
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $user->redeemed_vouchers_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">{{ $user->used_vouchers_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $user->unused_vouchers_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                id="actionMenu{{ $user->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="actionMenu{{ $user->id }}">
                                                <li> 
                                                    <a href="{{ route('admin.user_redeemed_vouchers.show', $user->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-muted">Không có dữ liệu</td>
                            </tr>
                        @endif
                    </tbody>

                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-3 d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
