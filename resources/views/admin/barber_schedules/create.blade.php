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
                <div class="form-group" id="barberField">
                    <label for="barber_id">Chọn thợ</label>
                    <select name="barber_id" id="barber_id" class="form-control @error('barber_id') is-invalid @enderror">
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
                <div class="form-group" id="scheduleDateField">
                    <label for="schedule_date">Ngày làm việc</label>
                    <input type="date" name="schedule_date" id="schedule_date"
                        class="form-control @error('schedule_date') is-invalid @enderror"
                        value="{{ old('schedule_date') }}">
                    @error('schedule_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Loại lịch --}}
                <div class="form-group">
                    <label for="status">Loại lịch</label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                        required>
                        <option value="">-- Chọn loại lịch --</option>
                        <option value="off" {{ old('status') == 'off' ? 'selected' : '' }}>Nghỉ cả ngày</option>
                        <option value="custom" {{ old('status') == 'custom' ? 'selected' : '' }}>Thay đổi giờ làm</option>
                        <option value="holiday" {{ old('status') == 'holiday' ? 'selected' : '' }}>Nghỉ lễ (toàn hệ thống)
                        </option>
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

                {{-- Thông tin nghỉ lễ --}}
                <div id="holidayFields" style="display: none;">
                    <div class="form-group">
                        <label for="holiday_start_date">Ngày bắt đầu nghỉ lễ</label>
                        <input type="date" name="holiday_start_date" id="holiday_start_date"
                            class="form-control @error('holiday_start_date') is-invalid @enderror"
                            value="{{ old('holiday_start_date') }}">
                        @error('holiday_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="holiday_end_date">Ngày kết thúc nghỉ lễ</label>
                        <input type="date" name="holiday_end_date" id="holiday_end_date"
                            class="form-control @error('holiday_end_date') is-invalid @enderror"
                            value="{{ old('holiday_end_date') }}">
                        @error('holiday_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="note">Tên kỳ nghỉ lễ</label>
                        <input type="text" name="note" id="note"
                            class="form-control @error('note') is-invalid @enderror" value="{{ old('note') }}">
                        @error('note')
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
        function toggleFields() {
            const status = document.getElementById('status').value;

            document.getElementById('timeFields').style.display = status === 'custom' ? 'block' : 'none';
            document.getElementById('holidayFields').style.display = status === 'holiday' ? 'block' : 'none';

            // Ẩn barber và ngày thường khi nghỉ lễ
            const hideOnHoliday = (status === 'holiday');
            document.getElementById('barberField').style.display = hideOnHoliday ? 'none' : 'block';
            document.getElementById('scheduleDateField').style.display = hideOnHoliday ? 'none' : 'block';
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            document.getElementById('status').addEventListener('change', toggleFields);
        });
    </script>
@endpush
