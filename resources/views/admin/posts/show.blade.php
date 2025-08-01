@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Bài viết')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Bài Viết</h3>
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
                <a href="{{ url('admin/posts') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/posts') }}">Bài viết</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/posts/' . $post->id) }}">Chi tiết bài viết</a>
            </li>
        </ul>
    </div>

    <!-- Card: Thông tin bài viết -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết bài viết</h4>
        </div>
        <div class="card-body">

            <div class="col-md-{{ $post->image ? '8' : '12' }}">
                <h4 class="fw-bold mb-3">{{ $post->title }}</h4>

                <p class="text-muted mb-2">
                    <i class="fa fa-link me-2 text-primary"></i><strong>Slug:</strong> {{ $post->slug }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-info-circle me-2 text-info"></i><strong>Mô tả:</strong> {{ $post->short_description }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-star me-2 text-warning"></i><strong>Nổi bật:</strong>
                    {{ $post->is_featured ? 'Nổi bật' : 'Không nổi bật' }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-calendar me-2 text-muted"></i><strong>Ngày xuất bản:</strong>
                    {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') : 'Chưa có' }}
                </p>

                <p class="text-muted mb-2">
                    <i class="fa fa-eye me-2 text-success"></i><strong>Trạng thái:</strong>
                    @if ($post->deleted_at)
                        <span class="badge bg-danger">Đã xoá mềm</span>
                    @else
                        <span class="badge bg-success">Đang hoạt động</span>
                    @endif
                </p>
                <p class="text-muted mb-2">
                    @if ($post->image)
                        <div class="col-md-4 ">
                            <i class="fa fa-image me-2 text-success"></i><strong class="text-muted mb-2">Ảnh nền:</strong>
                            <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh bài viết"
                                class="img-fluid rounded mb-3"
                                style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                        </div>
                    @endif
                </p>
                <div class="mt-3">
                    <p class="text-muted mb-2"><strong><i class="fa fa-file-alt me-2 text-muted"></i> Nội dung:</strong></p>
                    <div>{!! $post->content !!}</div>
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
                @if (!$post->trashed())
                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-edit me-1"></i> Sửa
                </a>
                @endif
                @if (!$post->trashed())
                    <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $post->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                @endif

                @if ($post->trashed())
                    <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $post->id }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>

                    <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $post->id }}">
                        <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                    </button>
                @endif

                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        cancelButtonText: 'Hủy',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
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
            title: 'Xoá mềm bài viết',
            text: 'Bạn có chắc chắn muốn xoá mềm bài viết này?',
            route: '{{ route('posts.softDelete', ':id') }}',
            method: 'PATCH'
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục bài viết',
            text: 'Bạn có muốn khôi phục bài viết này?',
            route: '{{ route('posts.restore', ':id') }}',
            method: 'POST'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn bài viết này? Hành động này không thể hoàn tác.',
            route: '{{ route('posts.forceDelete', ':id') }}',
            method: 'DELETE',
            onSuccess: () => {
                window.location.href = '{{ route('posts.index') }}'; // Redirect về trang danh sách
            }
        });
    </script>
@endsection
