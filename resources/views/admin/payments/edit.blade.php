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
                    $methods = ['momo' => 'Chuyển khoản Momo', 'cash' => 'Tiền mặt'];
                    $methodLocked = in_array($payment->status, ['paid', 'refunded', 'failed']);
                @endphp

                {{-- Phương thức thanh toán --}}
                <div class="mb-3">
                    <label for="method" class="form-label">Phương thức thanh toán</label>
                    <select class="form-control" id="method" name="method" {{ $methodLocked ? 'disabled' : '' }}>
                        @foreach ($methods as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('method', $payment->method) === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if ($methodLocked)
                        {{-- Nếu bị disable thì gửi thêm hidden field vì khi disabled thì browser sẽ không gửi dữ liệu method lên server--}}
                        <input type="hidden" name="method" value="{{ $payment->method }}">
                    @endif

                    @error('method')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>



                {{-- Trạng thái thanh toán --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái thanh toán</label>
                    @php
                        $paymentStatusOptions = [
                            'pending' => 'Chờ xử lý',
                            'paid' => 'Thanh toán thành công',
                            'refunded' => 'Hoàn trả',
                            'failed' => 'Thất bại',
                        ];
                    @endphp

                    <select class="form-control" id="status" name="status" {{ $paymentLocked ? 'disabled' : '' }}>
                        @foreach ($paymentStatusOptions as $status => $label)
                            <option value="{{ $status }}"
                                {{ old('status', $payment->status) === $status ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if ($paymentLocked)
                        <input type="hidden" name="status" value="{{ $payment->status }}">
                    @endif
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('payments.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay lại</a>
            </form>
        </div>
    </div>
@endsection
