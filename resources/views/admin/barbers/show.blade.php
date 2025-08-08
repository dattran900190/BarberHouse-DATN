@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Thợ Cắt Tóc')

@section('content')
    @php
        $skillLevels = [
            'assistant' => 'Thử việc',
            'junior' => 'Sơ cấp',
            'senior' => 'Chuyên ghiệp',
            'master' => 'Bậc thầy',
            'expert' => 'Chuyên gia',
        ];
        $skillLevelColors = [
            'assistant' => 'secondary',
            'junior' => 'info',
            'senior' => 'primary',
            'master' => 'success',
            'expert' => 'warning',
        ];
        $isSoftDeleted = $barber->trashed();
    @endphp

    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Thợ Cắt Tóc</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/dashboard') }}">Quản lý chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ url('admin/barbers') }}">Thợ cắt tóc</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết</a></li>
        </ul>
    </div>

    <!-- Card: Chi tiết Thợ Cắt Tóc -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title mb-0">Chi tiết Thợ Cắt Tóc</h4>
        </div>
        <div class="card-body">
            <div class="row gy-3">
                <div class="col-md-4 text-center">
                    @if ($barber->avatar)
                        <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar" style="max-height: 200px; width: 200px; object-fit: cover; border-radius: 10px;">
                    @else
                        <i class="fa fa-user-circle fa-5x text-muted"></i>
                        <p class="mt-2">Không có ảnh</p>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <i class="fa fa-id-card me-2 text-muted"></i>
                            <strong>Họ tên:</strong> {{ $barber->name }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-tools me-2 text-primary"></i>
                            <strong>Trình độ:</strong>
                            <span class="badge bg-{{ $skillLevelColors[$barber->skill_level] ?? 'secondary' }}">
                                {{ $skillLevels[$barber->skill_level] ?? 'Không xác định' }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-star me-2 text-success"></i>
                            <strong>Đánh giá trung bình:</strong> {{ $barber->rating_avg }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-file-alt me-2 text-info"></i>
                            <strong>Hồ sơ:</strong> {{ $barber->profile }}
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-store me-2 text-warning"></i>
                            <strong>Chi nhánh:</strong>
                            @if ($barber->branch)
                                <a href="{{ route('branches.show', $barber->branch->id) }}"
                                    class="text-decoration-underline">
                                    {{ $barber->branch->name }}
                                </a>
                            @else
                                <span class="text-muted">Chưa có chi nhánh</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <i class="fa fa-info-circle me-2 text-muted"></i>
                            <strong>Trạng thái:</strong>
                            @if ($barber->status === 'idle')
                                <span class="badge bg-success">Đang hoạt động</span>

                            @elseif ($barber->status === 'retired')
                                <span class="badge bg-secondary">Đã nghỉ việc</span>
                            @else
                                <span class="badge bg-light text-dark">Không rõ trạng thái</span>
                            @endif
                            @if ($isSoftDeleted)
                                <span class="badge bg-danger ms-2">Đã xóa mềm</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        @if (!$isSoftDeleted && $barber->status !== 'retired')
                            <a href="{{ route('barbers.edit', ['barber' => $barber->id, 'page' => request('page', 1)]) }}"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fa fa-edit me-1"></i> Sửa
                            </a>
                            <button class="btn btn-outline-warning btn-sm retire-btn" data-id="{{ $barber->id }}"
                                data-page="{{ request('page', 1) }}">
                                <i class="fas fa-user-slash me-1"></i> Nghỉ việc
                            </button>
                        @endif
                        @if (!$isSoftDeleted && $barber->status === 'retired')
                            <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $barber->id }}"
                                data-page="{{ request('page', 1) }}">
                                <i class="fa fa-trash me-1"></i> Xóa mềm
                            </button>
                        @elseif ($isSoftDeleted)
                            <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $barber->id }}"
                                data-page="{{ request('page', 1) }}">
                                <i class="fa fa-undo me-1"></i> Khôi phục
                            </button>
                        @endif
                        <a href="{{ route('barbers.index', ['page' => request('page', 1)]) }}"
                            class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
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
            withInput = false,
            inputPlaceholder = '',
            inputValidator = null,
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const id = this.getAttribute('data-id');
                    const page = this.getAttribute('data-page');

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

                            fetch(route.replace(':id', id) + (page ? `?page=${page}` : ''), {
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

        // Xử lý nút Xóa mềm
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xóa mềm thợ',
            text: 'Bạn có chắc muốn xóa mềm thợ này?',
            route: '{{ route('barbers.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Xử lý nút Khôi phục
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
