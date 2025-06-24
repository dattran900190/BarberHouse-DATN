@extends('adminlte::page')

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
            <hr>

            @if (!in_array($refund->refund_status, ['refunded', 'rejected']))
                <form action="{{ route('refunds.update', $refund->id) }}" method="POST"
                    onsubmit="return confirm('Bạn chắc chắn muốn cập nhật trạng thái?');">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="refund_status"><strong>Cập nhật trạng thái hoàn tiền:</strong></label>
                        <select name="refund_status" id="refund_status" class="form-control" required>
                            <option value="pending" {{ $refund->refund_status === 'pending' ? 'selected' : '' }}>Chờ duyệt
                            </option>
                            <option value="processing" {{ $refund->refund_status === 'processing' ? 'selected' : '' }}>Đang
                                xử lý</option>
                            <option value="refunded" {{ $refund->refund_status === 'refunded' ? 'selected' : '' }}>Đã hoàn
                                tiền</option>
                            <option value="rejected" {{ $refund->refund_status === 'rejected' ? 'selected' : '' }}>Từ chối
                            </option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-save"></i> Cập nhật trạng thái
                    </button>
                </form>
            @else
                <div class="alert alert-secondary mt-3">
                    Yêu cầu đã {{ $refund->refund_status === 'refunded' ? 'hoàn tiền' : 'bị từ chối' }}, không thể thay đổi
                    trạng thái.
                </div>
            @endif

        </div>
    </div>
@endsection
