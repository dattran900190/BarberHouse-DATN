@extends('adminlte::page')
@section('title', 'Tạo lịch nghỉ lễ')

@section('content')
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h3 class="card-title">Tạo lịch nghỉ lễ</h3>
        </div>

        <form action="{{ route('barber_schedules.store') }}" method="POST">
            @csrf

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

                <input type="hidden" name="status" value="holiday">

                <div class="form-group">
                    <label for="holiday_start_date">Ngày bắt đầu nghỉ</label>
                    <input type="date" name="holiday_start_date" id="holiday_start_date" class="form-control"
                        value="{{ old('holiday_start_date') }}">
                </div>

                <div class="form-group">
                    <label for="holiday_end_date">Ngày kết thúc nghỉ</label>
                    <input type="date" name="holiday_end_date" id="holiday_end_date" class="form-control"
                        value="{{ old('holiday_end_date') }}">
                </div>

                <div class="form-group">
                    <label for="note">Tên dịp nghỉ lễ</label>
                    <input type="text" name="note" id="note" class="form-control"
                        placeholder="VD: Tết Nguyên Đán, Lễ 30/4 - 1/5..." value="{{ old('note') }}">
                </div>
            </div>

            <div class="form-buttons mt-3">
                <button type="submit" class="btn btn-success me-2">Tạo lịch nghỉ lễ </button>
                <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Huỷ</a>
            </div>


        </form>
    </div>
@endsection
@section('css')
    <style>
        .form-buttons {
            display: flex;
            justify-content: flex-start;
            gap: 0.5rem;
        }

        .form-buttons .btn {
            min-width: 100px;
            font-weight: 500;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
@endsection
