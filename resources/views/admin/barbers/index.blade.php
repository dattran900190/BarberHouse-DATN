@extends('layouts.AdminLayout')

@section('title', 'Danh sách Thợ cắt tóc')

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
        <h3 class="fw-bold mb-3">Danh sách Thợ cắt tóc</h3>
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
                <a href="{{ url('admin/dashboard') }}">Quản lý chi nhánh</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/barbers') }}">Quản lý thợ</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách thợ</div>
            <a href="{{ route('barbers.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm thợ</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.index') }}" method="GET" class="mb-3">
                <div class="position-relative">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên thợ cắt tóc..."
                        class="form-control pe-5" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Stt</th>
                            <th>Avatar</th>
                            <th>Họ tên</th>
                            <th>Trình độ</th>
                            <th>Đánh giá</th>
                            <th>Hồ sơ</th>
                            <th>Chi nhánh</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barbers as $index => $barber)
                            <tr>
                                <td>{{ $index + 1 + ($barbers->currentPage() - 1) * $barbers->perPage() }}</td>
                                <td class="text-center">
                                    @if ($barber->avatar)
                                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar"
                                            class="img-thumbnail" style="max-width: 100px; max-height: 70px;">
                                    @else
                                        <img src="{{ asset('uploads/avatars/default-avatar.png') }}" alt="Avatar"
                                            class="img-thumbnail" style="max-width: 100px; max-height: 70px;">
                                    @endif
                                </td>

                                <td>{{ $barber->name }}</td>
                                <td>{{ $barber->skill_level }}</td>
                                <td>{{ $barber->rating_avg }}</td>
                                <td>{{ $barber->profile }}</td>
                                <td>{{ $barber->branch?->name ?? 'Chưa có chi nhánh' }}</td>
                                <td>
                                    @if ($barber->status === 'idle')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @elseif ($barber->status === 'busy')
                                        <span class="badge bg-warning">Không nhận lịch</span>
                                    @else
                                        <span class="badge bg-secondary">Đã Nghỉ việc</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="actionMenu{{ $barber->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="actionMenu{{ $barber->id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('barbers.show', ['barber' => $barber->id, 'page' => request('page', 1)]) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>

                                            @if ($barber->status !== 'retired')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('barbers.edit', ['barber' => $barber->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('barbers.destroy', $barber->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn cho thợ này nghỉ việc không?');">
                                                        @csrf @method('DELETE')
                                                        <input type="hidden" name="page"
                                                            value="{{ request('page', 1) }}">
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-user-slash me-2"></i> Nghỉ việc
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Không tìm thấy thợ nào phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $barbers->links() }}
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

        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection
