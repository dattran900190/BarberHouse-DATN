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

            <div class="mt-4">
                <a href="{{ route('users.index', ['page' => request('page', 1), 'role' => request('role')]) }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
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
