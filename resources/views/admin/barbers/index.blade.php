@extends('adminlte::page')

@section('title', 'Quản lý Thợ cắt tóc')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Thợ cắt tóc</h3>
            <a href="#" class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm thợ</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.barbers.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên thợ cắt tóc..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>Stt</th>
                        <th>Họ tên</th>
                        <th>Trình độ</th>
                        <th>Đánh giá</th>
                        <th>Hồ sơ</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barbers as $index => $barber)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $barber['name'] }}</td>
                            <td>{{ $barber['skill_level'] }}</td>
                            <td>{{ $barber['rating_avg'] }}</td>
                            <td>{{ $barber['profile'] }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="#" class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="#" class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="#" method="POST" class="d-inline m-0"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá thợ này không?');">
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
