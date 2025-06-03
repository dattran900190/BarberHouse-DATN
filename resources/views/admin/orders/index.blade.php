@extends('adminlte::page')

@section('title', 'Danh sách Đơn hàng')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Đơn hàng</h3>
            {{-- <a href="{{ route('orders.create') }}" class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm đơn hàng</span>
            </a> --}}
        </div>

        <div class="card-body">
            <form action="{{ route('orders.index') }}" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm kiếm theo mã đơn hàng hoặc tên người nhận..."
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
                        <th>Mã đơn hàng</th>
                        <th>Tên người nhận</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Phương thức thanh toán</th>
                        <th>Ghi chú</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                            <td>{{ $order->order_code }}</td>
                            <td>{{ $order->name }}</td>
                            <td>{{ $order->phone }}</td>
                            <td>{{ Str::limit($order->address, 30) }}</td>
                            <td class="text-end">{{ number_format($order->total_money, 0, ',', '.') }} đ</td>
                            <td class="text-center text-capitalize">
                                @if ($order->status)
                                    <span
                                        class="badge bg-{{ $order->status == 'pending'
                                            ? 'warning'
                                            : ($order->status == 'processing'
                                                ? 'primary'
                                                : ($order->status == 'shipping'
                                                    ? 'info'
                                                    : ($order->status == 'completed'
                                                        ? 'success'
                                                        : 'danger'))) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>

                            <td class="text-center text-uppercase">{{ $order->payment_method ?? '-' }}</td>
                            <td>{{ Str::limit($order->note ?? '-', 30) }}</td>
                            <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="btn btn-info btn-sm d-inline-flex align-items-center">
                                        <i class="fas fa-eye"></i> <span>Xem</span>
                                    </a>

                                    @if ($order->status === 'pending')
                                        <form action="{{ route('orders.destroy', $order->id) }}" method="POST"
                                            class="d-inline m-0"
                                            onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-warning btn-sm d-inline-flex align-items-center">
                                                <i class="fas fa-ban"></i> <span>Hủy đơn</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $orders->links() }}
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
