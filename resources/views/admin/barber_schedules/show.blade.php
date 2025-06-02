@extends('adminlte::page')

@section('title', 'Chi tiết lịch cắt tóc')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Chi tiết lịch cắt tóc</h3>
            </div>

            <div class="card-body">
                <div class="mb-3">
                    <strong>Thợ cắt tóc:</strong>
                    <span class="ms-2">{{ $barberSchedule->barber->name }}</span>
                </div>
                <div class="mb-3">
                    <strong>Ngày:</strong>
                    <span class="ms-2">{{ $barberSchedule->schedule_date }}</span>
                </div>
                <div class="mb-3">
                    <strong>Giờ bắt đầu:</strong>
                    <span class="ms-2">{{ $barberSchedule->start_time }}</span>
                </div>
                <div class="mb-3">
                    <strong>Giờ kết thúc:</strong>
                    <span class="ms-2">{{ $barberSchedule->end_time }}</span>
                </div>
                <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
@endsection
