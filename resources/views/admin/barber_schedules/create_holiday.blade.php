@extends('layouts.AdminLayout')

@section('title', 'Tạo lịch nghỉ lễ')

@section('content')
    <div class="page-header mb-4">
        <h3 class="fw-bold">Tạo lịch nghỉ lễ</h3>
        <ul class="breadcrumbs">
            <li class="nav-home"><a href="{{ route('dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('barber_schedules.index') }}">Lịch làm việc</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Tạo lịch nghỉ lễ</a></li>
        </ul>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header text-white align-items-center">
            <div class="card-title">Tạo lịch nghỉ lễ
            </div>
        </div>
        <form action="{{ route('barber_schedules.store') }}" method="POST">
            @csrf
            <input type="hidden" name="status" value="holiday">

            <div class="card-body px-4 py-4">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="holiday_start_date" class="form-label">Ngày bắt đầu nghỉ</label>
                            <input type="date" name="holiday_start_date" id="holiday_start_date"
                                class="form-control @error('holiday_start_date') is-invalid @enderror"
                                value="{{ old('holiday_start_date') }}">
                            @error('holiday_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="holiday_end_date" class="form-label">Ngày kết thúc nghỉ</label>
                            <input type="date" name="holiday_end_date" id="holiday_end_date"
                                class="form-control @error('holiday_end_date') is-invalid @enderror"
                                value="{{ old('holiday_end_date') }}">
                            @error('holiday_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="note" class="form-label">Tên dịp nghỉ lễ</label>
                            <input type="text" name="note" id="note"
                                class="form-control @error('note') is-invalid @enderror"
                                placeholder="VD: Tết Nguyên Đán, Lễ 30/4 - 1/5..." value="{{ old('note') }}">
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Tạo lịch nghỉ lễ
                    </button>
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </form>
    </div>
@endsection
