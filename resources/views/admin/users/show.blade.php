@extends('layouts.AdminLayout')

@section('title', 'Chi tiết ' . ($role == 'user' ? 'Người dùng' : 'Quản trị viên'))

@php
    $currentRole = Auth::user()->role;
@endphp
@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">{{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
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
                <a href="{{ url('admin/users') }}">Quản lý chung</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('users.index') }}">Quản lý người dùng</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/users/' . $user->id) }}">Chi tiết người dùng</a>
            </li>
        </ul>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-header text-white align-items-center">
            <h3 class="card-title mb-0">Chi tiết {{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
        </div>

        <div class="card-body">
            <div class="row gy-3">
                {{-- Dòng 1 --}}
                <div class="col-md-6">
                    <i class="fa fa-id-badge me-2 text-muted"></i>
                    <strong>ID:</strong> {{ $user->id }}
                     @if ($user->trashed())
                        <span class="badge bg-danger">Đã xoá mềm</span>
                    @endif
                </div>
                <div class="col-md-6">
                    <i class="fa fa-user me-2 text-primary"></i>
                    <strong>Họ tên:</strong> {{ $user->name }}
                </div>

                {{-- Dòng 2 --}}
                <div class="col-md-6">
                    <i class="fa fa-envelope me-2 text-info"></i>
                    <strong>Email:</strong> {{ $user->email }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-phone me-2 text-success"></i>
                    <strong>Số điện thoại:</strong> {{ $user->phone ?? 'Không có' }}
                </div>

                {{-- Dòng 3 --}}
                <div class="col-md-6">
                    <i class="fa fa-venus-mars me-2 text-warning"></i>
                    <strong>Giới tính:</strong>
                    {{ $user->gender ? ($user->gender == 'male' ? 'Nam' : ($user->gender == 'female' ? 'Nữ' : 'Khác')) : 'Không có' }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-map-marker-alt me-2 text-danger"></i>
                    <strong>Địa chỉ:</strong> {{ $user->address ?? 'Không có' }}
                </div>

                {{-- Dòng 4 --}}
                <div class="col-md-6">
                    <i class="fa fa-user-tag me-2 text-muted"></i>
                    <strong>Vai trò:</strong>
                    {{ $user->role == 'user' ? 'Người dùng' : ($user->role == 'admin' ? 'Quản trị viên' : 'Quản lý chi nhánh') }}
                </div>
                <div class="col-md-6">
                    <i class="fa fa-toggle-on me-2 text-secondary"></i>
                    <strong>Trạng thái:</strong>
                    <span
                        class="badge 
                    {{ $user->status == 'active' ? 'bg-success' : ($user->status == 'inactive' ? 'bg-warning' : 'bg-danger') }}">
                        {{ $user->status == 'active' ? 'Đang hoạt động' : ($user->status == 'inactive' ? 'Không hoạt động' : 'Đã xóa') }}
                    </span>
                </div>

                {{-- Dòng 5 --}}
                @if ($role === 'admin')
                    <div class="col-md-6">
                        <i class="fa fa-store-alt me-2 text-success"></i>
                        <strong>Chi nhánh:</strong> {{ $user->branch->name ?? 'Không có' }}
                    </div>
                @else
                    <div class="col-md-6">
                        <i class="fa fa-star me-2 text-warning"></i>
                        <strong>Số điểm:</strong> {{ $user->points_balance }}
                    </div>
                @endif
                <div class="col-md-6">
                    <i class="fa fa-calendar-alt me-2 text-muted"></i>
                    <strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                </div>

                {{-- Dòng 6 --}}
                <div class="col-md-6">
                    <i class="fa fa-clock me-2 text-muted"></i>
                    <strong>Ngày cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}
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
                {{-- nếu dịch vụ xoá mềm thì không hiện sửa xoá chỉ hiện quay lại --}}
                @if ($user->deleted_at)
                    <button class="btn btn-outline-success btn-sm restore-btn" data-id="{{ $user->id }}">
                        <i class="fa fa-undo me-1"></i> Khôi phục
                    </button>
                    <button class="btn btn-outline-danger btn-sm force-delete-btn" data-id="{{ $user->id }}">
                        <i class="fa fa-times-circle me-1"></i> Xoá vĩnh viễn
                    </button>
                    <a href="{{ route('users.index', ['page' => request('page', 1)]) }}"
                         class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                @else
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-edit me-1"></i> Sửa
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm soft-delete-btn"
                        data-id="{{ $user->id }}">
                         <i class="fas fa-trash me-2"></i> Xoá
                    </button>
                    <a href="{{ route('users.index', ['page' => request('page', 1)]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .badge-success {
            background-color: #28a745;
        }

        .badge-warning {
            background-color: #ffc107;
        }

        .badge-danger {
            background-color: #dc3545;
        }

        .avatar-img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #e0e0e0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .avatar-img:hover {
            transform: scale(1.05);
        }

        .avatar-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            font-size: 1.8rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #6c63ff;
            margin: 0 auto;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid #e0e0e0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .avatar-placeholder span {
            color: white;
            font-weight: 600;
            letter-spacing: 1px;
        }

        /* Responsive: thu nhỏ avatar trên mobile */
        @media (max-width: 768px) {

            .avatar-img,
            .avatar-placeholder {
                width: 80px;
                height: 80px;
                font-size: 1.5rem;
            }
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
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const userId = this.getAttribute('data-id');

                    Swal.fire({
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Vui lòng chờ...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading(),
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });

                            fetch(route.replace(':id', userId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(async response => {
                                    const contentType = response.headers.get(
                                        'content-type');

                                    let data = {};
                                    if (contentType && contentType.includes(
                                            'application/json')) {
                                        data = await response.json();
                                    } else {
                                        const text = await response.text();
                                        data.message = text ||
                                            'Lỗi không xác định từ server.';
                                    }

                                    Swal.close();

                                    if (response.ok) {
                                        Swal.fire({
                                            icon: data.success ? 'success' :
                                                'error',
                                            title: data.success ? 'Thành công' :
                                                'Thất bại',
                                            text: data.message ||
                                                'Thao tác đã hoàn tất.',
                                            customClass: {
                                                popup: 'custom-swal-popup'
                                            }
                                        }).then(() => {
                                            if (data.success) onSuccess();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: `Lỗi ${response.status}`,
                                            text: data.message ||
                                                'Đã xảy ra lỗi không xác định.',
                                            customClass: {
                                                popup: 'custom-swal-popup'
                                            }
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: error.message ||
                                            'Đã xảy ra lỗi không xác định.',
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

        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xóa mềm người dùng',
            text: 'Bạn có chắc chắn muốn xóa người dùng này?',
            route: '{{ route('users.softDelete', ':id') }}',
            method: 'DELETE'
        });

        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục người dùng',
            text: 'Khôi phục người dùng này?',
            route: '{{ route('users.restore', ':id') }}',
            method: 'POST'
        });

        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xóa vĩnh viễn',
            text: 'Hành động này sẽ xóa vĩnh viễn người dùng. Không thể khôi phục.',
            route: '{{ route('users.destroy', ':id') }}',
            method: 'DELETE',
             onSuccess: () => {
                window.location.href = '{{ route('users.index') }}';
            }
        });
    </script>
@endsection