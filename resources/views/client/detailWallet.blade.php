@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử yêu cầu hoàn tiền
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card wallet-page mt-4 shadow-sm">
                <div class="card-header border-0">
                    <h3 class="mb-0 fw-bold">Lịch sử yêu cầu hoàn tiền</h3>
                </div>

                <div class="card-body">
                    {{-- Bộ lọc tìm kiếm --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <form action="" method="GET" class="d-flex w-50">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="Tìm kiếm theo mã đơn hàng" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Tìm</button>
                        </form>

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Lọc theo trạng thái
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item" href="?status=pending">Chờ duyệt</a></li>
                                <li><a class="dropdown-item" href="?status=processing">Đang xử lý</a></li>
                                <li><a class="dropdown-item" href="?status=refunded">Đã hoàn tiền</a></li>
                                <li><a class="dropdown-item" href="?status=rejected">Từ chối</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Danh sách yêu cầu hoàn tiền --}}
                    @forelse ($refunds as $refund)
                        <div class="refund-item mb-3 p-3 rounded-3 border">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <span class="fw-bold">Mã đơn hàng: {{ $refund->order->order_code ?? 'N/A' }}</span><br>
                                    <span class="text-muted">Lý do: {{ $refund->reason }}</span><br>
                                    <span class="text-muted">Số tiền hoàn: {{ number_format($refund->refund_amount) }} VNĐ</span><br>
                                    <span class="text-muted">Ngày yêu cầu: {{ $refund->created_at->format('d/m/Y') }}</span>
                                    @if ($refund->refund_status === 'refunded')
                                        <br><span class="text-muted">Ngày hoàn tiền: {{ $refund->updated_at->format('d/m/Y') }}</span>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    @php
                                        $statusLabels = [
                                            'pending' => ['label' => 'Chờ duyệt', 'class' => 'status-processing'],
                                            'processing' => ['label' => 'Đang xử lý', 'class' => 'status-processing'],
                                            'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'status-delivered'],
                                            'rejected' => ['label' => 'Từ chối', 'class' => 'status-canceled'],
                                        ];
                                        $status = $statusLabels[$refund->refund_status] ?? ['label' => ucfirst($refund->refund_status), 'class' => 'text-muted'];
                                    @endphp

                                    <span class="status-label {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                                <div class="col-md-3 text-center">
                                    <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#detail-{{ $refund->id }}" aria-expanded="false"
                                        aria-controls="detail-{{ $refund->id }}">
                                        Xem chi tiết
                                    </button>
                                </div>
                            </div>

                            {{-- Chi tiết ẩn/hiện --}}
                            <div class="collapse mt-3" id="detail-{{ $refund->id }}">
                                <div class="card card-body bg-light">
                                    <p><strong>Ngân hàng:</strong> {{ $refund->bank_name }} - {{ $refund->bank_account_number }} ({{ $refund->bank_account_name }})</p>
                                    <p><strong>Lý do:</strong> {{ $refund->reason }}</p>
                                    <p><strong>Số tiền hoàn:</strong> {{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ</p>
                                    <p><strong>Trạng thái:</strong> {{ $status['label'] }}</p>
                                    <p><strong>Ngày yêu cầu:</strong> {{ $refund->created_at->format('d/m/Y H:i') }}</p>
                                    @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                                        <p><strong>Ngày hoàn tiền:</strong> {{ $refund->refunded_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">
                            Không có yêu cầu hoàn tiền nào.
                        </div>
                    @endforelse

                    {{-- Phân trang --}}
                    <div class="mt-4">
                        {{ $refunds->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }

        .status-processing {
            color: orange;
            font-weight: bold;
        }

        .status-delivered {
            color: green;
            font-weight: bold;
        }

        .status-canceled {
            color: red;
            font-weight: bold;
        }

        .status-label::before {
            content: '●';
            margin-right: 5px;
        }

        .status-processing::before {
            color: orange;
        }

        .status-delivered::before {
            color: green;
        }

        .status-canceled::before {
            color: red;
        }
    </style>
@endsection
