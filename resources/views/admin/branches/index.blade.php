@extends('layouts.AdminLayout')

@section('title', 'Danh sách Chi nhánh')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header giống trang thợ --}}
    <div class="page-header">
        <h3 class="fw-bold mb-3">Danh sách Chi nhánh</h3>
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
                <a href="{{ url('admin/branches') }}">Quản lý Chi nhánh</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách chi nhánh</div>
            <a href="{{ route('branches.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm chi nhánh</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('branches.index') }}" method="GET" class="mb-3">
                <div class="position-relative">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên chi nhánh..."
                        class="form-control pe-5" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>STT</th>
                            <th>Ảnh</th>
                            <th>Tên chi nhánh</th>
                            <th>Địa chỉ</th>
                            <th>SĐT</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($branches as $index => $branch)
                            <tr>
                                <td>{{ ($branches->currentPage() - 1) * $branches->perPage() + $loop->iteration }}</td>
                                <td>
                                    @if ($branch->image)
                                        <img src="{{ asset('storage/' . $branch->image) }}" alt="Ảnh"
                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->phone }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="branchActions{{ $branch->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="branchActions{{ $branch->id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('branches.show', ['branch' => $branch->id, 'page' => request('page', 1)]) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('branches.edit', ['branch' => $branch->id, 'page' => request('page', 1)]) }}">
                                                    <i class="fas fa-edit me-2"></i> Sửa
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form action="{{ route('branches.destroy', $branch->id) }}" method="POST"
                                                    onsubmit="return confirm('Bạn có chắc chắn muốn xoá chi nhánh này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
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
                                <td colspan="6" class="text-muted">Không tìm thấy chi nhánh phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $branches->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .dropdown-menu a {
            display: flex;
            align-items: center;
        }

        .dropdown-menu i {
            width: 20px;
        }
    </style>
@endsection
