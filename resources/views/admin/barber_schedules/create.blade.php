@extends('adminlte::page')

@section('title', 'Thêm lịch cắt tóc')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Thêm Lịch Cắt Tóc</h3>
            </div>

            <div class="card-body">
                <form action="{{ route('barber_schedules.store') }}" method="POST">
                    @csrf

                    {{-- Thợ cắt tóc --}}
                    <div class="mb-3">
                        <label for="barber_id" class="form-label">Thợ cắt tóc</label>
                        <select name="barber_id" id="barber_id" class="form-select">
                            <option value="">-- Chọn thợ --</option>
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                    {{ $barber->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('barber_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ngày --}}
                    <div class="mb-3">
                        <label for="schedule_date" class="form-label">Ngày</label>
                        <input type="date" name="schedule_date" class="form-control" value="{{ old('schedule_date') }}">
                        @error('schedule_date')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Giờ bắt đầu --}}
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Giờ bắt đầu</label>
                        <input type="time" name="start_time" id="start_time" class="form-control"
                            value="{{ old('start_time', $barberSchedule->start_time ?? '') }}">
                        @error('start_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giờ kết thúc --}}
                    <div class="mb-3">
                        <label for="end_time" class="form-label">Giờ kết thúc</label>
                        <input type="time" name="end_time" id="end_time" class="form-control"
                            value="{{ old('end_time', $barberSchedule->end_time ?? '') }}">
                        @error('end_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                        <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
