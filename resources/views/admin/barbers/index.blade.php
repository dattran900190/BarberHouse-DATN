@extends('layouts.AdminLayout')

@section('title', 'Danh sách Thợ cắt tóc')

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
        <h3 class="fw-bold mb-3 text-uppercase">Thợ cắt</h3>
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
                <a href="{{ url('admin/barbers') }}">Quản lý chi nhánh</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/barbers') }}">Thợ cắt</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách thợ cắt</div>
            <a href="{{ route('barbers.create') }}"
                class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                <i class="fas fa-plus"></i>
                <span class="ms-2">Thêm thợ</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.index') }}" method="GET" class="mb-3 d-flex gap-2 align-items-center">
                <div class="position-relative" style="flex:1">
                    <input type="text" name="search" placeholder="Tìm kiếm theo tên thợ cắt tóc..."
                        class="form-control pe-5" value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>

                <select name="filter" class="form-select"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Đã xoá</option>
                </select>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>Stt</th>
                            <th>Avatar</th>
                            <th>Họ tên</th>
                            <th>Trình độ</th>
                            <th>Đánh giá</th>
                            <th>Hồ sơ</th>
                            <th>Chi nhánh</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($barbers as $index => $barber)
                            <tr>
                                <td>{{ $index + 1 + ($barbers->currentPage() - 1) * $barbers->perPage() }}</td>
                                <td class="text-center">
                                    @if ($barber->avatar)
                                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar"
                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;">
                                    @else
                                        <img src="{{ asset('uploads/avatars/default-avatar.png') }}" alt="Avatar"
                                            style="width: 70px; height: 70px; object-fit: cover; border-radius: 10px;">
                                    @endif
                                </td>
                                <td>{{ $barber->name }}</td>
                                @php
                                    $skillLevels = [
                                        'assistant' => 'Phụ việc',
                                        'junior' => 'Mới vào nghề',
                                        'senior' => 'Có kinh nghiệm',
                                        'master' => 'Bậc thầy',
                                        'expert' => 'Chuyên gia',
                                    ];
                                @endphp

                                <td>{{ $skillLevels[$barber->skill_level] ?? 'Không xác định' }}</td>

                                <td>{{ $barber->rating_avg }}</td>
                                <td>{{ $barber->profile }}</td>
                                <td>{{ $barber->branch?->name ?? 'Chưa có chi nhánh' }}</td>
                                <td>
                                    @if ($barber->trashed())
                                        <span class="badge bg-danger">Đã xoá</span>
                                    @elseif ($barber->status === 'idle')
                                        <span class="badge bg-success">Đang hoạt động</span>
                                    @elseif ($barber->status === 'busy')
                                        <span class="badge bg-warning">Không nhận lịch</span>
                                    @else
                                        <span class="badge bg-secondary">Đã Nghỉ việc</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                            id="actionMenu{{ $barber->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="actionMenu{{ $barber->id }}">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('barbers.show', ['barber' => $barber->id, 'page' => request('page', 1)]) }}">
                                                    <i class="fas fa-eye me-2"></i> Xem
                                                </a>
                                            </li>

                                            @if ($barber->trashed())
                                                <li>
                                                    <button class="dropdown-item text-success restore-btn"
                                                        data-id="{{ $barber->id }}">
                                                        <i class="fas fa-undo me-2"></i> Khôi phục
                                                    </button>
                                                    {{-- <button class="dropdown-item text-danger force-delete-btn" data-id="{{ $barber->id }}">
                                                        <i class="fas fa-trash-alt me-2"></i> Xoá vĩnh viễn
                                                    </button> --}}
                                                </li>
                                            @elseif ($barber->status !== 'retired')
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('barbers.edit', ['barber' => $barber->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger retire-btn"
                                                        data-id="{{ $barber->id }}"
                                                        data-page="{{ request('page', 1) }}">
                                                        <i class="fas fa-user-slash me-2"></i> Nghỉ việc
                                                    </button>
                                                </li>
                                            @elseif ($barber->status === 'retired')
                                                <li>
                                                    <button class="dropdown-item text-danger soft-delete-btn"
                                                        data-id="{{ $barber->id }}">
                                                        <i class="fas fa-times me-2"></i> Xoá mềm
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">Không tìm thấy thợ nào phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $barbers->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .img-fluid {
            max-width: 100%;
            height: auto;
        }

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
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const body = withInput ? JSON.stringify({
                                input: result.value || ''
                            }) : undefined;

                            fetch(route.replace(':id', id), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Phản hồi không hợp lệ từ máy chủ.');
                                    }
                                    return response.json();
                                })
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

        // Áp dụng cho nút Xoá mềm thợ
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xoá mềm thợ',
            text: 'Bạn có chắc muốn xoá mềm thợ này?',
            route: '{{ route('barbers.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Áp dụng cho nút Khôi phục thợ
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục thợ',
            text: 'Bạn có chắc muốn khôi phục thợ này?',
            route: '{{ route('barbers.restore', ':id') }}',
            method: 'POST'
        });

        // Áp dụng cho nút Nghỉ việc
        handleSwalAction({
            selector: '.retire-btn',
            title: 'Cho thợ nghỉ việc',
            text: 'Bạn có chắc muốn cho thợ này nghỉ việc?',
            route: '{{ route('barbers.destroy', ':id') }}?page={{ request('page', 1) }}',
            method: 'DELETE'
        });
    </script>
@endsection
