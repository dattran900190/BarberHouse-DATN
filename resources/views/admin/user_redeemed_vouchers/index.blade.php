@extends('adminlte::page')

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

    
<div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">🔍 Tìm kiếm người đổi voucher</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('user_redeemed_vouchers.index') }}" method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control rounded-left"
                    placeholder="Nhập tên người dùng..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button class="btn btn-primary rounded-right" type="submit">
                        <i class="fas fa-search mr-1"></i> Tìm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

        </div>
    </div>

    {{-- DANH SÁCH VOUCHER --}}
    <div class="card shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">🎟️ Danh sách voucher đã đổi</h4>
            <a href="{{ route('user_redeemed_vouchers.create') }}" class="btn btn-warning text-white font-weight-bold"
>
                ➕ Gán voucher cho user
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="thead-dark">
                        <tr class="text-center">
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
                        @forelse($items as $item)
                            <tr class="text-center align-middle">
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->user->name ?? 'Không rõ' }}</td>
                                <td>{{ $item->promotion->code ?? 'Không rõ' }}</td>
                                <td>{{ $item->redeemed_at ? \Carbon\Carbon::parse($item->redeemed_at)->format('d/m/Y H:i') : '' }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->is_used ? 'success' : 'secondary' }}">
                                        {{ $item->is_used ? 'Đã dùng' : 'Chưa dùng' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $item->used_at ? \Carbon\Carbon::parse($item->used_at)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('user_redeemed_vouchers.edit', $item) }}" class="btn btn-sm btn-primary">Sửa</a>
                                    <form action="{{ route('user_redeemed_vouchers.destroy', $item) }}" method="POST"
                                          class="d-inline-block"
                                          onsubmit="return confirm('Bạn có chắc muốn xóa voucher này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer">
                {{ $items->links() }}
            </div>
        @endif
    </div>
@endsection
