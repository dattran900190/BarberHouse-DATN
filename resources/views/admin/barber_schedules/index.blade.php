@extends('layouts.AdminLayout')
@section('title', 'Danh sách lịch theo chi nhánh')

@section('content')
    <div class="card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @php
            $currentRole = Auth::user()->role;
        @endphp

        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 text-center flex-grow-1">Danh sách Chi nhánh</h3>
                <a href="{{ route('barber_schedules.createHoliday') }}"
                    class="btn btn-success btn-icon-toggle d-flex align-items-center">
                    <i class="fas fa-plus"></i>
                    <span class="btn-text ms-2"> Tạo lịch nghỉ lễ</span>
                </a>
            </div>

            <div class="card-body">
                @php
                    $holidays = \App\Models\BarberSchedule::where('status', 'holiday')
                        ->where('holiday_end_date', '>=', now()->toDateString())
                        ->select('holiday_start_date', 'holiday_end_date', 'note')
                        ->groupBy('holiday_start_date', 'holiday_end_date', 'note')
                        ->orderBy('holiday_start_date')
                        ->get();
                @endphp

                @if ($holidays->count())
                    <div class="alert alert-warning shadow-sm">
                        <h5 class="mb-3 text-danger">
                            <i class="fas fa-bell"></i> Thông báo nghỉ lễ
                        </h5>
                        <ul class="list-unstyled mb-0">
                            @foreach ($holidays as $holiday)
                                @php
                                    // Lấy bản ghi đầu tiên của kỳ nghỉ lễ này (có thể là MAX hoặc MIN id cũng được)
                                    $firstSchedule = \App\Models\BarberSchedule::where(
                                        'holiday_start_date',
                                        $holiday->holiday_start_date,
                                    )
                                        ->where('holiday_end_date', $holiday->holiday_end_date)
                                        ->where('note', $holiday->note)
                                        ->first();
                                @endphp

                                @if ($firstSchedule)
                                    <li
                                        class="holiday-item d-flex justify-content-between align-items-center p-2 mb-1 rounded border border-warning bg-light position-relative">
                                        <div>
                                            <i class="fas fa-calendar-day text-danger"></i>
                                            <strong>{{ $holiday->note }}</strong>:
                                            {{ \Carbon\Carbon::parse($holiday->holiday_start_date)->format('d/m/Y') }}
                                            - {{ \Carbon\Carbon::parse($holiday->holiday_end_date)->format('d/m/Y') }}
                                        </div>
                                        @if($currentRole == 'admin')
                                        <div class="action-buttons d-none d-md-flex">
                                            <a href="{{ route('barber_schedules.editHoliday', $firstSchedule->id) }}"
                                                class="btn btn-sm btn-warning text-white me-2">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('barber_schedules.deleteHoliday', $firstSchedule->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xoá kỳ nghỉ này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                        @endif
                                    </li>
                                @endif
                            @endforeach

                        </ul>
                    </div>
                @endif

                {{-- Form tìm kiếm --}}
                <form action="{{ route('barber_schedules.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Tìm kiếm theo tên chi nhánh..." value="{{ request()->get('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Tìm kiếm</button>
                        </div>
                    </div>
                </form>

                {{-- Danh sách chi nhánh --}}
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Tên chi nhánh</th>
                            <th>Địa chỉ</th>
                            <th>Điện thoại</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branches as $branch)
                            <tr>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->address }}</td>
                                <td>{{ $branch->phone }}</td>
                                <td>
                                    <a href="{{ route('barber_schedules.showBranch', $branch->id) }}"
                                        class="btn btn-info btn-sm uniform-btn">Xem</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .btn-icon-toggle .btn-text {
            display: none;
            transition: opacity 0.3s ease;
        }

        .btn-icon-toggle:hover .btn-text {
            display: inline;
        }

        .input-group input {
            border-right: 0;
        }

        .input-group .input-group-append {
            border-left: 0;
        }

        .input-group button {
            border-radius: 0;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }

        .uniform-btn {
            min-width: 110px;
            min-height: 36px;
            padding: 6px 12px;
            font-size: 14px;
            white-space: nowrap;
            text-align: center;
        }

        .holiday-item {
            transition: background-color 0.3s ease;
            position: relative;
        }

        .holiday-item:hover {
            background-color: #fff3cd;
        }

        /* ...existing code... */
        .action-buttons {
            display: none !important;
        }

        .holiday-item:hover .action-buttons {
            display: flex !important;
        }

        /* ...existing code... */

        .action-buttons a,
        .action-buttons button {
            padding: 5px 8px;
            font-size: 13px;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
        }

        .action-buttons a i,
        .action-buttons button i {
            margin: 0;
        }

        .action-buttons a:hover,
        .action-buttons button:hover {
            opacity: 0.85;
        }
    </style>
@endsection
