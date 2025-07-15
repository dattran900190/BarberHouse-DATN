@extends('layouts.AdminLayout')

@section('title', 'Chỉnh sửa lịch nghỉ lễ')

@section('content')
    <div class="page-header mb-4">
        <h3 class="fw-bold">Chỉnh sửa lịch nghỉ lễ</h3>
        <ul class="breadcrumbs">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('barber_schedules.index') }}">Lịch làm việc</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Chỉnh sửa nghỉ lễ</a></li>
        </ul>
    </div>

    <div class="card shadow-sm border">
        <form action="{{ route('barber_schedules.updateHoliday', $id) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="status" value="holiday">
            <input type="hidden" name="old_start" value="{{ $holiday['holiday_start_date'] }}">
            <input type="hidden" name="old_end" value="{{ $holiday['holiday_end_date'] }}">
            <input type="hidden" name="old_note" value="{{ $holiday['note'] }}">
            <input type="hidden" name="id" value="{{ $id }}">

            <div class="card-body px-4 py-4">
                <div class="row g-4">
                    {{-- Cột trái: Ngày bắt đầu & kết thúc --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="holiday_start_date" class="form-label">Ngày bắt đầu nghỉ</label>
                            <input type="date" name="holiday_start_date"
                                class="form-control @error('holiday_start_date') is-invalid @enderror"
                                value="{{ old('holiday_start_date', $holiday['holiday_start_date']) }}">
                            @error('holiday_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="holiday_end_date" class="form-label">Ngày kết thúc nghỉ</label>
                            <input type="date" name="holiday_end_date"
                                class="form-control @error('holiday_end_date') is-invalid @enderror"
                                value="{{ old('holiday_end_date', $holiday['holiday_end_date']) }}">
                            @error('holiday_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Cột phải: Tên kỳ nghỉ --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="note" class="form-label">Tên kỳ nghỉ lễ</label>
                            <input type="text" name="note" class="form-control @error('note') is-invalid @enderror"
                                placeholder="VD: Tết Âm Lịch, Giỗ tổ Hùng Vương..."
                                value="{{ old('note', $holiday['note']) }}">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Nút Cập nhật / Huỷ --}}
                <div class="d-flex justify-content-start mt-4 gap-2">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-save me-1"></i> Cập nhật
                    </button>
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-sm btn-outline-secondary">
                        Huỷ
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
