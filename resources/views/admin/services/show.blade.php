@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Dịch vụ</h3>
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
                <a href="#">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/services') }}">Dịch vụ</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/services/' . $service->id) }}">Chi tiết dịch vụ</a>
            </li>
        </ul>
    </div>

   <!-- Card 1: Thông tin dịch vụ -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết dịch vụ</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                @if ($service->image)
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('storage/' . $service->image) }}" alt="Ảnh dịch vụ"
                            class="img-fluid rounded mb-3" style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                    </div>
                @endif
                <div class="col-md-{{ $service->image ? '8' : '12' }}">
                    <h4 class="fw-bold mb-3">{{ $service->name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="fa fa-info-circle me-2 text-primary"></i>
                        {{ $service->description ?? 'Không có mô tả' }}
                    </p>

                    <div class="d-flex flex-wrap gap-3 mb-3">
                        <div>
                            <i class="fa fa-dollar-sign me-2 text-success"></i>
                            <span class="fw-semibold">Giá:</span>
                            <span class="text-success fw-bold">{{ number_format($service->price, 0, ',', '.') }} VNĐ</span>
                        </div>
                        <div>
                            <i class="fa fa-clock me-2 text-warning"></i>
                            <span class="fw-semibold">Thời lượng:</span>
                            <span>{{ $service->duration }} phút</span>
                        </div>
                        <div>
                            <i class="fa fa-tag me-2 text-info"></i>
                            <span class="fw-semibold">Loại:</span>
                            <span class="badge bg-{{ $service->is_combo ? 'warning text-dark' : 'secondary' }}">
                                {{ $service->is_combo ? 'Combo' : 'Thông thường' }}
                            </span>
                        </div>
                    </div>

                    <div class="text-muted mb-3">
                        <i class="fa fa-calendar-alt me-2 text-muted"></i>
                        <span class="fw-semibold">Ngày tạo:</span>
                        {{ $service->created_at ? $service->created_at->format('d/m/Y H:i') : 'Không xác định' }}
                    </div>
                    <div class="text-muted">
                        <i class="fa fa-calendar-check me-2 text-muted"></i>
                        <span class="fw-semibold">Ngày cập nhật:</span>
                        {{ $service->updated_at ? $service->updated_at->format('d/m/Y H:i') : 'Không xác định' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Lịch hẹn liên quan (nếu có) -->
    {{-- @if($relatedAppointments && $relatedAppointments->count())
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Lịch hẹn sử dụng dịch vụ này</h4>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã lịch hẹn</th>
                            <th>Khách hàng</th>
                            <th>Thợ</th>
                            <th>Thời gian</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($relatedAppointments as $appointment)
                            <tr>
                                <td>{{ $appointment->appointment_code }}</td>
                                <td>{{ $appointment->user->name ?? 'N/A' }}</td>
                                <td>{{ $appointment->barber->name ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusColors[$appointment->status] ?? 'secondary' }}">
                                        {{ $statusTexts[$appointment->status] ?? ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('appointments.show', $appointment->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye me-1"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif --}}

    <!-- Card 3: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                <a href="{{ route('services.edit', $service->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fa fa-trash me-1"></i> Xóa
                </button>
                <a href="{{ route('services.index', ['page' => request('page', 1)]) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa dịch vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc muốn xóa dịch vụ <strong>{{ $service->name }}</strong>? Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <form action="{{ route('services.destroy', $service->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
