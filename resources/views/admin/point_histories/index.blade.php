@extends('adminlte::page')

@section('title', 'Danh sách người dùng - Lịch sử điểm')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h3 class="card-title mb-0">Danh sách người dùng</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('point_histories.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên người dùng..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover text-center">
                <thead class="thead-light">
                    <tr>
                        <th>STT</th>
                        <th>Tên người dùng</th>
                        <th>Email</th>
                        <th>Điểm hiện có</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr>
                            <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-success">
                                    {{ $user->points_balance }} điểm
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('point_histories.user', ['id' => $user->id, 'page' => request('page', 1)]) }}"
                                    class="btn btn-sm btn-info d-inline-flex align-items-center">
                                    <i class="fas fa-eye mr-1"></i> <span>Xem chi tiết</span>
                                </a>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">Không có người dùng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        /* Hiệu ứng hover cho bảng */
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Nút thêm đơn hàng */
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }

        /* Cải thiện thẩm mỹ cho input tìm kiếm */
        .input-group input {
            border-right: 0;
        }

        .input-group .input-group-append {
            border-left: 0;
        }

        .input-group button {
            border-radius: 0;
        }
    </style>
@endsection
