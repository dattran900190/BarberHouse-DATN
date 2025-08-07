@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Chi nhánh')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Chi nhánh</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Quản lý chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('branches.index') }}">Danh sách chi nhánh</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết</a></li>
        </ul>
    </div>
    {{-- @if ($branch->trashed())
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-circle me-1"></i> Chi nhánh này đã bị xoá mềm. Bạn chỉ có thể xem thông tin.
        </div>
    @endif --}}

    <!-- Card: Thông tin chi nhánh -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Chi tiết chi nhánh</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-{{ $branch->image ? '8' : '12' }}">
                    <h4 class="fw-bold mb-3">{{ $branch->name }}</h4>
                    <p class="text-muted mb-2">
                        <i class="fa fa-map-marker-alt me-2 text-primary"></i>
                        <strong>Địa chỉ:</strong> {{ $branch->address }}
                    </p>
                    <p class="text-muted mb-2">
                        <i class="fa fa-phone me-2 text-info"></i>
                        <strong>Số điện thoại:</strong> {{ $branch->phone }}
                    </p>
                    @if ($branch->google_map_url)
                        <div class="mb-3">
                            <p class="text-muted mb-2">
                                <i class="fa fa-map me-2 text-success"></i>
                                <strong>Google Map:</strong>
                            </p>
                            <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                                <iframe src="{{ $branch->google_map_url }}" style="border:0;" allowfullscreen loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                    @endif

                    <p class="text-muted mb-2">
                        <i class="fa fa-calendar me-2 text-muted"></i>
                        <strong>Ngày tạo:</strong> {{ $branch->created_at->format('d/m/Y H:i') }}
                    </p>
                    @if ($branch->image)
                        <div class="col-md-4">
                            <i class="fa fa-image me-2 text-success"></i>
                            <strong class="text-muted mb-2">Ảnh chi nhánh:</strong>
                            <img src="{{ asset('storage/' . $branch->image) }}" alt="Ảnh chi nhánh"
                                class="img-fluid rounded mb-3"
                                style="max-height: 250px; object-fit: cover; border: 1px solid #dee2e6;">
                        </div>
                    @endif
                    @if ($branch->content)
                        <div class="">
                            <p class="text-muted mb-2"><strong><i class="fa fa-info-circle"></i> Giới thiệu:</strong></p>
                            <div>{!! $branch->content !!}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header px-4 py-3">
            <h4 class="card-title">Danh sách Thợ Cắt Tóc tại Chi nhánh</h4>
        </div>
        <div class="card-body" style="border-radius: 0 0 12px 12px;">
            @if ($branch->barbers->isEmpty())
                <div class="text-center text-muted py-4" style="font-size: 17px;">
                    Hiện chưa có thợ cắt tóc nào thuộc chi nhánh này.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>STT</th>
                                <th>Avatar</th>
                                <th>Họ tên</th>
                                <th>Trình độ</th>
                                <th>Đánh giá</th>
                                <th>Hồ sơ</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($branch->barbers as $index => $barber)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        @if ($barber->avatar)
                                            <img src="{{ asset('storage/' . $barber->avatar) }}"
                                                style="max-height: 100px; width: 100px; object-fit: cover; border-radius: 10px;"
                                                alt="Avatar">
                                        @else
                                            <img src="{{ asset('uploads/avatars/default-avatar.png') }}"
                                                style="max-height: 100px; width: 100px; object-fit: cover; border-radius: 10px;"
                                                alt="Avatar">
                                        @endif
                                    </td>
                                    <td>{{ $barber->name }}</td>
                                    <td>{{ $barber->skill_level }}</td>
                                    <td>{{ $barber->rating_avg }}</td>
                                    <td>{{ Str::limit($barber->profile, 50) }}</td>
                                    <td>
                                        @if ($barber->is_on_leave_today)
                                            <span class="badge bg-info">Đang nghỉ phép</span>
                                        @elseif ($barber->status === 'idle')
                                            <span class="badge bg-success">Đang hoạt động</span>
                                        @elseif ($barber->status === 'busy')
                                            <span class="badge bg-warning">Không nhận lịch</span>
                                        @else
                                            <span class="badge bg-secondary">Đã nghỉ việc</span>
                                        @endif

                                    </td>

                                    <td class="text-center align-middle" style="width: 70px;">
                                        <div class="dropdown d-inline-block">
                                            <button
                                                class="btn btn-light btn-sm d-flex align-items-center justify-content-center"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                                style="border: 1px solid #ddd; width: 38px; height: 38px; padding: 0;">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center"
                                                        href="{{ route('barbers.show', ['barber' => $barber->id]) }}">
                                                        <i class="fa fa-eye me-2"></i> Xem
                                                    </a>
                                                </li>


                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Card: Hành động -->
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex align-items-center">
            <h4 class="card-title">Hành động</h4>
        </div>
        <div class="card-body">
            <div class="d-flex gap-2">
                {{-- nếu dịch vụ xoá mềm thì không hiện sửa xoá chỉ hiện quay lại --}}
                @if ($branch->deleted_at)

                    @if (!$branch->trashed())
                        <button class="btn btn-outline-danger btn-sm soft-delete-btn" data-id="{{ $branch->id }}">
                            <i class="fas fa-trash me-2"></i> Xoá
                        </button>
                    @endif

                    @if ($branch->trashed())
                        <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $branch->id }}">
                            <i class="fa fa-undo me-1"></i> Khôi phục
                        </button>

                        <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $branch->id }}">
                            <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                        </button>
                    @endif
                    <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                @else
                    <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm soft-delete-btn"
                        data-id="{{ $branch->id }}">
                        <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                    <a href="{{ route('branches.index', ['page' => request('page', 1)]) }}"
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
            title: 'Xoá mềm chi nhánh',
            text: 'Bạn có chắc chắn muốn xoá mềm chi nhánh này?',
            route: '{{ route('branches.softDelete', ':id') }}',
            method: 'PATCH'
        });

        // Xoá cứng
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn',
            text: 'Bạn có chắc chắn muốn xoá vĩnh viễn chi nhánh này? Hành động này không thể hoàn tác.',
            route: '{{ route('branches.forceDelete', ':id') }}',
            method: 'DELETE'
        });

        // Khôi phục
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục chi nhánh',
            text: 'Bạn có chắc chắn muốn khôi phục chi nhánh này?',
            route: '{{ route('branches.restore', ':id') }}',
            method: 'POST'
        });
    </script>
@endsection
