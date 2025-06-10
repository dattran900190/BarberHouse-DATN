@extends('adminlte::page')

@section('title', 'Sửa lịch làm việc')

@section('content')
    <div class="card">
        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Sửa lịch làm việc</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('barber_schedules.update', $schedule->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="barber_id">Chọn thợ cắt tóc</label>
                    <select name="barber_id" id="barber_id" class="form-control @error('barber_id') is-invalid @enderror">
                        <option value="">-- Chọn thợ --</option>
                        @foreach ($barbers as $barber)
                            <option value="{{ $barber->id }}"
                                {{ old('barber_id', $schedule->barber_id) == $barber->id ? 'selected' : '' }}>
                                {{ $barber->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('barber_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="schedule_date">Ngày làm việc</label>
                    <input type="date" name="schedule_date" id="schedule_date"
                        class="form-control @error('schedule_date') is-invalid @enderror"
                        value="{{ old('schedule_date', $schedule->schedule_date) }}">
                    @error('schedule_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="start_time">Giờ bắt đầu</label>
                    <input type="time" name="start_time" id="start_time"
                        class="form-control @error('start_time') is-invalid @enderror"
                        value="{{ old('start_time', $schedule->start_time) }}">
                    @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_time">Giờ kết thúc</label>
                    <input type="time" name="end_time" id="end_time"
                        class="form-control @error('end_time') is-invalid @enderror"
                        value="{{ old('end_time', $schedule->end_time) }}">
                    @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if (session('msg'))
                    <div class="alert alert-danger">{{ session('msg') }}</div>
                @endif

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="{{ route('barber_schedules.showBranch', $branch->id) }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
@endsection
