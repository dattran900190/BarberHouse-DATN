@extends('layouts.AdminLayout')

@section('title', 'Chi tiết ' . ($role == 'user' ? 'Người dùng' : 'Quản trị viên'))

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết {{ $role == 'user' ? 'Người dùng' : 'Quản trị viên' }}</h3>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar"
                            class="rounded-circle mb-3 img-fluid avatar-img">
                    @else
                        <div
                            class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-3 avatar-placeholder">
                            <span class="text-white">N/A</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->role }}</p>
                </div>

                <div class="col-md-8">
                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>Họ tên:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Không có' }}</p>
                    <p><strong>Giới tính:</strong> {{ $user->gender ?? 'Không xác định' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $user->address ?? 'Không có' }}</p>
                    <p><strong>Vai trò:</strong> {{ $user->role }}</p>
                    <p><strong>Trạng thái:</strong>
                        <span
                            class="badge 
                            {{ $user->status == 'active'
                                ? 'badge-success'
                                : ($user->status == 'inactive'
                                    ? 'badge-warning'
                                    : 'badge-danger') }}">
                            {{ $user->status }}
                        </span>
                    </p>
                    <p><strong>Số điểm:</strong> {{ $user->points_balance }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Ngày cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    <a href="{{ route('users.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                        lại</a>
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

        .avatar-placeholder {
            width: 150px;
            height: 150px;
            font-size: 1rem;
            text-align: center;
            margin: 0 auto;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #6c757d;
        }

        .avatar-placeholder span {
            padding: 0 10px;
            white-space: normal;
            line-height: 1.5;
        }

        .avatar-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .card {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
        }

        @media (max-width: 768px) {

            .avatar-placeholder,
            .avatar-img {
                width: 100px;
                height: 100px;
                font-size: 0.9rem;
            }

            .avatar-placeholder span {
                line-height: 1.5;
            }

            .col-md-4,
            .col-md-8 {
                margin-bottom: 1rem;
            }
        }

        .col-md-4 {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
@endsection
