@extends('adminlte::page')

@section('title', 'Chỉnh sửa lịch hẹn')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa lịch hẹn</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="page" value="{{ request('page', 1) }}">

                {{-- Thời gian hẹn --}}
                <div class="mb-3">
                    <label for="appointment_time" class="form-label">Thời gian hẹn</label>
                    <input type="datetime-local" id="appointment_time" name="appointment_time" class="form-control"
                        value="{{ old('appointment_time', \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d\TH:i')) }}">
                    @error('appointment_time')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                @php
                    $currentStatus = $appointment->status;
                    $currentPaymentStatus = $appointment->payment_status;

                    $statusOptions = [
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ];
                    $paymentOptions = [
                        'unpaid' => 'Chưa thanh toán',
                        'paid' => 'Thanh toán thành công',
                        'refunded' => 'Hoàn trả thanh toán',
                        'failed' => 'Thanh toán thất bại',
                    ];

                    // Khi payment đã 'paid' hoặc 'refunded' hoặc 'failed', khóa select này
                    $paymentLocked = in_array($currentPaymentStatus, ['paid', 'refunded']);
                    // (Nếu bạn vẫn muốn cho ‘failed’ → ‘paid’, thì hãy loại bỏ 'failed' khỏi mảng trên)
                @endphp

                {{-- Trạng thái lịch hẹn --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái lịch hẹn</label>
                    <select class="form-control" id="status" name="status" {{-- Nếu đã cancelled hoặc completed thì disable toàn bộ --}}
                        @if (in_array($currentStatus, ['completed', 'cancelled'])) disabled @endif>
                        @foreach ($statusOptions as $statusValue => $label)
                            <option value="{{ $statusValue }}"
                                {{ old('status', $currentStatus) === $statusValue ? 'selected' : '' }} {{-- Disabled từng option khi cần --}}
                                @if (
                                    // Nếu đang cancelled ⇒ disable mọi option khác
                                    ($currentStatus === 'cancelled' && $statusValue !== 'cancelled') ||
                                        // Nếu đang completed ⇒ disable mọi option khác
                                        ($currentStatus === 'completed' && $statusValue !== 'completed') ||
                                        // Nếu đang confirmed ⇒ disable option 'pending'
                                        ($currentStatus === 'confirmed' && $statusValue === 'pending') ||
                                        // Nếu đang completed/cancelled ⇒ disable 'pending','confirmed'
                                        (in_array($currentStatus, ['completed', 'cancelled']) && in_array($statusValue, ['pending', 'confirmed']))) disabled @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if (in_array($currentStatus, ['completed', 'cancelled']))
                        <small class="text-muted">
                            Không thể thay đổi vì lịch đã {{ $currentStatus === 'completed' ? 'hoàn thành' : 'bị huỷ' }}.
                        </small>
                    @endif

                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái thanh toán --}}
                <div class="mb-3">
                    <label for="payment_status" class="form-label">Trạng thái thanh toán</label>
                    <select class="form-control" id="payment_status" name="payment_status"
                        {{ $paymentLocked ? 'disabled' : '' }}>
                        @foreach ($paymentOptions as $payValue => $label)
                            <option value="{{ $payValue }}"
                                {{ old('payment_status', $currentPaymentStatus) === $payValue ? 'selected' : '' }}
                                @if (
                                    // Nếu đã refunded ⇒ disable mọi option khác
                                    ($currentPaymentStatus === 'refunded' && $payValue !== 'refunded') ||
                                        // Nếu đã paid ⇒ disable 'unpaid'
                                        ($currentPaymentStatus === 'paid' && $payValue === 'unpaid') ||
                                        // Nếu đã failed ⇒ disable 'unpaid'
                                        ($currentPaymentStatus === 'failed' && $payValue === 'unpaid')) disabled @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if ($paymentLocked)
                        <input type="hidden" name="payment_status" value="{{ $currentPaymentStatus }}">
                        <small class="text-muted">
                            Thanh toán đã {{ $currentPaymentStatus === 'refunded' ? 'hoàn trả' : 'thành công' }}, không thể
                            thay đổi.
                        </small>
                    @endif

                    @error('payment_status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('appointments.index', ['page' => request('page', 1)]) }}" class="btn btn-secondary">Quay
                    lại</a>
            </form>

        </div>
    </div>
@endsection
