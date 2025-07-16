@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Thợ Cắt Tóc')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Thợ Cắt Tóc</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/dashboard') }}">Quản lý chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/barbers') }}">Thợ cắt tóc</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chi tiết Thợ Cắt Tóc</div>
        </div>

        <div class="card-body">
            <div class="row g-3 d-flex align-items-stretch">
                <!-- Cột ảnh đại diện -->
                <div class="col-md-4 d-flex">
                    <div class="border rounded shadow-sm bg-white p-3 w-100 d-flex flex-column justify-content-between">
                        @if ($barber->avatar)
                            <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar"
                                class="img-fluid border shadow" style="width: 100%; object-fit: cover;">
                            <div class="mt-2 text-muted small text-center">Ảnh đại diện</div>
                        @else
                            <p class="text-muted">Không có ảnh</p>
                        @endif
                    </div>
                </div>

                <!-- Cột thông tin -->
                <div class="col-md-8 d-flex">
                    <div class="bg-white rounded shadow-sm p-3 w-100 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="mb-3 text-primary"><i class="fa fa-id-card me-2"></i>Thông tin Thợ Cắt Tóc</h5>

                            <div class="mb-3">
                                <label class="fw-semibold">Họ tên:</label>
                                <div class="text-muted">{{ $barber->name }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">Trình độ:</label>
                                <div class="text-muted">{{ $barber->skill_level }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">Đánh giá trung bình:</label>
                                <div class="text-muted">{{ $barber->rating_avg }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">Hồ sơ:</label>
                                <div class="text-muted">{{ $barber->profile }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">Chi nhánh:</label>
                                <div>
                                    @if ($barber->branch)
                                        <a href="{{ route('branches.show', $barber->branch->id) }}"
                                            class="text-decoration-underline">
                                            {{ $barber->branch->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Chưa có chi nhánh</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="fw-semibold">Trạng thái:</label>
                                <div>
                                    @if ($barber->status === 'idle')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @elseif ($barber->status === 'busy')
                                        <span class="badge bg-warning text-dark">Không nhận lịch</span>
                                    @elseif ($barber->status === 'retired')
                                        <span class="badge bg-secondary">Đã Nghỉ việc</span>
                                    @else
                                        <span class="badge bg-light text-dark">Không rõ trạng thái</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Nút hành động -->
                        <div class="mt-3">
                            @if ($barber->status !== 'retired')
                                <a href="{{ route('barbers.edit', ['barber' => $barber->id, 'page' => request('page', 1)]) }}"
                                    class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fa fa-edit me-1"></i> Sửa
                                </a>
                            @endif

                            <a href="{{ route('barbers.index', ['page' => request('page', 1)]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
