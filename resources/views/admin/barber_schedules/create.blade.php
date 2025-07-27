@extends('layouts.AdminLayout')

@section('title', 'Tạo lịch thợ')

@section('content')
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tạo Lịch Nghỉ </h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ url('admin/dashboard') }}"><i class="icon-home"></i></a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Lịch thợ</a></li>
            <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="#">Tạo lịch</a></li>
        </ul>
    </div>

    <div class="card">

        <div class="card-header text-white align-items-center">
            <div class="card-title">Tạo lịch cho thợ @if (isset($branch))
                    - Chi nhánh: {{ $branch->name }}
                @endif
            </div>
        </div>

        <div class="card-body">
            <form action="{{ route('barber_schedules.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Bên trái --}}
                    <div class="col-md-6">
                        <div class="mb-3" id="barberField">
                            <label for="barber_id" class="form-label">Chọn thợ</label>
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
                            @error('barber_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="scheduleDateField">
                            <label for="schedule_date" class="form-label">Ngày làm việc</label>
                            <input type="date" name="schedule_date" id="schedule_date"
                                class="form-control @error('schedule_date') is-invalid @enderror"
                                value="{{ old('schedule_date') }}">
                            @error('schedule_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Bên phải --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Loại lịch</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror"
                                required>
                                <option value="">-- Chọn loại lịch --</option>
                                <option value="off" {{ old('status') == 'off' ? 'selected' : '' }}>Nghỉ cả ngày</option>
                                <option value="custom" {{ old('status') == 'custom' ? 'selected' : '' }}>Thay đổi giờ làm
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Thời gian nếu là custom --}}
                        <div id="timeFields" style="display: none;">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Giờ bắt đầu</label>
                                <input type="time" name="start_time" id="start_time"
                                    class="form-control @error('start_time') is-invalid @enderror"
                                    value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="end_time" class="form-label">Giờ kết thúc</label>
                                <input type="time" name="end_time" id="end_time"
                                    class="form-control @error('end_time') is-invalid @enderror"
                                    value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>


                <div class="mt-4">
                    <button type="submit" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-plus"></i> <span class="ms-2">Tạo lịch</span>
                    </button>
                    @if (isset($branch))
                        <a href="{{ route('barber_schedules.showBranch', $branch->id) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-arrow-left me-1"></i> Quay lại
                        </a>
                    @else
                        <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Quay lại</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function toggleFields() {
            const status = document.getElementById('status').value;

            document.getElementById('timeFields').style.display = status === 'custom' ? 'block' : 'none';

        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleFields();
            document.getElementById('status').addEventListener('change', toggleFields);
        });
    </script>
@endsection
