@extends('layouts.AdminLayout')

@section('title', isset($schedule) ? 'Sửa lịch thợ' : 'Tạo lịch thợ')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">
                {{ isset($schedule) ? 'Sửa lịch cho thợ cắt tóc' : 'Tạo lịch cho thợ cắt tóc' }}
                @if (isset($branch))
                    - Chi nhánh: {{ $branch->name }}
                @endif
            </h3>
        </div>

        <div class="card-body">



            <form
                action="{{ isset($schedule) ? route('barber_schedules.update', $schedule->id) : route('barber_schedules.store') }}"
                method="POST">
                @csrf
                @if (isset($schedule))
                    @method('PUT')
                @endif

                {{-- Chọn thợ --}}
                <div class="form-group">
                    <label for="barber_id">
                        {{ isset($schedule) ? 'Thợ cắt tóc' : 'Chọn thợ' }}
                    </label>
                    @if (isset($schedule))
                        {{-- Khi sửa: chỉ hiển thị tên thợ, không cho thay đổi --}}
                        <input type="text" class="form-control" value="{{ $schedule->barber->name }}" readonly>
                        <input type="hidden" name="barber_id" value="{{ $schedule->barber_id }}">
                    @else
                        {{-- Khi tạo mới: cho phép chọn thợ --}}
                        <select name="barber_id" id="barber_id"
                            class="form-control @error('barber_id') is-invalid @enderror">
                            <option value="">-- Chọn thợ --</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}
                                    @if (!isset($branch))
                                        ({{ $barber->branch->name ?? 'Không có chi nhánh' }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('barber_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ngày làm việc --}}
                <div class="form-group">
                    <label for="schedule_date">Ngày làm việc</label>
                    <input type="date" name="schedule_date" id="schedule_date"
                        class="form-control @error('schedule_date') is-invalid @enderror"
                        value="{{ old('schedule_date') ?? (isset($schedule) ? $schedule->schedule_date : '') }}"
                        {{ isset($schedule) }}>
                    @error('schedule_date')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Loại lịch --}}
                <div class="form-group">
                    <label for="status">Loại lịch</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="off"
                            {{ (old('status') ?? (isset($schedule) ? $schedule->status : '')) == 'off' ? 'selected' : '' }}>
                            Nghỉ cả ngày
                        </option>
                        <option value="custom"
                            {{ (old('status') ?? (isset($schedule) ? $schedule->status : '')) == 'custom' ? 'selected' : '' }}>
                            Thay đổi giờ làm
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Thời gian nếu là custom --}}
                <div id="timeFields" style="display: none;">
                    <div class="form-group">
                        <label for="start_time">Giờ bắt đầu</label>
                        <input type="time" name="start_time" id="start_time"
                            class="form-control @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time') ?? (isset($schedule) ? $schedule->start_time : '') }}">
                        @error('start_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_time">Giờ kết thúc</label>
                        <input type="time" name="end_time" id="end_time"
                            class="form-control @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time') ?? (isset($schedule) ? $schedule->end_time : '') }}">
                        @error('end_time')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nút submit --}}
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-success">
                        {{ isset($schedule) ? 'Cập nhật lịch' : 'Tạo lịch' }}
                    </button>
                    @if (isset($branch))
                        <a href="{{ route('barber_schedules.showBranch', $branch->id) }}" class="btn btn-secondary">Quay
                            lại</a>
                    @else
                        <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Quay lại</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function toggleTimeFields() {
            const status = document.getElementById('status').value;
            const timeFields = document.getElementById('timeFields');

            if (status === 'custom') {
                timeFields.style.display = 'block';
            } else {
                timeFields.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleTimeFields();
            document.getElementById('status').addEventListener('change', toggleTimeFields);
        });
    </script>
@endpush
