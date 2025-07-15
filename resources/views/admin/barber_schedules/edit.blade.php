@extends('layouts.AdminLayout')

@section('title', isset($schedule) ? 'Sửa lịch thợ' : 'Tạo lịch thợ')

@section('content')

    <div class="card">
        <div class="page-header mb-4">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <h3 class="fw-bold mb-0">
                    {{ isset($schedule) ? 'Chỉnh sửa Lịch Thợ Cắt Tóc' : 'Tạo Lịch Thợ Cắt Tóc' }}
                </h3>
                @if (isset($branch))
                    <span class="fw-normal ms-2" style="font-size: 1.2rem; color: #333;">
                        - Chi nhánh: {{ $branch->name }}
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <form
                action="{{ isset($schedule) ? route('barber_schedules.update', $schedule->id) : route('barber_schedules.store') }}"
                method="POST">
                @csrf
                @if (isset($schedule))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="barber_id" class="form-label">
                            {{ isset($schedule) ? 'Thợ cắt tóc' : 'Chọn thợ' }}
                        </label>
                        @if (isset($schedule))
                            <input type="text" class="form-control" value="{{ $schedule->barber->name }}" readonly>
                            <input type="hidden" name="barber_id" value="{{ $schedule->barber_id }}">
                        @else
                            <select name="barber_id" id="barber_id"
                                class="form-control @error('barber_id') is-invalid @enderror">
                                <option value="">-- Chọn thợ --</option>
                                @foreach ($barbers as $barber)
                                    <option value="{{ $barber->id }}"
                                        {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->name }}
                                        @if (!isset($branch))
                                            ({{ $barber->branch->name ?? 'Không có chi nhánh' }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('barber_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="schedule_date" class="form-label">Ngày làm việc</label>
                        <input type="date" name="schedule_date" id="schedule_date"
                            class="form-control @error('schedule_date') is-invalid @enderror"
                            value="{{ old('schedule_date') ?? (isset($schedule) ? $schedule->schedule_date : '') }}">
                        @error('schedule_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Loại lịch</label>
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
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3" id="timeFields" style="display: none;">
                        <label for="start_time" class="form-label">Giờ bắt đầu</label>
                        <input type="time" name="start_time" id="start_time"
                            class="form-control @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time') ?? (isset($schedule) ? $schedule->start_time : '') }}">
                        @error('start_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror

                        <label for="end_time" class="form-label mt-2">Giờ kết thúc</label>
                        <input type="time" name="end_time" id="end_time"
                            class="form-control @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time') ?? (isset($schedule) ? $schedule->end_time : '') }}">
                        @error('end_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-warning">
                        <i class="fa fa-edit me-1"></i> {{ isset($schedule) ? 'Cập nhật lịch' : 'Tạo lịch' }}
                    </button>
                    @if (isset($branch))
                        <a href="{{ route('barber_schedules.showBranch', $branch->id) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    @else
                        <a href="{{ route('barber_schedules.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
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
@endsection
