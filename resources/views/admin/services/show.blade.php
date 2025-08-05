@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Dịch vụ</h3>
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
                <a href="{{ url('admin/services') }}">Quản lý đặt lịch</a>
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
                {{-- @if ($service->image)
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('storage/' . $service->image) }}" alt="Ảnh dịch vụ"
                            class="img-fluid rounded mb-3"
                            style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                    </div>
                @endif --}}
                <div class="col-md-{{ $service->image ? '8' : '12' }}">
                    <h4 class="fw-bold mb-3">{{ $service->name }}
                        @if ($service->deleted_at)
                            <span class="badge bg-danger">Đã xoá mềm</span>
                        @endif
                    </h4>
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

    <!-- Card 3: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                {{-- nếu dịch vụ xoá mềm thì không hiện sửa xoá chỉ hiện quay lại --}}
                @if ($service->deleted_at)

                @if (!$service->trashed())
                    <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $service->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                @endif

                @if ($service->trashed())
                    <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $service->id }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>

                        <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $service->id }}">
                        <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                    </button>
                @endif
                    <a href="{{ route('services.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                @else
                    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $service->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                    <a href="{{ route('services.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                @endif
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script>
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false, // Thêm tùy chọn hiển thị input
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload() // Thay reload bằng callback linh hoạt
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const appointmentId = this.getAttribute('data-id');

                    const swalOptions = {
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    };

                    if (withInput) {
                        swalOptions.input = 'textarea';
                        swalOptions.inputPlaceholder = inputPlaceholder;
                        if (inputValidator) {
                            swalOptions.inputValidator = inputValidator;
                        }
                    }

                    Swal.fire(swalOptions).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup' // CSS
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ? JSON.stringify({
                                no_show_reason: result.value || 'Không có lý do'
                            }) : undefined;

                            fetch(route.replace(':id', appointmentId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) onSuccess();
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }

        // Xoá mềm
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm dịch vụ',
            text: 'Bạn có chắc chắn muốn xoá mềm dịch vụ này?',
            route: '{{ route('services.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Khôi phục
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục dịch vụ',
            text: 'Bạn có chắc chắn muốn khôi phục dịch vụ này?',
            route: '{{ route('services.restore', ':id') }}',
            method: 'POST'
        });     

        // Xoá vĩnh viễn
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn dịch vụ này?',
            route: '{{ route('services.destroy', ':id') }}',
            method: 'DELETE',
            onSuccess: () => {
                window.location.href = '{{ route('services.index') }}';
            }
        });
    </script>
@endsection
