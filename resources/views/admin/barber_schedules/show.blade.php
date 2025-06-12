@extends('adminlte::page')

@section('title', 'Lịch thợ - ' . $branch->name)

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card-header bg-info text-white">
            <h3 class="card-title mb-0">Lịch làm việc của thợ - Chi nhánh: {{ $branch->name }}</h3>
        </div>

        <div class="card-body">
            @if ($branch->barbers->isEmpty())
                <p>Chi nhánh này hiện chưa có thợ nào.</p>
            @else
                @foreach ($branch->barbers as $barber)
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <strong>Thợ: {{ $barber->name }}</strong>
                        </div>
                        <div class="card-body p-0">
                            @if ($barber->schedules->isEmpty())
                                <div class="p-3">Thợ này chưa có lịch làm việc.</div>
                            @else
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th>Ngày</th>
                                            <th>Giờ bắt đầu</th>
                                            <th>Giờ kết thúc</th>
                                            <th>Trạng thái</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($barber->schedules as $schedule)
                                            <tr>
                                                <td>{{ $schedule->schedule_date }}</td>
                                                <td>{{ $schedule->start_time }}</td>
                                                <td>{{ $schedule->end_time }}</td>
                                                <td>
                                                    @if ($schedule->is_available)
                                                        <span class="badge badge-success">Có lịch</span>
                                                    @else
                                                        <span class="badge badge-secondary">Đã đặt</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (auth()->user()->role === 'admin_branch')
                                                        <a href="{{ route('barber_schedules.edit', $schedule->id) }}"
                                                            class="btn btn-warning btn-sm">Sửa</a>
                                                        <form
                                                            action="{{ route('barber_schedules.destroy', $schedule->id) }}"
                                                            method="POST" style="display:inline-block">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Bạn có chắc muốn hủy lịch này?');">
                                                                Hủy
                                                            </button>
                                                        </form>
                                                    @endif




                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="card-footer d-flex justify-content-start gap-2">
            <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary mt-3">← Quay lại</a>
            @if (auth()->user()->role === 'admin_branch')
                <a href="{{ route('barber_schedules.create') }}" class="btn btn-primary mt-3">Tạo lịch mới</a>
            @endif
            @php
                $firstSchedule = null;
                foreach ($branch->barbers as $barber) {
                    if ($barber->schedules->isNotEmpty()) {
                        $firstSchedule = $barber->schedules->first();
                        break;
                    }
                }
            @endphp

        </div>
    </div>
@endsection
