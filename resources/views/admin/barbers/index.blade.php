@extends('adminlte::page')

@section('title', 'Danh sách Thợ cắt tóc')

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
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Thợ cắt tóc</h3>
            <a href="{{ route('barbers.create') }}" class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm thợ</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('barbers.index') }}" method="GET" class="mb-3">
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
                        <th>Avatar</th> <!-- Thêm cột Avatar -->
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
                            <td class="text-center">
                                @if ($barber->avatar)
                                    <img src="{{ asset('storage/' . $barber->avatar) }}" alt="Avatar"
                                        class="img-fluid rounded-circle" style="max-width: 100px; max-height: 70px;">
                                @else
                                    <img src="{{ asset('uploads/avatars/default-avatar.png') }}" alt="Avatar"
                                        class="img-fluid rounded-circle" style="max-width: 100px; max-height: 70px;">
                                @endif
                            </td>

                            <td>{{ $barber['name'] }}</td>
                            <td>{{ $barber['skill_level'] }}</td>
                            <td>{{ $barber['rating_avg'] }}</td>
                            <td>{{ $barber['profile'] }}</td>

                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('barbers.show', $barber->id) }}"
                                        class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('barbers.edit', $barber->id) }}"
                                        class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('barbers.destroy', $barber->id) }}" method="POST"
                                        class="d-inline m-0"
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
                    @if ($barbers->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    {{ $barbers->links() }}
@endsection

@section('css')
    <style>
        /* Thêm hiệu ứng hover cho các dòng bảng */
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        /* Nút thêm thợ */
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

        /* Đảm bảo avatar không bị vỡ hình */
        .img-fluid {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection
