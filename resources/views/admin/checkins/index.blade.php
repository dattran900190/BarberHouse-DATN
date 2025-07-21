@extends('layouts.AdminLayout')

@section('title', 'Quản lý Checkin')

@section('content')
    {{-- THÔNG BÁO --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="page-header">
        <h3 class="fw-bold mb-3">Quản lý Checkin</h3>
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
                <a href="{{ url('admin/checkins') }}">Checkin</a>
            </li>
        </ul>
    </div>

    {{-- <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Xác nhận mã Checkin</div>
        </div>

        <div class="card-body">
            <form action="{{ route('checkins.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label fw-bold">Nhập mã Checkin (6 chữ số)</label>
                    <input type="text" name="code" id="code"
                        class="form-control @error('code') is-invalid @enderror"
                        maxlength="6" required placeholder="Ví dụ: 123456">
                    @error('code')
                        <span class="invalid-feedback d-block mt-1"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div> --}}

    {{-- BẢNG DỮ LIỆU --}}
    <div class="card mt-4">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách Checkins</div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>STT</th>
                            <th>Mã Checkin</th>
                            <th>Trạng thái</th>
                            <th>Thời gian Checkin</th>
                            <th>Khách hàng</th>
                            <th>Thời gian hẹn</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($checkins as $index => $checkin)
                            <tr>
                                <td class="text-center">{{ $checkins->firstItem() + $index }}</td>
                                <td class="text-center"><strong>{{ $checkin->qr_code_value }}</strong></td>
                                <td class="text-center">
                                    @if ($checkin->is_checked_in)
                                        <span class="badge bg-success">Đã Checkin</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chưa Checkin</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $checkin->checkin_time ? \Carbon\Carbon::parse($checkin->checkin_time)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="text-center">
                                    @if (!empty($checkin->appointment->name))
                                        {{ $checkin->appointment->name }}
                                    @else
                                        {{ $checkin->appointment->user->name ?? '-' }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ optional($checkin->appointment)->appointment_time ? \Carbon\Carbon::parse($checkin->appointment->appointment_time)->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="text-center">{{ $checkin->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button" id="actionMenu{{ $checkin->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu{{ $checkin->id }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('checkins.show', $checkin->id) }}">
                                                    <i class="fas fa-eye me-1"></i> Xem
                                                </a>
                                            </li>

                                        </ul>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">Chưa có dữ liệu checkin nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $checkins->links() }}
            </div>
        </div>
    </div>
@endsection
