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
                <a href="{{ url('admin/dashboard') }}">Quản lý chung</a>
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
                <a href="#">Chi tiết</a>
            </li>
        </ul>
    </div>
    <div class="card">
        <div class="card-header text-white align-items-center">
            <h3 class="card-title mb-0">Chi tiết {{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
        </div>

        <div class="card-body">
            <div class="row">
                {{-- <div class="col-md-4 text-center">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                            class="rounded-circle mb-3 img-fluid avatar-img">
                    @else
                        <div class="rounded-circle avatar-placeholder mb-3">
                            <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</p>
                </div> --}}
                <h4>Thông tin người dùng</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID:</strong> {{ $user->id }}</p>
                        <p><strong>Họ tên:</strong> {{ $user->name }}</p>
                        <p><strong>Email:</strong> {{ $user->email }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Không có' }}</p>
                        <p><strong>Giới tính:</strong> {{ $user->gender ? ($user->gender == 'male' ? 'Nam' : ($user->gender == 'female' ? 'Nữ' : 'Khác')) : 'Không có' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Địa chỉ:</strong> {{ $user->address ?? 'Không có' }}</p>
                        <p><strong>Vai trò:</strong>
                            {{ $user->role == 'user' ? 'Người dùng' : ($user->role == 'admin' ? 'Quản trị viên' : 'Quản lý chi nhánh') }}
                        </p>
                        <p><strong>Trạng thái:</strong>
                            <span
                                class="badge 
                                {{ $user->status == 'active'
                                    ? 'badge-success'
                                    : ($user->status == 'inactive'
                                        ? 'badge-warning'
                                        : 'badge-danger') }}">
                                {{ $user->status == 'active' ? 'Đang hoạt động' : ($user->status == 'inactive' ? 'Không hoạt động' : 'Bị khóa') }}
                            </span>
                        </p>
                        @if ($role === 'admin')
                            <p><strong>Chi nhánh:</strong> {{ $user->branch->name ?? 'Không có' }} </p>
                        @else
                            <p><strong>Số điểm:</strong> {{ $user->points_balance }}</p>
                        @endif
                        <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                        <p><strong>Ngày cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        <div class="text-left mt-auto" style="position: absolute; bottom: 15px; left: 15px;">
                            <a href="{{ route('users.index', ['page' => request('page', 1), 'role' => request('role')]) }}"
                                class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-arrow-left me-1"></i> Quay lại
                            </a>

                        </div>
                    </div>
                </div>
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
