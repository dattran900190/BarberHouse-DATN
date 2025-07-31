@extends('layouts.AdminLayout')

@section('title', 'Chi tiết Yêu cầu hoàn tiền')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Yêu cầu hoàn tiền</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Đơn hàng</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('refunds.index') }}">Hoàn tiền</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chi tiết</a></li>
        </ul>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Chi tiết Yêu cầu hoàn tiền</div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">Người dùng</label>
                        <div class="form-control-plaintext">{{ $refund->user->name ?? 'N/A' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mã đơn hàng</label>
                        <div class="form-control-plaintext">
                            {{ $refund->order->order_code ?? ($refund->appointment->appointment_code ?? 'Không có') }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số tiền hoàn</label>
                        <div class="form-control-plaintext">{{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lý do yêu cầu</label>
                        <div class="form-control-plaintext">{{ $refund->reason }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngân hàng</label>
                        <div class="form-control-plaintext">
                            {{ $refund->bank_name }} - {{ $refund->bank_account_number }}
                            ({{ $refund->bank_account_name }})
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
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
                        <div class="form-control-plaintext">
                            <span class="badge bg-{{ $info['class'] }}">{{ $info['label'] }}</span>
                        </div>
                    </div>

                    @if ($refund->refund_status === 'rejected' && $refund->reject_reason)
                        <div class="mb-3">
                            <label class="form-label">Lý do từ chối</label>
                            <div class="form-control-plaintext">{{ $refund->reject_reason }}</div>
                        </div>
                    @endif

                    @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                        <div class="mb-3">
                            <label class="form-label">Ngày hoàn tiền</label>
                            <div class="form-control-plaintext">{{ $refund->refunded_at->format('d/m/Y H:i') }}</div>
                        </div>
                    @endif

                    @if ($refund->order && $refund->order->items->count())
                        <div class="mb-3">
                            <label class="form-label">Danh sách sản phẩm</label>
                            <table class="table table-bordered mt-2">
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
                                            <td>{{ number_format($item->price_at_time, 0, ',', '.') }} VNĐ</td>
                                            <td>{{ number_format($item->total_price, 0, ',', '.') }} VNĐ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @php
                        $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];
                        $currentStatus = $refund->refund_status;
                        $currentOrder = $statusOrder[$currentStatus];
                    @endphp

                    @if (!$refund->trashed() && !in_array($currentStatus, ['refunded', 'rejected']))
                        <form action="{{ route('refunds.update', $refund->id) }}" method="POST"
                            onsubmit="return confirm('Bạn chắc chắn muốn cập nhật trạng thái?');">
                            @csrf
                            @method('PUT')

                            <div class="form-group mt-3">
                                <label for="refund_status" class="form-label">Cập nhật trạng thái hoàn tiền</label>
                                <select name="refund_status" id="refund_status" class="form-control" required
                                    onchange="toggleRejectReason(this)">
                                    @foreach ($statusOrder as $value => $order)
                                        @php
                                            $isCurrent = $currentStatus === $value;
                                            $isAllowed =
                                                ($currentStatus === 'pending' && $value === 'processing') ||
                                                ($currentStatus === 'processing' &&
                                                    in_array($value, ['refunded', 'rejected'])) ||
                                                $isCurrent;
                                        @endphp
                                        <option value="{{ $value }}" {{ $isCurrent ? 'selected' : '' }}
                                            {{ !$isAllowed ? 'disabled' : '' }}>
                                            {{ $statusMap[$value]['label'] ?? ucfirst($value) }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="form-group mt-3" id="reject_reason_container" style="display: none;">
                                <label for="reject_reason" class="form-label">Lý do từ chối</label>
                                <textarea name="reject_reason" id="reject_reason" class="form-control" placeholder="Nhập lý do từ chối" rows="4"></textarea>
                                @error('reject_reason')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mt-3 d-flex align-items-center">
                                <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                    <i class="fas fa-save me-1"></i> Cập nhật
                                </button>
                                <a href="{{ route('refunds.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                                </a>
                            </div>
                        </form>

                        <script>
                            function toggleRejectReason(select) {
                                const rejectReasonContainer = document.getElementById('reject_reason_container');
                                rejectReasonContainer.style.display = select.value === 'rejected' ? 'block' : 'none';
                            }
                        </script>
                    @elseif ($refund->trashed())
                        <div class="alert alert-warning mt-4">
                            Yêu cầu này đã bị xóa mềm. Không thể cập nhật trạng thái.
                        </div>
                    @else
                        <div class="alert alert-secondary mt-4">
                            Yêu cầu đã {{ $currentStatus === 'refunded' ? 'hoàn tiền' : 'bị từ chối' }}.
                        </div>
                        <a href="{{ route('refunds.index') }}" class="btn btn-outline-secondary mt-2">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
