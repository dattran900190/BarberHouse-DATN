@extends('adminlte::page')

@section('title', 'Sửa lịch cắt tóc')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Chỉnh sửa lịch cắt tóc</h3>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('barber_schedules.update', $barberSchedule->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Thợ cắt tóc --}}
                    <div class="mb-3">
                        <label for="barber_id" class="form-label">Thợ cắt tóc</label>
                        <select name="barber_id" id="barber_id" class="form-select">
                            @foreach ($barbers as $barber)
                                <option value="{{ $barber->id }}"
                                    {{ $barber->id == $barberSchedule->barber_id ? 'selected' : '' }}>
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
                        <input type="date" name="schedule_date" id="schedule_date" class="form-control"
                            value="{{ $barberSchedule->schedule_date }}">
                        @error('schedule_date')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giờ bắt đầu --}}
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Giờ bắt đầu</label>
                        <input type="time" name="start_time" id="start_time" class="form-control"
                            value="{{ $barberSchedule->start_time }}">
                        @error('start_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Giờ kết thúc --}}
                    <div class="mb-3">
                        <label for="end_time" class="form-label">Giờ kết thúc</label>
                        <input type="time" name="end_time" id="end_time" class="form-control"
                            value="{{ $barberSchedule->end_time }}">
                        @error('end_time')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Nút hành động --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
