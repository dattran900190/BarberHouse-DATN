@extends('layouts.AdminLayout')

@section('title', 'Danh sách dung tích')

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
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách dung tích sản phẩm</h3>
            <a href="{{ route('admin.volumes.create') }}"
               class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2">Thêm sản phẩm</span>
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($volumes as $volume)
                            <tr>
                                <td>{{ $volume->id }}</td>
                                <td>{{ $volume->name }}</td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                                            id="actionMenu{{ $volume->id }}" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end"
                                                        aria-labelledby="actionMenu{{ $volume->id }}">
                                                       
                                                        <li>   <a href="{{ route('admin.volumes.edit', $volume) }}?page={{ request()->get('page') }}"
                                                            class="dropdown-item">
                                                            <i class="fas fa-edit me-2"></i> Sửa
                                                        </a></li>
                                      <li>
                                        <form action="{{ route('admin.volumes.destroy', $volume) }}?page={{ request()->get('page') }}" 
                                            method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                          @csrf
                                          @method('DELETE')
                                          <button type="submit"    class="dropdown-item text-danger">
                                            <i class="fas fa-trash-alt"></i> <span>  Xóa </span>
                                          </button>
                                      </form>
                                    </li>
                                    </div>
                                </td>
                               
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Không có dữ liệu dung tích.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex mt-3">
                {{ $volumes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
