@extends('layouts.AdminLayout')

@section('title', 'Danh sách Yêu cầu hoàn tiền')

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Đóng">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    <div class="page-header">
        <h3 class="fw-bold mb-3">Danh sách Yêu cầu hoàn tiền</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ url('admin/dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item">
                <a href="{{ route('refunds.index') }}">Quản lý hoàn tiền</a>
            </li>
        </ul>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="card-title">Yêu cầu hoàn tiền</div>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('refunds.index') }}" class="mb-3">
                <div class="position-relative">
                    <input type="text" name="search" class="form-control pe-5" placeholder="Tìm kiếm yêu cầu hoàn..."
                        value="{{ request()->get('search') }}">
                    <button type="submit" class="btn position-absolute end-0 top-0 bottom-0 px-3 border-0 bg-transparent">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
            </form>

            @php
                $tabs = [
                    'pending' => 'Chờ duyệt',
                    'processing' => 'Đang xử lý',
                    'refunded' => 'Đã hoàn tiền',
                    'rejected' => 'Từ chối',
                ];
            @endphp

            <ul class="nav nav-tabs" id="refundTabs" role="tablist">
                @foreach ($tabs as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == $key ? 'active' : '' }}" id="{{ $key }}-tab"
                            href="{{ route('refunds.index', ['status' => $key, 'search' => request('search')]) }}">
                            {{ $label }}
                            @if ($key == 'pending' && $pendingRefundCount > 0)
                                <span class="position-relative">
                                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                        <span class="visually-hidden">New alerts</span>
                                    </span>
                                </span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content">
                @foreach ($tabs as $key => $label)
                    <div class="tab-pane fade {{ $activeTab == $key ? 'show active' : '' }}" id="{{ $key }}">
                        @php
                            $filteredRefundsForTab = $refunds->where('refund_status', $key);
                        @endphp

                        @if ($search && $filteredRefundsForTab->isEmpty() && $activeTab == $key)
                            <div class="alert alert-warning mt-3">
                                Không tìm thấy yêu cầu nào khớp với "{{ $search }}" trong tab này.
                            </div>
                        @endif

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light text-center">
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
                                <tbody id="refund-table-{{ $key }}">
                                    @forelse ($filteredRefundsForTab as $index => $refund)
                                        <tr data-refund-id="{{ $refund->id }}">
                                            <td>{{ $refunds->firstItem() + $index }}</td>
                                            <td>{{ $refund->user->name ?? 'N/A' }}</td>
                                            <td>{{ $refund->order->order_code ?? ($refund->appointment->appointment_code ?? 'Không có') }}</td>
                                            <td class="text-end">
                                                {{ number_format($refund->refund_amount, 0, ',', '.') }} đ
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $map = [
                                                        'pending' => ['label' => 'Chờ duyệt', 'class' => 'warning'],
                                                        'processing' => ['label' => 'Đang xử lý', 'class' => 'primary'],
                                                        'refunded' => ['label' => 'Đã hoàn tiền', 'class' => 'success'],
                                                        'rejected' => ['label' => 'Từ chối', 'class' => 'danger'],
                                                    ];
                                                    $info = $map[$refund->refund_status] ?? ['label' => $refund->refund_status, 'class' => 'secondary'];
                                                @endphp
                                                <span class="badge bg-{{ $info['class'] }}">{{ $info['label'] }}</span>
                                            </td>
                                            <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('refunds.show', $refund->id) }}">
                                                                <i class="fas fa-eye me-2"></i> Xem
                                                            </a>
                                                        </li>
                                                        @if ($key === 'pending')
                                                            <li>
                                                                <form action="{{ route('refunds.update', $refund->id) }}"
                                                                    method="POST"
                                                                    onsubmit="return confirm('Chuyển trạng thái sang đang xử lý?')">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="refund_status"
                                                                        value="processing">
                                                                    <button class="dropdown-item text-success"
                                                                        type="submit">
                                                                        <i class="fas fa-check-circle me-2"></i> Xác nhận
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="no-data-row">
                                            <td colspan="7" class="text-center text-muted">Không có yêu cầu nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $refunds->appends(['status' => $status, 'search' => $search])->links() }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .dot-indicator {
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            display: inline-block;
            margin-left: 4px;
            vertical-align: middle;
        }

        .rounded-circle {
            width: 10px;
            height: 10px;
            line-height: 10px;
            text-align: center;
            font-size: 0.7em;
        }

        .toast {
            max-width: 350px;
        }

        .new-refund-row {
            animation: highlight 2s ease-in-out;
        }

        @keyframes highlight {
            0% {
                background-color: #fff3cd;
            }
            100% {
                background-color: transparent;
            }
        }
    </style>
@endsection
