@extends('adminlte::page')

@section('title', 'Quản lý Chi nhánh')

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

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Chi nhánh</h3>
            <a href="{{ route('admin.branches.create') }}"
                class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm chi nhánh</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.branches.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên chi nhánh..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>STT</th>
                        <th>Tên chi nhánh</th>
                        <th>Địa chỉ</th>
                        <th>SĐT</th>
                        <th>Email</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($branches as $index => $branch)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->address }}</td>
                            <td>{{ $branch->phone }}</td>
                            <td>{{ $branch->email }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.branches.show', $branch->id) }}"
                                        class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('admin.branches.edit', $branch->id) }}"
                                        class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST"
                                        class="d-inline m-0"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá chi nhánh này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                            <i class="fas fa-trash"></i> <span>Xoá</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($branches->isEmpty())
                        <tr>
                            <td colspan="6" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>
            </table>
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
