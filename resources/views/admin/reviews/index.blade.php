@extends('adminlte::page')

@section('title', 'Quản lý bình luận')

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
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách bình luận</h3>
            <a href="{{ route('reviews.create') }}"
                class="btn btn-success btn-icon-toggle d-flex align-items-center ml-auto">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm bình luận</span>
            </a>
        </div>

        <div class="card-body">
            <form action="#" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên bình luận...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Stt</th>
                        <th>Người bình luận</th>
                        <th>Thợ</th>
                        {{-- <th>Giá</th> --}}
                        <th>Đánh giá</th>
                        <th>Bình luận</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($reviews->count())
                        @foreach ($reviews as $index => $service)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $service->user->name }}</td>
                                <td>{{ $service->barber->name }}</td>
                                <td>{{ $service->rating }}</td>
                                <td>{{ $service->comment }}</td>
                                <td class="text-center">
                                    <a href="{{ route('reviews.show', $service->id) }}"
                                        class="btn btn-info btn-sm action-btn">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('reviews.edit', $service->id) }}"
                                        class="btn btn-warning btn-sm action-btn">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('reviews.destroy', $service->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xoá bình luận này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm action-btn">
                                            <i class="fas fa-trash"></i> <span>Xoá</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không tìm thấy bình luận nào phù hợp.</td>
                        </tr>
                    @endif
                </tbody>

            </table>
        </div>
    </div>
    {{ $reviews->links() }}
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
