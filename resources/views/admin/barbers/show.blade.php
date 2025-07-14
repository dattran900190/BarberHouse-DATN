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
            <li class="nav-item"><a href="#">Quản lý nhân sự</a></li>
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
            <div class="row">
                <!-- Cột ảnh -->
                <div class="col-md-4 text-center mb-3">
                    @if ($barber->avatar)
                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar" class="img-fluid rounded"
                            style="max-height: 300px;">
                    @else
                        <p>Không có ảnh</p>
                    @endif
                </div>

                <!-- Cột thông tin -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <div class="form-control-plaintext">{{ $barber->name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trình độ</label>
                        <div class="form-control-plaintext">{{ $barber->skill_level }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Đánh giá trung bình</label>
                        <div class="form-control-plaintext">{{ $barber->rating_avg }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hồ sơ</label>
                        <div class="form-control-plaintext">{{ $barber->profile }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chi nhánh</label>
                        <div class="form-control-plaintext">
                            @if ($barber->branch)
                                <a href="{{ route('branches.show', $barber->branch->id) }}">
                                    {{ $barber->branch->name }}
                                </a>
                            @else
                                Chưa có chi nhánh
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <div class="form-control-plaintext">
                            @if ($barber->status === 'idle')
                                <span class="text-success fw-bold">Đang hoạt động</span>
                            @elseif ($barber->status === 'busy')
                                <span class="text-warning fw-bold">Không nhận lịch</span>
                            @elseif ($barber->status === 'retired')
                                <span class="text-danger fw-bold">Đã Nghỉ việc</span>
                            @else
                                <span>Không rõ trạng thái</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('barbers.edit', ['barber' => $barber->id, 'page' => request('page', 1)]) }}"
                        class="btn btn-sm btn-outline-warning me-2">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                    <a href="{{ route('barbers.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
