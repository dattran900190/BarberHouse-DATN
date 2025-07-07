@extends('layouts.AdminLayout')

@section('title', 'Tạo lịch thợ')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">
                Tạo lịch cho thợ cắt tóc
                @if (isset($branch))
                    - Chi nhánh: {{ $branch->name }}
                @endif
            </h3>
        </div>

        <div class="card-body">
            <form action="{{ route('barber_schedules.store') }}" method="POST">
                @csrf

                {{-- Chọn thợ --}}
                <div class="form-group">
                    <label for="barber_id">Chọn thợ</label>
                    <select name="barber_id" id="barber_id" class="form-control @error('barber_id') is-invalid @enderror"
                        required>
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
                    @error('barber_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ngày làm việc --}}
                <div class="form-group">
                    <label for="schedule_date">Ngày làm việc</label>
                    <input type="date" name="schedule_date" id="schedule_date"
                        class="form-control @error('schedule_date') is-invalid @enderror" value="{{ old('schedule_date') }}"
                        required>
                    @error('schedule_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Loại lịch --}}
                <div class="form-group">
                    <label for="status">Loại lịch</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                        required>
                        <option value="off" {{ old('status') == 'off' ? 'selected' : '' }}>Nghỉ cả ngày</option>
                        <option value="custom" {{ old('status') == 'custom' ? 'selected' : '' }}>Thay đổi giờ làm</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Thời gian nếu là custom --}}
                <div id="timeFields" style="display: none;">
                    <div class="form-group">
                        <label for="start_time">Giờ bắt đầu</label>
                        <input type="time" name="start_time" id="start_time"
                            class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}">
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="end_time">Giờ kết thúc</label>
                        <input type="time" name="end_time" id="end_time"
                            class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}">
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nút submit --}}
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-success">Tạo lịch</button>
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
            const startInput = document.getElementById('start_time');
            const endInput = document.getElementById('end_time');

            if (status === 'custom') {
                timeFields.style.display = 'block';
                startInput.disabled = false;
                endInput.disabled = false;
            } else {
                timeFields.style.display = 'none';
                startInput.disabled = true;
                endInput.disabled = true;
                startInput.value = '';
                endInput.value = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            toggleTimeFields();
            document.getElementById('status').addEventListener('change', toggleTimeFields);
        });
    </script>
@endpush
