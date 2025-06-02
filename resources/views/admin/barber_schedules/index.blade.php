@extends('adminlte::page')
@section('title', 'Danh sách lịch cắt tóc')

@section('content')
    <div class="card">


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách lịch thợ</h3>
            <a href="{{ route('barber_schedules.create') }}"
                class="btn btn-success btn-icon-toggle d-flex align-items-center">
                <i class="fas fa-plus"></i>
                <span class="btn-text ms-2"> Thêm lịch</span>
            </a>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Thợ cắt tóc</th>
                    <th>Ngày</th>
                    <th>Giờ bắt đầu</th>
                    <th>Giờ kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedules as $schedule)
                    <tr>
                        <td>{{ $schedule->barber->name }}</td>
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
                            <a href="{{ route('barber_schedules.show', $schedule->id) }}"
                                class="btn btn-info btn-sm">Xem</a>
                            <a href="{{ route('barber_schedules.edit', $schedule->id) }}"
                                class="btn btn-warning btn-sm">Sửa</a>
                            <form action="{{ route('barber_schedules.destroy', $schedule->id) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?')"
                                    class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $schedules->links() }}
    </div>

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }
    </style>
@endsection

@endsection
