@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử yêu cầu hoàn tiền
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container">
            <div class="card wallet-page mt-4 shadow-sm">
                <div class="card-header border-0 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fw-bold">Lịch sử yêu cầu hoàn tiền</h3>
                    <a href="{{ route('client.wallet') }}"
                        class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2 px-3 py-2 rounded-pill shadow-sm">
                        <span>Gửi yêu cầu hoàn tiền</span>
                    </a>
                </div>
                <div class="card-body">
                    {{-- Bộ lọc tìm kiếm --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <form action="{{ route('client.detailWallet') }}" method="GET" class="position-relative"
                            style="width: 400px;">
                            <input type="text" name="search"
                                class="form-control rounded-pill pe-5 shadow-sm border-secondary"
                                placeholder="Tìm kiếm theo mã đơn hàng hoặc mã đặt lịch" value="{{ request('search') }}">
                            <button type="submit"
                                class="btn position-absolute top-50 end-0 translate-middle-y me-3 p-0 border-0 bg-transparent text-muted">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>

                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('status', 'Lọc theo trạng thái') ?: 'Lọc theo trạng thái' }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item"
                                        href="{{ route('client.detailWallet', array_merge(request()->except('status'), ['status' => ''])) }}">Tất
                                        cả</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('client.detailWallet', array_merge(request()->except('status'), ['status' => 'pending'])) }}">Chờ
                                        duyệt</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('client.detailWallet', array_merge(request()->except('status'), ['status' => 'processing'])) }}">Đang
                                        xử lý</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('client.detailWallet', array_merge(request()->except('status'), ['status' => 'refunded'])) }}">Đã
                                        hoàn tiền</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ route('client.detailWallet', array_merge(request()->except('status'), ['status' => 'rejected'])) }}">Từ
                                        chối</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Danh sách yêu cầu --}}
                    @forelse ($refunds as $refund)
                        <div class="refund-item mb-3 p-3 rounded-3 border">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    @if ($refund->order_id)
                                        <span class="fw-bold">Mã đơn hàng:
                                            {{ $refund->order->order_code ?? 'N/A' }}</span><br>
                                        <span class="text-muted">Tổng tiền:
                                            {{ number_format($refund->order->total_money ?? 0, 0, ',', '.') }}
                                            VNĐ</span><br>
                                    @elseif ($refund->appointment_id)
                                        <span class="fw-bold">Mã đặt lịch:
                                            {{ $refund->appointment->appointment_code ?? 'N/A' }}</span><br>
                                        <span class="text-muted">Tổng tiền:
                                            {{ number_format($refund->appointment->total_amount ?? 0, 0, ',', '.') }}
                                            VNĐ</span><br>
                                    @endif

                                    <span class="text-muted">Lý do: {{ $refund->reason }}</span><br>
                                    <span class="text-muted">Số tiền hoàn:
                                        {{ number_format($refund->refund_amount, 0, ',', '.') }}
                                        VNĐ</span><br>
                                    <span class="text-muted">Ngày yêu cầu:
                                        {{ $refund->created_at->format('d/m/Y') }}</span>
                                    @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                                        <br><span class="text-muted">Ngày hoàn tiền:
                                            {{ $refund->refunded_at->format('d/m/Y') }}</span>
                                    @endif
                                    @if ($refund->refund_status === 'rejected' && $refund->reject_reason)
                                        <br><span class="text-muted">Lý do từ chối: {{ $refund->reject_reason }}</span>
                                    @endif
                                    @if ($refund->refund_status === 'refunded' && $refund->proof_image)
                                        <br><span class="text-muted">Hình ảnh minh chứng:
                                            <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">Xem hình ảnh</a></span>
                                    @endif
                                </div>
                                <div class="col-md-2 text-center">
                                    @php
                                        $statusLabels = [
                                            'pending' => ['label' => 'Chờ duyệt', 'class' => 'status-processing'],
                                            'processing' => ['label' => 'Đang xử lý', 'class' => 'status-processing'],
                                            'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'status-delivered'],
                                            'rejected' => ['label' => 'Từ chối', 'class' => 'status-cancelled'],
                                        ];
                                        $status = $statusLabels[$refund->refund_status] ?? [
                                            'label' => ucfirst($refund->refund_status),
                                            'class' => 'text-muted',
                                        ];
                                    @endphp
                                    <span class="status-label {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                                <div class="col-md-3 text-center">
                                    <button class="btn-outline-buy" style="padding: 5px 10px;" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#detail-{{ $refund->id }}"
                                        aria-expanded="false" aria-controls="detail-{{ $refund->id }}">
                                        Xem chi tiết
                                    </button>
                                </div>
                            </div>

                            {{-- Chi tiết --}}
                            <div class="collapse mt-3" id="detail-{{ $refund->id }}">
                                <div class="card card-body bg-light">
                                    <p><strong>Thông tin ngân hàng:</strong>
                                        {{ $refund->bank_name }} - {{ $refund->bank_account_number }}
                                        ({{ $refund->bank_account_name }})
                                    </p>
                                    <p><strong>Lý do yêu cầu:</strong> {{ $refund->reason }}</p>
                                    @if ($refund->refund_status === 'rejected' && $refund->reject_reason)
                                        <p><strong>Lý do từ chối:</strong> {{ $refund->reject_reason }}</p>
                                    @endif
                                    @if ($refund->refund_status === 'refunded' && $refund->proof_image)
                                        <p><strong>Hình ảnh minh chứng:</strong>
                                            <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">
                                                <img src="{{ Storage::url($refund->proof_image) }}" alt="Proof Image" style="max-width: 200px; max-height: 200px;">
                                            </a>
                                        </p>
                                    @endif
                                    <p><strong>Số tiền hoàn:</strong>
                                        {{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ
                                    </p>
                                    <p><strong>Trạng thái:</strong> {{ $status['label'] }}</p>
                                    <p><strong>Ngày yêu cầu:</strong> {{ $refund->created_at->format('d/m/Y H:i') }}</p>
                                    @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                                        <p><strong>Ngày hoàn tiền:</strong> {{ $refund->refunded_at->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info">Không có yêu cầu hoàn tiền nào.</div>
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

        .status-cancelled {
            color: red;
            font-weight: bold;
        }

        .status-label::before {
            content: none;
            margin-right: 5px;
        }
    </style>
@endsection