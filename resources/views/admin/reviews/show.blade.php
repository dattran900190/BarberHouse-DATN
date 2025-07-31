@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Dịch vụ')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Bình luận</h3>
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
                <a href="{{ url('admin/reviews') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews') }}">Bình luận</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews/' . $review->id) }}">Chi tiết bình luận</a>
            </li>
        </ul>
    </div>

    <!-- Card: Chi tiết bình luận -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Chi tiết bình luận</h4>
        </div>

        <div class="card-body">
            <div class="row gy-3">
                <div class="col-12">
                    <p class="fw-bold mb-3">
                        <i class="fa fa-user me-2 text-primary"></i>Người bình luận:
                        <span class="fw-normal text-dark">{{ $review->user->name }}</span>
                    </p>

                    <p class="mb-2">
                        <i class="fas fa-user-check me-2 text-info"></i>
                        <strong>Thợ:</strong> {{ $review->barber->name }}
                    </p>

                    <p class="mb-2">
                        <i class="fa fa-star me-2 text-warning"></i>
                        <strong>Đánh giá:</strong> {{ $review->rating }}
                    </p>

                    <p class="mb-2">
                        <i class="fa fa-comment-dots me-2 text-secondary"></i>
                        <strong>Bình luận:</strong> {{ $review->comment }}
                        @if ($review->deleted_at)
                            <span class="badge bg-danger">Đã xoá mềm</span>
                        @endif
                    </p>

                    <p class="mb-3">
                        <i class="fa fa-eye me-2 text-success"></i>
                        <strong>Trạng thái:</strong>
                        @if ($review->is_visible == 1)
                            <span class="badge bg-success">Hiện</span>
                        @else
                            <span class="badge bg-danger">Ẩn</span>
                        @endif
                    </p>


                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                @if ($review->deleted_at)
                @if (!$review->trashed())
                    <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $review->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                @endif

                @if ($review->trashed())
                    <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $review->id }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>

                    <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $review->id }}">
                        <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                    </button>
                @endif
                    <a href="{{ route('reviews.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                @else
                    <a href="{{ route('reviews.edit', $review->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm soft-delete-btn"
                        data-id="{{ $review->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                    <a href="{{ route('reviews.index', ['page' => request('page', 1)]) }}"
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
            route: '{{ route('reviews.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Khôi phục
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục dịch vụ',
            text: 'Bạn có chắc chắn muốn khôi phục dịch vụ này?',
            route: '{{ route('reviews.restore', ':id') }}',
            method: 'POST'
        });     

        // Xoá vĩnh viễn
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn dịch vụ này?',
            route: '{{ route('reviews.destroy', ':id') }}',
            method: 'DELETE',
            onSuccess: () => {
                window.location.href = '{{ route('reviews.index') }}';
            }
        });
    </script>
@endsection
