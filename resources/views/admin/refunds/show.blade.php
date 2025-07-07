@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Yêu cầu hoàn tiền')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Chi tiết yêu cầu hoàn tiền</h3>
        </div>

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

        <div class="card-body">
            <p><strong>Người dùng:</strong> {{ $refund->user->name ?? 'N/A' }}</p>
            <p><strong>Mã đơn hàng:</strong> {{ $refund->order->order_code ?? 'Không có đơn' }}</p>
            <p><strong>Số tiền hoàn:</strong> {{ number_format($refund->refund_amount, 0, ',', '.') }} đ</p>
            <p><strong>Lý do:</strong> {{ $refund->reason }}</p>
            <p><strong>Ngân hàng:</strong> {{ $refund->bank_name }} - {{ $refund->bank_account_number }}
                ({{ $refund->bank_account_name }})</p>
            <p><strong>Trạng thái:</strong>
                @php
                    $statusMap = [
                        'pending' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
                        'processing' => ['label' => 'Đang xử lý', 'class' => 'primary'],
                        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'success'],
                        'rejected' => ['label' => 'Từ chối', 'class' => 'danger'],
                    ];
                    $info = $statusMap[$refund->refund_status] ?? [
                        'label' => ucfirst($refund->refund_status),
                        'class' => 'secondary',
                    ];
                @endphp
                <span class="badge bg-{{ $info['class'] }}">{{ $info['label'] }}</span>
            </p>

            @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                <p><strong>Ngày hoàn tiền:</strong> {{ $refund->refunded_at->format('d/m/Y H:i') }}</p>
            @endif

            @if ($refund->order && $refund->order->items->count())
                <h5 class="mt-4"><strong>Danh sách sản phẩm trong đơn hàng</strong></h5>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Tên sản phẩm</th>
                            <th>Dung tích</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refund->order->items as $item)
                            <tr>
                                <td>{{ $item->productVariant->product->name ?? 'Không có tên' }}</td>
                                <td>{{ $item->productVariant->volume->name ?? '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price_at_time, 0, ',', '.') }} đ</td>
                                <td>{{ number_format($item->total_price, 0, ',', '.') }} đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif


            <hr>
            @php
                $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];
                $currentStatus = $refund->refund_status;
                $currentOrder = $statusOrder[$currentStatus];
            @endphp

            @if (!in_array($currentStatus, ['refunded', 'rejected']))
                <form action="{{ route('refunds.update', $refund->id) }}" method="POST"
                    onsubmit="return confirm('Bạn chắc chắn muốn cập nhật trạng thái?');">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="refund_status"><strong>Cập nhật trạng thái hoàn tiền:</strong></label>
                        <select name="refund_status" id="refund_status" class="form-control" required>
                            @foreach ($statusOrder as $value => $order)
                                <option value="{{ $value }}" {{ $currentStatus === $value ? 'selected' : '' }}
                                    {{ $order < $currentOrder ? 'disabled' : '' }}>
                                    {{ $statusMap[$value]['label'] ?? ucfirst($value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-save"></i> Cập nhật trạng thái
                    </button>
                </form>
            @else
                <div class="alert alert-secondary mt-3">
                    Yêu cầu đã {{ $currentStatus === 'refunded' ? 'hoàn tiền' : 'bị từ chối' }}, không thể thay đổi trạng
                    thái.
                </div>
            @endif
        </div>
    </div>
@endsection
