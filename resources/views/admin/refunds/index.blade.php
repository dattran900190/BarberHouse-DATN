@extends('layouts.AdminLayout')

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
            {{-- Form tìm kiếm --}}
            <form method="GET" action="{{ route('refunds.index') }}" class="mb-3">
                <input type="hidden" name="status" value="{{ $activeTab }}">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm yêu cầu hoàn..."
                        value="{{ request()->get('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                    </div>
                </div>
            </form>

            {{-- Nếu không có kết quả tìm kiếm --}}
            @if ($search && trim($search) && $refunds->where('refund_status', $activeTab)->isEmpty())
                <div class="alert alert-warning">
                    Không tìm thấy yêu cầu hoàn tiền nào khớp với "{{ $search }}".
                </div>
            @endif

            {{-- Tabs --}}
            <ul class="nav nav-tabs" id="refundTabs" role="tablist">
                @php
                    $tabs = [
                        'pending' => 'Chờ duyệt',
                        'processing' => 'Đang xử lý',
                        'refunded' => 'Đã hoàn tiền',
                        'rejected' => 'Từ chối',
                    ];
                @endphp
                @foreach ($tabs as $key => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab == $key ? 'active' : '' }}" id="{{ $key }}-tab"
                            href="{{ route('refunds.index', ['status' => $key, 'search' => request('search')]) }}"
                            role="tab">
                            {{ $label }}
                        </a>
                    </li>
                @endforeach
            </ul>

            {{-- Nội dung từng tab --}}
            <div class="tab-content" id="refundTabsContent">
                @foreach ($tabs as $key => $label)
                    <div class="tab-pane fade {{ $activeTab == $key ? 'show active' : '' }}" id="{{ $key }}"
                        role="tabpanel">
                        <table class="table table-bordered table-hover mt-3">
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
                                @php $filtered = $refunds->where('refund_status', $key); @endphp
                                @forelse ($filtered as $index => $refund)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $refund->user->name ?? 'N/A' }}</td>
                                        <td>{{ $refund->order->order_code ?? 'Không có đơn' }}</td>
                                        <td class="text-end">{{ number_format($refund->refund_amount, 0, ',', '.') }} đ
                                        </td>
                                        <td class="text-center">
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
                                        </td>
                                        <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('refunds.show', $refund->id) }}"
                                                class="btn btn-info btn-sm mb-1">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>

                                            @if ($key === 'pending')
                                                <form action="{{ route('refunds.update', $refund->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="refund_status" value="processing">
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Xác nhận chuyển sang trạng thái đang xử lý?')">
                                                        <i class="fas fa-check-circle"></i> Xác nhận
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Không có yêu cầu nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
