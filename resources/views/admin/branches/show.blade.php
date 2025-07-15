@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Chi nhánh')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Chi nhánh</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Quản lý chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('branches.index') }}">Danh sách chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết</a></li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chi tiết Chi nhánh</div>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Cột ảnh -->
                <div class="col-md-4 text-center mb-3">
                    @if ($branch->image)
                        <img src="{{ asset('storage/' . $branch->image) }}" alt="Ảnh chi nhánh" class="img-fluid rounded"
                            style="max-height: 300px;">
                    @else
                        <p>Không có ảnh</p>
                    @endif
                </div>

                <!-- Cột thông tin -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label">Tên chi nhánh</label>
                        <div class="form-control-plaintext">{{ $branch->name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <div class="form-control-plaintext">{{ $branch->address }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <div class="form-control-plaintext">{{ $branch->phone }}</div>
                    </div>

                    @if ($branch->google_map_url)
                        <div class="mb-3">
                            <label class="form-label">Google Map</label>
                            <div class="form-control-plaintext">
                                <a href="{{ $branch->google_map_url }}" target="_blank">Xem bản đồ</a>
                            </div>
                        </div>
                    @endif

                    @if ($branch->content)
                        <div class="mb-3">
                            <label class="form-label">Giới thiệu</label>
                            <div class="border rounded p-3 bg-light">
                                {!! $branch->content !!}
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Ngày tạo</label>
                        <div class="form-control-plaintext">{{ $branch->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    <a href="{{ route('branches.edit', ['branch' => $branch->id, 'page' => request('page', 1)]) }}"
                        class="btn btn-sm btn-outline-warning me-2">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                    <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Danh sách thợ cắt tóc --}}
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h3 class="card-title mb-0">Danh sách Thợ Cắt Tóc tại Chi nhánh</h3>
            {{-- <a href="{{ route('barbers.create') }}" class="btn btn-sm btn-outline-light d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm thợ</span>
            </a> --}}
        </div>

        <div class="card-body">
            @if ($branch->barbers->isEmpty())
                <div class="text-center text-muted">Hiện chưa có thợ cắt tóc nào thuộc chi nhánh này.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th>STT</th>
                                <th>Avatar</th>
                                <th>Họ tên</th>
                                <th>Trình độ</th>
                                <th>Đánh giá</th>
                                <th>Hồ sơ</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branch->barbers as $index => $barber)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        @if ($barber->avatar)
                                            <img src="{{ asset('storage/' . $barber->avatar) }}"
                                                class="img-fluid rounded-circle" style="max-width: 100px; max-height: 70px;"
                                                alt="Avatar">
                                        @else
                                            <img src="{{ asset('uploads/avatars/default-avatar.png') }}"
                                                class="img-fluid rounded-circle" style="max-width: 100px; max-height: 70px;"
                                                alt="Avatar">
                                        @endif
                                    </td>
                                    <td>{{ $barber->name }}</td>
                                    <td>{{ $barber->skill_level }}</td>
                                    <td>{{ $barber->rating_avg }}</td>
                                    <td>{{ Str::limit($barber->profile, 50) }}</td>
                                    <td>
                                        @if ($barber->status === 'idle')
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @elseif ($barber->status === 'busy')
                                            <span class="badge bg-warning">Không nhận lịch</span>
                                        @else
                                            <span class="badge bg-secondary">Đã nghỉ việc</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('barbers.show', ['barber' => $barber->id]) }}"
                                                class="btn btn-info btn-sm d-inline-flex align-items-center">
                                                <i class="fas fa-eye me-1"></i> Xem
                                            </a>
                                            <a href="{{ route('barbers.edit', ['barber' => $barber->id]) }}"
                                                class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                                <i class="fas fa-edit me-1"></i> Sửa
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>


@endsection
