@extends('layouts.AdminLayout')

@section('title', 'Quản lý Dịch vụ')

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
    <div class="page-header">
        <h3 class="fw-bold mb-3">Dịch vụ cắt tóc</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/services') }}">Dịch vụ</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách dịch vụ</div>

            <a href="{{ route('services.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm dịch vụ</span>
            </a>
        </div>

        <div class="card-body">
            <form action="#" method="GET" class="mb-3">
                <div class="position-relative">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên dịch vụ..."
                        class="form-control pe-5">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Stt</th>
                            <th>Tên dịch vụ</th>
                            <th>Mô tả</th>
                            <th>Giá</th>
                            <th>Thời gian</th>
                            <th>Combo?</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($services->count())
                            @foreach ($services as $index => $service)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->description ? $service->description : 'Không có mô tả' }}</td>
                                    <td>{{ number_format($service->price, 0, ',', '.') }}đ</td>
                                    <td>{{ $service->duration }} phút</td>
                                    <td>{{ $service->is_combo ? 'Có' : 'Không' }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                id="actionMenu{{ $service->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="actionMenu{{ $service->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('services.show', ['service' => $service->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('services.edit', ['service' => $service->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('services.destroy', $service->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá dịch vụ này không?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Xoá
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không tìm thấy dịch vụ nào phù hợp.</td>
                            </tr>
                        @endif
                    </tbody>

                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $services->links() }}
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
    </style>
@endsection
