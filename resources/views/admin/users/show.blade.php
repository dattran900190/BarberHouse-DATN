@extends('adminlte::page')

@section('title', 'Chi tiết Người dùng')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết Người dùng</h3>
        </div>

        <div class="card-body">
            <div class="row">
                <!-- Cột trái: Ảnh đại diện và thông tin cơ bản -->
                <div class="col-md-4 text-center">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle mb-3" width="150" height="150">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                            <span class="text-white">Không có ảnh</span>
                        </div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->role }}</p>
                </div>

                <!-- Cột phải: Thông tin chi tiết -->
                <div class="col-md-8">
                    <p><strong>ID:</strong> {{ $user->id }}</p>
                    <p><strong>Họ tên:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Không có' }}</p>
                    <p><strong>Giới tính:</strong> {{ $user->gender ?? 'Không xác định' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $user->address ?? 'Không có' }}</p>
                    <p><strong>Vai trò:</strong> {{ $user->role }}</p>
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge 
                            {{ $user->status == 'active' ? 'badge-success' : 
                               ($user->status == 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                            {{ $user->status }}
                        </span>
                    </p>
                    <p><strong>Số điểm:</strong> {{ $user->points_balance }}</p>
                    <p><strong>Ngày tạo:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Ngày cập nhật:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Quay lại</a>
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
    </style>
@endsection