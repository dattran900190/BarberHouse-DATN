@extends('adminlte::page')

@section('title', 'Tạo lịch thợ')

@section('content')
    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">Tạo lịch cho thợ cắt tóc</h3>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('barber_schedules.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="barber_id">Chọn thợ</label>
                    <select name="barber_id" id="barber_id" class="form-control">
                        <option value="">-- Chọn thợ --</option>
                        @foreach ($barbers as $barber)
                            <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                {{ $barber->name }} ({{ $barber->branch->name ?? 'Không có chi nhánh' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="schedule_date">Ngày làm việc</label>
                    <input type="date" name="schedule_date" id="schedule_date" class="form-control"
                        value="{{ old('schedule_date') }}">
                </div>

                <div class="form-group">
                    <label for="start_time">Giờ bắt đầu</label>
                    <input type="time" name="start_time" id="start_time" class="form-control"
                        value="{{ old('start_time') }}">
                </div>

                <div class="form-group">
                    <label for="end_time">Giờ kết thúc</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time') }}">
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-success">Tạo lịch</button>
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
@endsection
