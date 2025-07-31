@extends('layouts.AdminLayout')

@section('title', 'Quản lý Ảnh Khách Hàng')

@section('content')
    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @php
        $currentRole = Auth::user()->role;
    @endphp
    {{-- Breadcrumb --}}
    <div class="page-header">
        <h3 class="fw-bold mb-3">Ảnh Khách Hàng</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
             <li class="nav-item">
                <a href="{{ url('admin/customer_images') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">Quản lý ảnh khách hàng</li>
        </ul>
    </div>

    {{-- Card danh sách --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title fw-bold">Danh sách ảnh</div>
            @if ($currentRole == 'admin')
                <a href="{{ route('customer-images.create') }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-plus"></i> Thêm ảnh mới
                </a>
            @endif
        </div>

        <div class="card-body">

            {{-- Bảng --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Ảnh</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($customerImages as $index => $img)
                            <tr>
                                <td>{{ $loop->iteration + ($customerImages->currentPage() - 1) * $customerImages->perPage() }}
                                </td>
                                <td>
                                    @if ($img->image)
                                        <img src="{{ asset('storage/' . $img->image) }}" alt="Ảnh khách" width="100"
                                            class="img-thumbnail">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($img->status === 1)
                                        <span class="badge bg-success">Hiển thị</span>
                                    @else
                                        <span class="badge bg-danger">Ẩn</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="dropdownMenu{{ $img->id }}" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu{{ $img->id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('customer-images.show', $img->id) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('customer-images.edit', $img->id) }}">
                                                    <i class="fas fa-edit me-2"></i> Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('customer-images.destroy', $img->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá ảnh này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger" type="submit">
                                                        <i class="fas fa-trash me-2"></i> Xoá
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted">Chưa có ảnh khách hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="d-flex justify-content-center mt-3">
                {{ $customerImages->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection
