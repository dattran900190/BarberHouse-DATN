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
        <h3 class="fw-bold mb-3">Quản lý Check-in</h3>
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
                <a href="{{ url('admin/checkins') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>

            <li class="nav-item">
                <a href="{{ url('admin/checkins') }}">Lịch sử Check-in</a>
            </li>
        </ul>
    </div>

    {{-- BẢNG DỮ LIỆU --}}
    <div class="card mt-4">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách lịch sử Check-in</div>
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
                                        <span class="badge bg-success">Đã Check-in</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Chưa Check-in</span>
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
                                <td colspan="8" class="text-center text-muted">Chưa có dữ liệu check-in nào.</td>
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
