@extends('adminlte::page')

@section('title', 'Chỉnh sửa Dịch vụ')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-dark">
            <h3 class="card-title mb-0">Chỉnh sửa Dịch vụ</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                    $appointmentLocked = in_array($appointment->status, ['completed', 'cancelled']);
                    $paymentLocked = in_array($appointment->payment_status, ['paid', 'refunded', 'failed']);
                @endphp


                {{-- Trạng thái lịch hẹn --}}
                <div class="mb-3">
                    <label for="status" class="form-label">Trạng thái lịch hẹn</label>
                    <select class="form-control" id="status" name="status"
                        {{ $appointmentLocked || $paymentLocked ? 'disabled' : '' }}>

                        @php
                            $statusOptions = [
                                'pending' => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                            ];
                        @endphp

                        @foreach ($statusOptions as $status => $label)
                            <option value="{{ $status }}"
                                {{ old('status', $appointment->status) === $status ? 'selected' : '' }}
                                @if ($status === 'pending' && $appointment->status !== 'pending') disabled @endif>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @if ($appointmentLocked || $paymentLocked)
                        <input type="hidden" name="status" value="{{ $appointment->status }}">
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

                        @php
                            $paymentOptions = [
                                'unpaid' => 'Chưa thanh toán',
                                'paid' => 'Thanh toán thành công',
                                'refunded' => 'Hoàn trả thanh toán',
                                'failed' => 'Thanh toán thất bại',
                            ];
                        @endphp

                        @foreach ($paymentOptions as $status => $label)
                            <option value="{{ $status }}"
                                {{ old('payment_status', $appointment->payment_status) === $status ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @if ($paymentLocked)
                        <input type="hidden" name="payment_status" value="{{ $appointment->payment_status }}">
                    @endif

                    @error('payment_status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                {{-- Ghi chú --}}
                <div class="mb-3">
                    <label for="note" class="form-label">Ghi chú</label>
                    <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $appointment->note) }}</textarea>
                    @error('note')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">Cập nhật</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Quay lại</a>
            </form>

        </div>
    </div>
@endsection
