@extends('adminlte::page')

@section('title', 'Danh sách Yêu cầu hoàn tiền')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Yêu cầu hoàn tiền</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('refunds.index') }}" method="GET" class="mb-3" id="searchForm">
                <div class="row">
                    <div class="col-md-3">
                        <select name="status" onchange="this.form.submit()" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="processing" {{ $status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="refunded" {{ $status === 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Từ chối</option>
                        </select>
                    </div>
                    <div class="input-group col-md-9">
                        <input type="text" name="search" id="searchInput" class="form-control"
                            placeholder="Tìm kiếm theo tên người dùng hoặc mã đơn..."
                            value="{{ request()->get('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                        </div>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light text-center align-middle">
                    <tr>
                        <th>STT</th>
                        <th>Người dùng</th>
                        <th>Mã đơn hàng</th>
                        <th>Số tiền hoàn</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($refunds as $index => $refund)
                        <tr>
                            <td>{{ $index + 1 + ($refunds->currentPage() - 1) * $refunds->perPage() }}</td>
                            <td>{{ $refund->user->name ?? 'N/A' }}</td>
                            <td>{{ $refund->order->order_code ?? 'Không có đơn' }}</td>
                            <td class="text-end">{{ number_format($refund->refund_amount, 0, ',', '.') }} đ</td>
                            <td class="text-center">
                                @php
                                    $statusMap = [
                                        'pending' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
                                        'processing' => ['label' => 'Đang xử lý', 'class' => 'primary'],
                                        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'success'],
                                        'rejected' => ['label' => 'Từ chối', 'class' => 'danger'],
                                    ];
                                    $info = $statusMap[$refund->refund_status] ?? ['label' => ucfirst($refund->refund_status), 'class' => 'secondary'];
                                @endphp
                                <span class="badge bg-{{ $info['class'] }}">
                                    {{ $info['label'] }}
                                </span>
                            </td>
                            <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('refunds.show', $refund->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Không có yêu cầu hoàn tiền nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $refunds->links() }}
    </div>
@endsection

@section('css')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
    </style>
@endsection
