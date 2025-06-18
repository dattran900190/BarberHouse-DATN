@extends('adminlte::page')

@section('title', 'Lịch làm việc của thợ - Chi nhánh: ' . $branch->name)

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card-header bg-info text-white">
            <h3 class="card-title">Lịch làm việc của thợ - Chi nhánh: {{ $branch->name }}</h3>
        </div>

        <div class="card-body">
            @if ($barbers->count() > 0)
                @foreach ($barbers as $barber)
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0">Thợ: {{ $barber->name }}</h4>
                        </div>
                        <div class="card-body">
                            @if ($barber->schedules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Ngày làm việc</th>
                                                <th>Giờ bắt đầu</th>
                                                <th>Giờ kết thúc</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($barber->schedules as $schedule)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('barber_schedules.edit', $schedule->id) }}"
                                                            class="btn btn-warning btn-sm">Sửa</a>
                                                        <form
                                                            action="{{ route('barber_schedules.destroy', $schedule->id) }}"
                                                            method="POST" style="display: inline-block;"
                                                            onsubmit="return confirm('Bạn có chắc chắn muốn xóa lịch này?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="btn btn-danger btn-sm">Xóa</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">Thợ này chưa có lịch làm việc.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 d-flex">
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Quay lại</a>

                    <a href="{{ route('barber_schedules.createForBranch', $branch->id) }}" class="btn btn-success me-2">Tạo
                        lịch mới</a>
                </div>
            @else
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Thông báo</h5>
                    <p class="mb-0">Chi nhánh này không có thợ cắt tóc nào.</p>
                </div>

                <div class="mt-3 d-flex">
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            @endif
        </div>
    </div>
@endsection
