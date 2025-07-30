@extends('layouts.AdminLayout')

@section('title', 'Thống kê thợ')

@section('content')
    <div class="page-inner">
        <div class="page-header">
            <h4 class="page-title">Thống kê thợ theo chi nhánh</h4>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin/dashboard') }}">Quản lý Chi nhánh</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ url('admin/barber-statistics') }}">Thống kê thợ</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <!-- Bộ lọc -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Bộ lọc</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('barber_statistics.index') }}" class="row">
                            <div class="col-md-3">
                                <label>Tháng</label>
                                <select name="month" class="form-control">
                                    @foreach ($availableMonths as $month)
                                        <option value="{{ $month }}"
                                            {{ $selectedMonth == $month ? 'selected' : '' }}>
                                            Tháng {{ $month }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Năm</label>
                                <select name="year" class="form-control">
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @if (Auth::user()->role !== 'admin_branch')
                                <div class="col-md-3">
                                    <label>Chi nhánh</label>
                                    <select name="branch_id" class="form-control">
                                        <option value="">Tất cả chi nhánh</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}"
                                                {{ $selectedBranch == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">Lọc</button>
                                    <a href="{{ route('barber_statistics.export', request()->query()) }}"
                                        class="btn btn-success">
                                        <i class="fas fa-download"></i> Xuất Excel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Thống kê tổng quan -->
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Tổng thợ</p>
                                            <h4 class="card-title">{{ $totalStats['total_barbers'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-warning bubble-shadow-small">
                                            <i class="fas fa-calendar-times"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Ngày nghỉ</p>
                                            <h4 class="card-title">{{ $totalStats['total_off_days'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Nghỉ lễ</p>
                                            <h4 class="card-title">{{ $totalStats['total_holiday_days'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Đổi ca</p>
                                            <h4 class="card-title">{{ $totalStats['total_custom_days'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ml-3 ml-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Làm việc</p>
                                            <h4 class="card-title">{{ $totalStats['total_working_days'] }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng thống kê chi tiết -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Thống kê chi tiết theo thợ</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên thợ</th>
                                        <th>Chi nhánh</th>
                                        <th class="text-center">Ngày nghỉ</th>
                                        <th class="text-center">Nghỉ lễ</th>
                                        <th class="text-center">Đổi ca</th>
                                        <th class="text-center">Tổng nghỉ</th>
                                        <th class="text-center">Làm việc</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($barbers as $index => $barber)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($barber->avatar)
                                                        <img src="{{ asset('storage/' . $barber->avatar) }}"
                                                            alt="{{ $barber->name }}" class="rounded-circle mr-2"
                                                            width="40" height="40">
                                                    @else
                                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2"
                                                            style="width: 40px; height: 40px;">
                                                            {{ substr($barber->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <span>{{ $barber->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $barber->branch?->name }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-warning">{{ $barber->off_days }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-success">{{ $barber->holiday_days }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $barber->custom_days }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-danger">{{ $barber->total_off }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $barber->working_days }}</span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('barber_statistics.show', $barber->id) }}?month={{ $selectedMonth }}&year={{ $selectedYear }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
