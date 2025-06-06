@extends('adminlte::page')

@section('title', 'Chỉnh sửa Thanh toán')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa Thanh toán</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('payments.update', $payment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                @php
                    // Các label
                    $methods = [
                        'momo' => 'Chuyển khoản Momo',
                        'cash' => 'Tiền mặt',
                    ];
                    $paymentStatusOptions = [
                        'pending' => 'Chờ xử lý',
                        'paid' => 'Thanh toán thành công',
                        'refunded' => 'Hoàn trả',
                        'failed' => 'Thanh toán thất bại',
                    ];

                    $currentStatus = $payment->status;

                    // Khi ở 'refunded' => disable toàn bộ select trạng thái và phương thức
                    // Khi ở 'paid'    => disable chuyển về 'pending'
                    // Khi ở 'paid'    => vẫn cho phép chuyển -> 'refunded' hoặc 'failed'
                    // Khi ở 'failed'  => disable chuyển về 'pending'
                    // Khi ở 'refunded' => disable mọi option
                    // Khi ở 'pending'  => cho chuyển -> 'paid' hoặc 'failed'

                @endphp

                {{-- Phương thức thanh toán --}}
                <div class="mb-3">
                    <label for="method" class="form-label">Phương thức thanh toán</label>

                    {{--  
        CƠ CHẾ KHÓA PHƯƠNG THỨC:
        - Nếu payment đã 'paid' hoặc 'refunded', không đổi method nữa.
        - Nếu payment đang 'failed', ta vẫn có thể cho đổi method (để retry),
          ví dụ chuyển đổi momo <-> cash nếu khách muốn đổi cách thanh toán.
        - Nếu payment đang 'pending', dĩ nhiên phải cho đổi.
    --}}

                    @php
                        $methodLocked = in_array($currentStatus, ['paid', 'refunded']);
                    @endphp

                    <select class="form-control" id="method" name="method" {{ $methodLocked ? 'disabled' : '' }}>
                        @foreach ($methods as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('method', $payment->method) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if ($methodLocked)
                        <input type="hidden" name="method" value="{{ $payment->method }}">
                        <small class="text-muted">
                            Thanh toán đã <strong>{{ $currentStatus === 'paid' ? 'thành công' : 'hoàn trả' }}</strong>,
                            không thể thay đổi phương thức.
                        </small>
                    @endif

                    @error('method')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                {{-- Trạng thái thanh toán --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái thanh toán</label>

                    <select class="form-control" id="status" name="status" {{-- Nếu đã 'refunded' thì disable toàn bộ --}}
                        {{ $currentStatus === 'refunded' ? 'disabled' : '' }}>
                        @foreach ($paymentStatusOptions as $statusValue => $label)
                            <option value="{{ $statusValue }}"
                                {{ old('status', $currentStatus) === $statusValue ? 'selected' : '' }}
                                @if (
                                    // 1. Nếu đang 'refunded' thì disable mọi option khác ngoài chính nó
                                    ($currentStatus === 'refunded' && $statusValue !== 'refunded') ||
                                        // 2. Nếu đang 'paid'   thì disable 'pending' (không cho quay về)
                                        //    Ví dụ: paid->pending chặn
                                        ($currentStatus === 'paid' && $statusValue === 'pending') ||
                                        // 3. Nếu đang 'failed' thì disable 'pending'
                                        //    (không cho quay từ failed về pending)
                                        ($currentStatus === 'failed' && $statusValue === 'pending') ||
                                        // 4. Nếu đang 'pending', đôi khi bạn không muốn cho từ 'pending'->'refunded'
                                        //    (chưa paid sao đã refund?), nên chặn 'pending'->'refunded'
                                        ($currentStatus === 'pending' && $statusValue === 'refunded')) disabled @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if ($currentStatus === 'refunded')
                        <small class="text-muted">
                            Thanh toán đã được hoàn trả, không thể thay đổi trạng thái.
                        </small>
                    @endif

                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('payments.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a>
            </form>
        </div>
    </div>
@endsection
