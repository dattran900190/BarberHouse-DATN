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
        <h3 class="fw-bold mb-3">Sản phẩm</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/user_redeemed_vouchers') }}">Danh sách voucher</a>
            </li>
           
        </ul>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header bg- text-black">
            <h4 class="mb-0"><i class="fas fa-ticket-alt mr-2"></i>Danh sách Voucher đã đổi</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('user_redeemed_vouchers.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Nhập tên người dùng..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn " style="border: 1px solid #a29b9b;" type="submit">
                            <i class="fas fa-search mr-1"></i>
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Người dùng</th>
                            <th>Mã khuyến mãi</th>
                            <th>Ngày đổi</th>
                            <th>Trạng thái</th>
                            <th>Ngày sử dụng</th>
                            <th>Hành động</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($items) > 0)
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user->name ?? 'Không rõ' }}</td>
                                    <td>{{ $item->promotion->code ?? 'Không rõ' }}</td>
                                    <td>{{ $item->redeemed_at ? \Carbon\Carbon::parse($item->redeemed_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $item->is_used ? 'success' : 'secondary' }}">
                                            {{ $item->is_used ? 'Đã dùng' : 'Chưa dùng' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $item->used_at ? \Carbon\Carbon::parse($item->used_at)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                                id="actionMenu{{ $item->id }}" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end"
                                                            aria-labelledby="actionMenu{{ $item->id }}">
                                                            <li> <a href="{{ route('admin.user_redeemed_vouchers.show', $item->id) }}"
                                                                class="dropdown-item">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a></li>
                                            
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-muted">Không có dữ liệu</td>
                            </tr>
                        @endif
                    </tbody>
                    
                </table>
            </div>

            @if($items->hasPages())
                <div class="mt-3 d-flex justify-content-center">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
