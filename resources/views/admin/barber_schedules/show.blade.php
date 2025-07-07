@extends('layouts.AdminLayout')

@section('title', 'Lịch làm việc của thợ - Chi nhánh: ' . $branch->name)

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card-header bg-info text-white">
            <h3 class="card-title">Lịch làm việc đặc biệt của thợ - Chi nhánh: {{ $branch->name }}</h3>
        </div>

        <div class="card-body">
            <form method="GET" class="mb-3 d-flex align-items-end gap-3">
                <div class="form-group mb-0">
                    <label for="filter">Lọc loại lịch</label>
                    <select name="filter" id="filter" class="form-control">
                        <option value="">-- Tất cả --</option>
                        <option value="off" {{ request('filter') === 'off' ? 'selected' : '' }}>Nghỉ cả ngày</option>
                        <option value="custom" {{ request('filter') === 'custom' ? 'selected' : '' }}>Thay đổi giờ làm
                        </option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Lọc</button>
            </form>

            @if ($barbers->count() > 0)
                @foreach ($barbers as $barber)
                    @php
                        $schedules = $barber->schedules
                            ->when(request('filter'), function ($query) {
                                return $query->where('status', request('filter'));
                            })
                            ->sortBy('schedule_date');
                    @endphp

                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h4 class="card-title mb-0">Thợ: {{ $barber->name }}</h4>
                        </div>
                        <div class="card-body">
                            @if ($schedules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Ngày</th>
                                                <th>Giờ bắt đầu</th>
                                                <th>Giờ kết thúc</th>
                                                <th>Ghi chú</th>
                                                <th>Hành động</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($schedules as $schedule)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}
                                                    </td>

                                                    @if ($schedule->status === 'off')
                                                        <td colspan="2" class="text-center text-danger">Nghỉ cả ngày</td>
                                                        <td class="text-danger">Nghỉ phép / Lễ</td>
                                                    @else
                                                        <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                        </td>
                                                        <td class="text-warning">Thay đổi giờ làm do việc cá nhân</td>
                                                    @endif

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
                                    <p class="text-muted">* Những ngày khác {{ $barber->name }} làm việc bình thường.</p>
                                </div>
                            @else
                                <p class="text-muted">Thợ này không có lịch thay đổi phù hợp với bộ lọc hiện tại.</p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 d-flex justify-content-between">
                    <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary">← Quay lại</a>
                    <a href="{{ route('barber_schedules.createForBranch', $branch->id) }}" class="btn btn-success">+ Chỉnh
                        sửa lịch trình</a>
                </div>
            @else
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> Không có thợ</h5>
                    <p>Chi nhánh này hiện không có thợ cắt tóc nào.</p>
                </div>
                <a href="{{ route('barber_schedules.index') }}" class="btn btn-secondary mt-3">← Quay lại</a>
            @endif
        </div>
    </div>
@endsection
