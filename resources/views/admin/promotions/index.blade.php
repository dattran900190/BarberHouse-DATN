@extends('adminlte::page')

@section('title', 'Quản lý Mã Giảm Giá')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Mã Giảm Giá</h3>
            <a href="{{ route('promotions.create') }}" class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm mã giảm giá</span>
            </a>
        </div>

        <div class="card-body">
            <form action="{{ route('promotions.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo mã giảm giá..."
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
                        <th>Mã</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Số lượng</th>
                        <th>Điểm</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày kết thúc</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($promotions as $index => $promotion)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $promotion->code }}</td>
                            <td>{{ $promotion->discount_type == 'fixed' ? 'Cố định' : 'Phần trăm' }}</td>
                            <td>{{ number_format($promotion->discount_value, 2) }}</td>
                            <td>{{ $promotion->quantity }}</td>
                            <td>{{ $promotion->required_points }}</td>
                            <td>{{ $promotion->start_date->format('d/m/Y') }}</td>
                            <td>{{ $promotion->end_date->format('d/m/Y') }}</td>
                            <td>{{ $promotion->is_active ? 'Hoạt động' : 'Không hoạt động' }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('promotions.edit', ['promotion' => $promotion->id, 'page' => request('page', 1)]) }}"
                                        class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-edit"></i> <span>Sửa</span>
                                    </a>
                                    <form action="{{ route('promotions.destroy', $promotion->id) }}" method="POST"
                                        class="d-inline m-0"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-danger btn-sm d-inline-flex align-items-center">
                                            <i class="fas fa-trash"></i> <span>Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @if ($promotions->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $promotions->links('pagination::bootstrap-5') }}
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
