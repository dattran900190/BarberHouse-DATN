@extends('adminlte::page')

@section('title', 'Quản lý Tin tức')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách bài viết</h3>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm bài viết</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.posts.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tiêu đề..."
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
                        <th>Tiêu đề</th>
                        <th>Ảnh</th>
                        <th>Trạng thái</th>
                        <th>Ngày xuất bản</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $index => $post)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $post->title }}</td>
                            <td class="text-center">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="Ảnh" width="80" class="img-thumbnail">
                                @else
                                    <span class="text-muted">Không có ảnh</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($post->status)
                                    <span class="badge bg-success">Đã xuất bản</span>
                                @else
                                    <span class="badge bg-secondary">Chưa xuất bản</span>
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('d/m/Y') : 'Chưa xuất bản' }}
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('admin.posts.show', $post->id) }}"
                                       class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>
                                    <a href="{{ route('admin.posts.edit', $post->id) }}"
                                       class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('admin.posts.destroy', $post->id) }}"
                                          method="POST" class="d-inline m-0"
                                          onsubmit="return confirm('Xác nhận xoá bài viết?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                            <i class="fas fa-trash"></i> <span>Xoá</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Chưa có bài viết nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
    {{ $posts->links() }}
</div>

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
