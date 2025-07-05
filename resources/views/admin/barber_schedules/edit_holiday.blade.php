@extends('adminlte::page')
@section('title', 'Chỉnh sửa lịch nghỉ lễ')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h3 class="card-title mb-0">Chỉnh sửa lịch nghỉ lễ</h3>
        </div>

        <div class="card-body">
            <form action="{{ route('barber_schedules.updateHoliday', $id) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="status" value="holiday">
                <input type="hidden" name="old_start" value="{{ $holiday['holiday_start_date'] }}">
                <input type="hidden" name="old_end" value="{{ $holiday['holiday_end_date'] }}">
                <input type="hidden" name="old_note" value="{{ $holiday['note'] }}">

                <div class="form-group">
                    <input type="hidden" name="id" value="{{ $id }}">
                    <label for="holiday_start_date">Ngày bắt đầu</label>
                    <input type="date" name="holiday_start_date" class="form-control"
                        value="{{ $holiday['holiday_start_date'] }}">
                </div>

                <div class="form-group">
                    <label for="holiday_end_date">Ngày kết thúc</label>
                    <input type="date" name="holiday_end_date" class="form-control"
                        value="{{ $holiday['holiday_end_date'] }}">
                </div>

                <div class="form-group">
                    <label for="note">Tên kỳ nghỉ lễ</label>
                    <input type="text" name="note" class="form-control" value="{{ $holiday['note'] }}">
                </div>

                <button type="submit" class="btn btn-success">Cập nhật</button>
                <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Huỷ</a>
            </form>
        </div>
    </div>
@endsection
