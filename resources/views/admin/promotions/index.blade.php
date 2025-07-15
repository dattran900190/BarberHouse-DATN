@extends('layouts.AdminLayout')

@section('title', 'Quản lý Mã Giảm Giá')

@section('content')
    {{-- Flash message --}}
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    {{-- Page‑header + breadcrumbs --}}
    <div class="page-header">
        <h3 class="fw-bold mb-3">Mã giảm giá</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/dashboard') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/promotions') }}">Mã giảm giá</a>
            </li>
        </ul>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách mã giảm giá</div>

            <a href="{{ route('promotions.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm mã giảm giá</span>
            </a>
        </div>

        <div class="card-body">
            {{-- Search --}}
            <form action="{{ route('promotions.index') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo mã..."
                        value="{{ request('search') }}">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Số lượng</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $index => $promo)
                            <tr>
                                <td>{{ $promotions->firstItem() + $index }}</td>
                                <td>{{ $promo->code }}</td>
                                <td>{{ $promo->discount_type === 'fixed' ? 'Cố định' : 'Phần trăm' }}</td>
                                <td>
                                    {{ $promo->discount_type === 'percent'
                                        ? rtrim(rtrim(number_format($promo->discount_value, 2), '0'), '.') . '%'
                                        : number_format($promo->discount_value, 0, ',', '.') . ' ₫' }}
                                </td>
                                <td>{{ $promo->quantity }}</td>
                                <td>{{ $promo->start_date?->format('d/m/Y') }}</td>
                                <td>{{ $promo->end_date?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $promo->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $promo->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('promotions.show', ['promotion' => $promo->id]) }}">
                                                    <i class="fas fa-eye me-1"></i>Xem
                                                </a>    
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('promotions.edit', ['promotion' => $promo->id, 'page' => request('page', 1)]) }}">
                                                    <i class="fas fa-edit me-1"></i>Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <form action="{{ route('promotions.destroy', $promo->id) }}" method="POST"
                                                    onsubmit="return confirm('Xóa mã này?')" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item text-danger" type="submit">
                                                        <i class="fas fa-trash me-1"></i>Xóa
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-muted">Không có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

             <div class="d-flex justify-content-center mt-3">
                {{ $promotions->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
@endsection
