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
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($reviews->count())
                        @foreach ($reviews as $index => $review)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $review->user->name }}</td>
                                <td>{{ $review->barber->name }}</td>
                                <td>{{ $review->rating }}</td>
                                <td>{{ $review->comment }}</td>
                                <td>
                                    @if ($review->is_visible == 1)
                                        <span class="badge bg-success">Hiện</span>
                                    @else
                                        <span class="badge bg-danger">Ẩn</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reviews.show', ['review' => $review->id, 'page' => request('page', 1)]) }}"
                                        class="btn btn-info btn-sm action-btn">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('reviews.edit', ['review' => $review->id, 'page' => request('page', 1)]) }}"
                                        class="btn btn-warning btn-sm action-btn">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST"
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
