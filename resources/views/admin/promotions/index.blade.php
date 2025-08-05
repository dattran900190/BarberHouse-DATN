@extends('layouts.AdminLayout')

@section('title', 'Quản lý Mã Giảm Giá')

@section('content')
    {{-- Flash message --}}
    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $type)
        @if (session($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show" role="alert">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    @php
        $currentRole = Auth::user()->role;
    @endphp
    <div class="page-header">
        <h3 class="fw-bold mb-3 text-uppercase">Mã giảm giá</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ url('admin/promotions') }}">Quản lý đặt lịch</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ url('admin/promotions') }}">Mã giảm giá</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
            <div class="card-title">Danh sách mã giảm giá</div>
            @if ($currentRole == 'admin')
                <a href="{{ route('promotions.create') }}"
                    class="btn btn-sm btn-outline-success d-flex align-items-center ms-auto mb-3">
                    <i class="fas fa-plus"></i>
                    <span class="ms-2">Thêm mã giảm giá</span>
                </a>
            @endif
        </div>

        <div class="card-body">
            {{-- Search --}}
            <form action="{{ route('promotions.index') }}" method="GET"
                class="d-flex flex-wrap gap-2 mb-4 align-items-center">

                <div class="position-relative" style="flex: 1; min-width: 200px">
                    <input type="text" name="search" class="form-control pe-5" placeholder="Tìm theo mã..."
                        value="{{ request('search') }}">
                    <button type="submit"
                        class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent text-dark">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <select name="filter" id="filter" class="form-select pe-5"
                    style="max-width: 200px; padding: 9px; border: 2px solid #EBEDF2;" onchange="this.form.submit()">
                    <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Tất cả mã giảm giá</option>
                    <option value="1" {{ request('filter') == '1' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="0" {{ request('filter') == '0' ? 'selected' : '' }}>Đã xóa</option>
                </select>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Mã</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Số lượng</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $index => $promo)
                            <tr>
                                <td>{{ $promotions->firstItem() + $index }}</td>
                                <td>{{ $promo->code }}</td>
                                <td>{{ $promo->discount_type === 'fixed' ? 'Cố định' : 'Phần trăm' }}</td>
                                <td>
                                    {{ $promo->discount_type === 'percent'
                                        ? rtrim(rtrim(number_format($promo->discount_value, 2), '0'), '.') . '%'
                                        : number_format($promo->discount_value, 0, ',', '.') . ' VNĐ' }}
                                </td>
                                <td>{{ $promo->quantity }}</td>
                                <td>{{ $promo->start_date?->format('d/m/Y') }}</td>
                                <td>{{ $promo->end_date?->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge {{ $promo->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $promo->is_active ? 'Hoạt động' : 'Đã xóa' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="promotionActions{{ $promo->id }}">
                                            @if ($promo->trashed())
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('promotions.show', ['promotion' => $promo->id]) }}">
                                                        <i class="fas fa-eye me-1"></i> Xem
                                                    </a>
                                                </li>
                                               <hr class="dropdown-divider">
                                                <li>
                                                    <button type="button" class="dropdown-item text-success restore-btn"
                                                        data-id="{{ $promo->id }}">
                                                        <i class="fas fa-undo me-1"></i> Khôi phục
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item text-danger force-delete-btn"
                                                        data-id="{{ $promo->id }}">
                                                        <i class="fas fa-trash me-1"></i> Xóa vĩnh viễn
                                                    </button>
                                                </li>
                                            @else
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('promotions.show', ['promotion' => $promo->id]) }}">
                                                        <i class="fas fa-eye me-1"></i> Xem
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('promotions.edit', ['promotion' => $promo->id, 'page' => request('page', 1)]) }}">
                                                        <i class="fas fa-edit me-1"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger soft-delete-btn"
                                                        data-id="{{ $promo->id }}">
                                                        <i class="fas fa-times me-1"></i> Xóa mềm
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-muted">Không có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">
                {{ $promotions->links() }}
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>
@endsection

@section('js')
    <script>
        function handleSwalAction({
            selector,
            title,
            text,
            route,
            method = 'POST',
            onSuccess = () => location.reload()
        }) {
            document.querySelectorAll(selector).forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const promoId = this.getAttribute('data-id');

                    Swal.fire({
                        title,
                        text,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Xác nhận',
                        cancelButtonText: 'Hủy',
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Đang xử lý...',
                                text: 'Vui lòng chờ trong giây lát.',
                                allowOutsideClick: false,
                                customClass: {
                                    popup: 'custom-swal-popup'
                                },
                                didOpen: () => Swal.showLoading()
                            });

                            fetch(route.replace(':id', promoId), {
                                    method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();
                                    Swal.fire({
                                        title: data.success ? 'Thành công!' : 'Lỗi!',
                                        text: data.message,
                                        icon: data.success ? 'success' : 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    }).then(() => {
                                        if (data.success) onSuccess();
                                    });
                                })
                                .catch(error => {
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Lỗi!',
                                        text: 'Đã có lỗi xảy ra: ' + error.message,
                                        icon: 'error',
                                        customClass: {
                                            popup: 'custom-swal-popup'
                                        }
                                    });
                                });
                        }
                    });
                });
            });
        }

        // ✅ Xoá mềm
        handleSwalAction({
            selector: '.soft-delete-btn',
            title: 'Xóa mềm mã giảm giá',
            text: 'Bạn có chắc chắn muốn xóa mềm mã này?',
            route: '{{ route('promotions.softDelete', ':id') }}',
            method: 'DELETE'
        });

        // ✅ Xoá vĩnh viễn
        handleSwalAction({
            selector: '.force-delete-btn',
            title: 'Xoá vĩnh viễn mã giảm giá',
            text: 'Hành động này không thể hoàn tác. Bạn chắc chứ?',
            route: '{{ route('promotions.destroy', ':id') }}',
            method: 'DELETE'
        });

        // ✅ Khôi phục
        handleSwalAction({
            selector: '.restore-btn',
            title: 'Khôi phục mã giảm giá',
            text: 'Bạn có chắc chắn muốn khôi phục mã này?',
            route: '{{ route('promotions.restore', ':id') }}',
            method: 'PUT'
        });
    </script>
@endsection
