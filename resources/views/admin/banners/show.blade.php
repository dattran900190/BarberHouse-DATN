@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Banner')

@section('content')
    <!-- Header + Breadcrumb -->
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Banner</h3>
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
                <a href="{{ url('admin/banners') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('banners.index') }}">Banner</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/banners/' . $banner->id) }}">Chi tiết banner</a>
            </li>
        </ul>
    </div>

    <!-- Card: Thông tin banner -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết Banner</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                @if ($banner->image_url)
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('storage/' . $banner->image_url) }}" alt="Ảnh banner"
                            class="img-fluid rounded mb-3"
                            style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                    </div>
                @endif

                <div class="col-md-{{ $banner->image_url ? '8' : '12' }}">
                    <h4 class="fw-bold mb-3">{{ $banner->title }}</h4>

                    @if (!empty($banner->link_url))
                        <p class="text-muted mb-2">
                            <i class="fa fa-link me-2 text-primary"></i><strong>Liên kết:</strong>
                            <a href="{{ $banner->link_url }}" target="_blank">{{ $banner->link_url }}</a>
                        </p>
                    @endif

                    <p class="text-muted mb-2">
                        <i class="fa fa-eye me-2 text-success"></i><strong>Trạng thái:</strong>
                        @if ($banner->trashed())
                            <span class="badge bg-danger">Đã xoá mềm</span>
                        @elseif ($banner->is_active)
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Không hoạt động</span>
                        @endif
                    </p>

                    <p class="text-muted mb-2">
                        <i class="fa fa-calendar me-2 text-muted"></i><strong>Ngày tạo:</strong>
                        {{ $banner->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                @if (!$banner->trashed())
                    <a href="{{ route('banners.edit', $banner->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                @endif

                @if (!$banner->trashed())
                    <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $banner->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                @endif

                @if ($banner->trashed())
                    <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $banner->id }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>

                    <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $banner->id }}">
                        <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                    </button>
                @endif

                <a href="{{ route('banners.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
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
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function() {
                    const postId = this.dataset.id;
                    Swal.fire({
                        title,
                        text,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                icon: 'info',
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });
                            
                            fetch(route.replace(':id', postId), {
                                    method,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                })
                                .then(response => response.json())  
                                .then(data => {
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                    if (data.success) onSuccess();
                                });
                        }
                    });
                });
            });
        }

        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm banner',
            text: 'Bạn có chắc chắn muốn xoá mềm banner này?',
            route: '{{ route('banners.softDelete', ':id') }}',
            method: 'PATCH'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn banner này?',
            route: '{{ route('banners.destroy', ':id') }}',
            method: 'DELETE',
            onSuccess: () => {
                window.location.href = '{{ route('banners.index') }}';
            }
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục banner',
            text: 'Bạn có chắc chắn muốn khôi phục banner này?',
            route: '{{ route('banners.restore', ':id') }}',
            method: 'POST'
        });
    </script>
@endsection
