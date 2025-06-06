@extends('adminlte::page')

@section('title', 'Quản lý thanh toán')

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
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách thanh toán</h3>
        </div>

        <div class="card-body">
            <form action="#" method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên thanh toán...">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Stt</th>
                        <th>Mã đặt lịch</th>
                        <th>Phương thức</th>
                        <th>Số tiền</th>
                        <th>Ngày thanh toán</th>
                        <th>Mã giao dịch</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($payments->count())
                        @foreach ($payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $payment->appointment->appointment_code ?? 'Không có' }}</td>
                                <td>
                                    @php
                                        $methodColors = [
                                            'momo' => 'secondary',
                                            'cash' => 'primary',
                                        ];
                                        $statusTexts = [
                                            'momo' => 'Chuyển khoản Momo',
                                            'cash' => 'Tiền mặt',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $methodColors[$payment->method] ?? 'secondary' }}">
                                        {{ $statusTexts[$payment->method] ?? 'Không xác định' }}
                                    </span>
                                    {{-- {{ ucfirst($payment->method) }} --}}
                                </td>
                                <td>{{ number_format($payment->amount, 0, ',', '.') }}đ</td>
                                <td>{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : 'Chưa thanh toán' }}
                                </td>
                                <td>{{ $payment->transaction_code ?? 'Không có' }}</td>
                                <td>
                                    @php
                                        $paymentColors = [
                                            'pending' => 'warning',
                                            'paid' => 'success',
                                            'refunded' => 'info',
                                            'failed' => 'danger',
                                        ];
                                        $statusTexts = [
                                            'pending' => 'Chờ xử lý',
                                            'paid' => 'Thanh toán thành công',
                                            'refunded' => 'Hoàn trả thanh toán',
                                            'failed' => 'Thanh toán thất bại',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $paymentColors[$payment->status] ?? 'secondary' }}">
                                        {{ $statusTexts[$payment->status] ?? 'Không xác định' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('payments.show', ['payment' => $payment->id, 'page' => request('page', 1)]) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="{{ route('payments.edit', ['payment' => $payment->id, 'page' => request('page', 1)]) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
                                    <form action="{{ route('payments.destroy', $payment->id) }}" method="POST"
                                        style="display:inline-block;"
                                        onsubmit="return confirm('Bạn có chắc chắn muốn huỷ thanh toán này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-ban"></i> Huỷ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không tìm thấy thanh toán nào phù hợp.</td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div>
    </div>
    {{ $payments->links() }}
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
