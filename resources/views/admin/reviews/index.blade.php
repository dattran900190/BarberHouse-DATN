@extends('layouts.AdminLayout')

@section('title', 'Quản lý bình luận')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
                <a href="#">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/reviews') }}">Bình luận</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách bình luận</div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('reviews.index') }}"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">
                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" placeholder="Tìm kiếm theo người bình luận..."
                        value="{{ request('search') }}" class="form-control pe-5">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>

                <select name="filter" id="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả dịch vụ</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Còn hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Stt</th>
                            <th>Người bình luận</th>
                            <th>Thợ</th>
                            <th>Đánh giá</th>
                            <th>Bình luận</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($reviews->count())
                            @foreach ($reviews as $review)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $review->user->name ?? '[N/A]' }}</td>
                                    <td>{{ $review->barber->name ?? '[N/A]' }}</td>
                                    <td>{{ $review->rating }}</td>
                                    <td>{{ $review->comment }}</td>
                                    <td>
                                        @if ($review->trashed())
                                            <span class="badge bg-danger">Đã xoá</span>
                                        @elseif($review->is_visible)
                                            <span class="badge bg-success">Hiện</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Ẩn</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button"
                                                data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a href="{{ route('reviews.show', ['review' => $review->id]) }}"
                                                        class="dropdown-item">
                                                        <i class="fas fa-eye me-2"></i> Xem
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('reviews.edit', ['review' => $review->id]) }}"
                                                        class="dropdown-item">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                @if ($review->trashed())
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-success restore-btn"
                                                            data-id="{{ $review->id }}">
                                                            <i class="fas fa-undo me-2"></i> Khôi phục
                                                        </button>
                                                        <button type="button"
                                                            class="dropdown-item text-danger force-delete-btn"
                                                            data-id="{{ $review->id }}">
                                                            <i class="fas fa-trash-alt me-2"></i> Xoá vĩnh viễn
                                                        </button>
                                                    </li>
                                                @else
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-danger soft-delete-btn"
                                                            data-id="{{ $review->id }}">
                                                            <i class="fas fa-times me-2"></i> Xoá mềm
                                                        </button>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center text-muted">Không tìm thấy bình luận nào phù hợp.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection

@section('js')
    <script>
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            withInput = false,
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const id = this.getAttribute('data-id');

                    const swalOptions = {
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Huỷ',
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
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ?
                                JSON.stringify({
                                    reason: result.value || 'Không có lý do'
                                }) :
                                undefined;

                            fetch(route.replace(':id', id), {
                                    method,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body
                                })
                                .then(res => res.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        icon: data.success ? 'success' : 'error',
                                        title: data.success ? 'Thành công!' :
                                            'Thất bại!',
                                        text: data.message,
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) onSuccess();
                                    });
                                })
                                .catch(err => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + err.message,
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

        // Xoá mềm bình luận
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm bình luận',
            text: 'Bạn có chắc muốn xoá mềm bình luận này?',
            route: '{{ route('reviews.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Khôi phục bình luận
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục bình luận',
            text: 'Bạn có chắc muốn khôi phục bình luận này?',
            route: '{{ route('reviews.restore', ':id') }}',
            method: 'POST'
        });

        // Xoá vĩnh viễn bình luận
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn bình luận',
            text: 'Bạn có chắc muốn xoá vĩnh viễn bình luận này? Hành động này không thể hoàn tác.',
            route: '{{ route('reviews.destroy', ':id') }}',
            method: 'DELETE'
        });
    </script>
@endsection
