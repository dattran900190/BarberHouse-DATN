@extends('layouts.ClientLayout')

@section('title-page')
    Lịch sử yêu cầu hoàn tiền
@endsection

@section('content')
    <main style="padding: 10%">
        <div class="container-fluid">
            <div class="card wallet-page mt-4 shadow-sm">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card-header border-bottom-dashed p-4">
                            <div class="d-sm-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <h3 class="mb-0 fw-bold">Lịch sử yêu cầu hoàn tiền</h3>
                                </div>
                                <div class="flex-shrink-0 mt-sm-0 mt-3">
                                    <a href="{{ route('client.wallet') }}"
                                        class="btn btn-outline-primary btn-sm">
                                        <span>Gửi yêu cầu hoàn tiền</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card-body p-4">
                            {{-- Bộ lọc tìm kiếm --}}
                            <div class="row g-3 mb-4">
                                <div class="col-lg-8 col-md-7">
                                    <form method="GET" action="{{ route('client.detailWallet') }}" id="searchForm">
                                        <div class="position-relative">
                                            <input type="text" name="search"
                                                class="form-control"
                                                placeholder="Tìm kiếm theo mã đơn hàng hoặc mã đặt lịch" value="{{ request('search') }}">
                                            <button type="submit"
                                                class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-4 col-md-5">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-secondary dropdown-toggle w-100" type="button"
                                            id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ request('status', 'Lọc theo trạng thái') ?: 'Lọc theo trạng thái' }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end w-100" aria-labelledby="filterDropdown">
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
                            </div>

                            {{-- Danh sách yêu cầu --}}
                            @forelse ($refunds as $refund)
                                <div class="refund-item mb-3 p-4 rounded-3 border">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-lg-7 col-md-12">
                                            @if ($refund->order_id)
                                                <div class="mb-2">
                                                    <span class="fw-bold">Mã đơn hàng:
                                                        {{ $refund->order->order_code ?? 'N/A' }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="text-muted">Tổng tiền:
                                                        {{ number_format($refund->order->total_money ?? 0, 0, ',', '.') }}
                                                        VNĐ</span>
                                                </div>
                                            @elseif ($refund->appointment_id)
                                                <div class="mb-2">
                                                    <span class="fw-bold">Mã đặt lịch:
                                                        {{ $refund->appointment->appointment_code ?? 'N/A' }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="text-muted">Tổng tiền:
                                                        {{ number_format($refund->appointment->total_amount ?? 0, 0, ',', '.') }}
                                                        VNĐ</span>
                                                </div>
                                            @endif

                                            <div class="mb-2">
                                                <span class="text-muted">Lý do: {{ $refund->reason }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted">Số tiền hoàn:
                                                    {{ number_format($refund->refund_amount, 0, ',', '.') }}
                                                    VNĐ</span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="text-muted">Ngày yêu cầu:
                                                    {{ $refund->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                                                <div class="mb-2">
                                                    <span class="text-muted">Ngày hoàn tiền:
                                                        {{ $refund->refunded_at->format('d/m/Y') }}</span>
                                                </div>
                                            @endif
                                            @if ($refund->refund_status === 'rejected' && $refund->reject_reason)
                                                <div class="mb-2">
                                                    <span class="text-muted">Lý do từ chối: {{ $refund->reject_reason }}</span>
                                                </div>
                                            @endif
                                            @if ($refund->refund_status === 'refunded' && $refund->proof_image)
                                                <div class="mb-2">
                                                    <span class="text-muted">Hình ảnh minh chứng:
                                                        <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">Xem hình ảnh</a></span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-lg-2 col-md-6 text-center">
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
                                        <div class="col-lg-3 col-md-6 text-center">
                                            <button class="btn-outline-show" style="padding: 6px 10px;" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#detail-{{ $refund->id }}"
                                                aria-expanded="false" aria-controls="detail-{{ $refund->id }}">
                                                Xem chi tiết
                                            </button>
                                        </div>
                                    </div>
                                    </div>

                                    {{-- Chi tiết --}}
                                    <div class="collapse mt-3" id="detail-{{ $refund->id }}">
                                        <div class="card card-body bg-light">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <h6 class="text-muted text-uppercase fw-semibold fs-15">Thông tin ngân hàng</h6>
                                                    <p class="text-muted mb-1">{{ $refund->bank_name }} - {{ $refund->bank_account_number }}</p>
                                                    <p class="text-muted mb-1">Chủ tài khoản: {{ $refund->bank_account_name }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-muted text-uppercase fw-semibold fs-15">Thông tin yêu cầu</h6>
                                                    <p class="text-muted mb-1">Lý do: {{ $refund->reason }}</p>
                                                    <p class="text-muted mb-1">Số tiền: {{ number_format($refund->refund_amount, 0, ',', '.') }} VNĐ</p>
                                                    <p class="text-muted mb-1">Ngày yêu cầu: {{ $refund->created_at->format('d/m/Y H:i') }}</p>
                                                    @if ($refund->refund_status === 'refunded' && $refund->refunded_at)
                                                        <p class="text-muted mb-1">Ngày hoàn tiền: {{ $refund->refunded_at->format('d/m/Y H:i') }}</p>
                                                    @endif
                                                    @if ($refund->refund_status === 'rejected' && $refund->reject_reason)
                                                        <p class="text-muted mb-1">Lý do từ chối: {{ $refund->reject_reason }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if ($refund->refund_status === 'refunded' && $refund->proof_image)
                                                <div class="mt-3">
                                                    <h6 class="text-muted text-uppercase fw-semibold fs-15">Hình ảnh minh chứng</h6>
                                                    <a href="{{ Storage::url($refund->proof_image) }}" target="_blank">
                                                        <img src="{{ Storage::url($refund->proof_image) }}" alt="Proof Image" class="img-fluid" style="max-width: 200px; max-height: 200px;">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted p-4">
                                    <i class="fa fa-inbox fa-3x mb-3"></i>
                                    <p>Không có yêu cầu hoàn tiền nào.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-4" style="color: #000;">
            {{ $refunds->withQueryString()->links() }}
        </div>
    </main>

    <style>
        #mainNav {
            background-color: #000;
        }

        .status-label {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-processing {
            color: orange;
            background-color: rgba(255, 165, 0, 0.1);
        }

        .status-delivered {
            color: green;
            background-color: rgba(0, 128, 0, 0.1);
        }

        .status-cancelled {
            color: red;
            background-color: rgba(255, 0, 0, 0.1);
        }

        .border-bottom-dashed {
            border-bottom: 2px dashed #dee2e6;
        }

        .border-top-dashed {
            border-top: 2px dashed #dee2e6;
        }

        @media (max-width: 768px) {
            main {
                padding: 80px 10px 10px 10px !important;
            }

            .refund-item .row {
                flex-direction: column;
            }

            .refund-item .col-lg-7,
            .refund-item .col-lg-2,
            .refund-item .col-lg-3 {
                width: 100%;
                margin-bottom: 15px;
            }

            .refund-item .col-lg-2,
            .refund-item .col-lg-3 {
                text-align: center !important;
            }



            .d-sm-flex {
                flex-direction: column;
                gap: 15px;
            }

            .d-sm-flex h3 {
                text-align: center;
            }

            .btn-outline-show {
                margin-top: 10px;
                width: auto !important;
                min-width: fit-content;
            }

            .card-header {
                padding: 20px !important;
            }

            .card-body {
                padding: 20px !important;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 15px !important;
            }

            .refund-item {
                padding: 15px !important;
            }

            .dropdown-menu {
                width: 100% !important;
            }

            .form-control {
                font-size: 14px;
            }

            .btn {
                font-size: 14px;
            }
        }
    </style>
@endsection
