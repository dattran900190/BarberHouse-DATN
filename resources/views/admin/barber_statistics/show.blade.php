@extends('layouts.AdminLayout')

@section('title', 'Chi tiết thống kê thợ')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h4 class="page-title">Chi tiết thống kê thợ: {{ $barber->name }}</h4>
        <ul class="breadcrumbs">
            <li class="nav-home">
                <a href="{{ route('dashboard') }}">
                    <i class="flaticon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('barber_statistics.index') }}">Thống kê thợ</a>
            </li>
            <li class="separator">
                <i class="flaticon-right-arrow"></i>
            </li>
            <li class="nav-item">
                <span>{{ $barber->name }}</span>
            </li>
        </ul>
    </div>

    <div class="row">
        <!-- Thông tin thợ -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Thông tin thợ</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            @if($barber->avatar)
                                <img src="{{ asset('storage/' . $barber->avatar) }}" 
                                     alt="{{ $barber->name }}" 
                                     class="img-fluid rounded">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" 
                                     style="width: 150px; height: 150px; font-size: 48px;">
                                    {{ substr($barber->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-10">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Tên:</strong> {{ $barber->name }}</p>
                                    <p><strong>Chi nhánh:</strong> {{ $barber->branch->name }}</p>
                                    <p><strong>Trạng thái:</strong> 
                                        <span class="badge badge-{{ $barber->status == 'idle' ? 'success' : 'warning' }}">
                                            {{ $barber->status == 'idle' ? 'Đang làm việc' : 'Nghỉ việc' }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Tháng/Năm:</strong> {{ $selectedMonth }}/{{ $selectedYear }}</p>
                                    <p><strong>Tổng ngày nghỉ:</strong> {{ $stats['off_days']->count() + $stats['holiday_days']->count() }}</p>
                                    <p><strong>Ngày làm việc:</strong> {{ $stats['custom_days']->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Bộ lọc</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('barber_statistics.show', $barber->id) }}" class="row">
                        <div class="col-md-4">
                            <label>Tháng</label>
                            <select name="month" class="form-control">
                                @foreach($availableMonths as $month)
                                    <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                        Tháng {{ $month }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Năm</label>
                            <select name="year" class="form-control">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Lọc</button>
                                <a href="{{ route('barber_statistics.index') }}?month={{ $selectedMonth }}&year={{ $selectedYear }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

                 <!-- Tổng quan ngày nghỉ trong tháng -->
         <div class="col-md-12">
             <div class="card">
                 <div class="card-header">
                     <h4 class="card-title">Tổng quan ngày nghỉ trong tháng</h4>
                 </div>
                 <div class="card-body">
                     @if($stats['off_days']->count() > 0)
                         <div class="row">
                             @foreach($stats['off_days'] as $schedule)
                             <div class="col-md-4 mb-3">
                                 <div class="card border-warning">
                                     <div class="card-body">
                                         <h6 class="card-title text-warning">
                                             <i class="fas fa-calendar-times"></i> Ngày nghỉ
                                         </h6>
                                         <p class="card-text">
                                             <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}<br>
                                             <strong>Thứ:</strong> {{ \Carbon\Carbon::parse($schedule->schedule_date)->locale('vi')->isoFormat('dddd') }}<br>
                                             @if($schedule->note)
                                                 <strong>Ghi chú:</strong> {{ $schedule->note }}
                                             @endif
                                         </p>
                                     </div>
                                 </div>
                             </div>
                             @endforeach
                         </div>
                     @else
                         <p class="text-muted text-center">Không có ngày nghỉ trong tháng này</p>
                     @endif
                 </div>
             </div>
         </div>

         <!-- Tổng quan nghỉ lễ trong tháng -->
         <div class="col-md-12">
             <div class="card">
                 <div class="card-header">
                     <h4 class="card-title">Tổng quan nghỉ lễ trong tháng</h4>
                 </div>
                 <div class="card-body">
                     @if($stats['holiday_days']->count() > 0)
                         <div class="row">
                             @php
                                 $holidayGroups = [];
                                 foreach($stats['holiday_days'] as $schedule) {
                                     $key = $schedule->holiday_start_date . '_' . $schedule->holiday_end_date . '_' . $schedule->note;
                                     if (!isset($holidayGroups[$key])) {
                                         $holidayGroups[$key] = [
                                             'start_date' => \Carbon\Carbon::parse($schedule->holiday_start_date)->format('d/m/Y'),
                                             'end_date' => \Carbon\Carbon::parse($schedule->holiday_end_date)->format('d/m/Y'),
                                             'note' => $schedule->note,
                                             'days_count' => 0
                                         ];
                                     }
                                     $holidayGroups[$key]['days_count']++;
                                 }
                             @endphp
                             @foreach($holidayGroups as $holiday)
                             <div class="col-md-6 mb-3">
                                 <div class="card border-success">
                                     <div class="card-body">
                                         <h6 class="card-title text-success">
                                             <i class="fas fa-calendar-check"></i> Nghỉ lễ
                                         </h6>
                                         <p class="card-text">
                                             <strong>Thời gian:</strong> {{ $holiday['start_date'] }} - {{ $holiday['end_date'] }}<br>
                                             <strong>Số ngày:</strong> {{ $holiday['days_count'] }} ngày<br>
                                             @if($holiday['note'])
                                                 <strong>Ghi chú:</strong> {{ $holiday['note'] }}
                                             @endif
                                         </p>
                                     </div>
                                 </div>
                             </div>
                             @endforeach
                         </div>
                     @else
                         <p class="text-muted text-center">Không có nghỉ lễ trong tháng này</p>
                     @endif
                 </div>
             </div>
         </div>

         <!-- Tổng quan đổi ca trong tháng -->
         <div class="col-md-12">
             <div class="card">
                 <div class="card-header">
                     <h4 class="card-title">Tổng quan đổi ca trong tháng</h4>
                 </div>
                 <div class="card-body">
                     @if($stats['custom_days']->count() > 0)
                         <div class="row">
                             @foreach($stats['custom_days'] as $schedule)
                             <div class="col-md-4 mb-3">
                                 <div class="card border-info">
                                     <div class="card-body">
                                         <h6 class="card-title text-info">
                                             <i class="fas fa-exchange-alt"></i> Đổi ca
                                         </h6>
                                         <p class="card-text">
                                             <strong>Ngày:</strong> {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}<br>
                                             <strong>Thứ:</strong> {{ \Carbon\Carbon::parse($schedule->schedule_date)->locale('vi')->isoFormat('dddd') }}<br>
                                             @if($schedule->start_time && $schedule->end_time)
                                                 <strong>Thời gian:</strong> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}<br>
                                             @endif
                                             @if($schedule->note)
                                                 <strong>Ghi chú:</strong> {{ $schedule->note }}
                                             @endif
                                         </p>
                                     </div>
                                 </div>
                             </div>
                             @endforeach
                         </div>
                     @else
                         <p class="text-muted text-center">Không có đổi ca trong tháng này</p>
                     @endif
                 </div>
             </div>
         </div>

         <!-- Thống kê theo tuần -->
         <div class="col-md-12">
             <div class="card">
                 <div class="card-header">
                     <h4 class="card-title">Thống kê theo tuần</h4>
                 </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                                                         <thead>
                                 <tr>
                                     <th>Tuần</th>
                                     <th>Thời gian</th>
                                     <th class="text-center">Ngày nghỉ</th>
                                     <th>Nghỉ lễ</th>
                                     <th class="text-center">Đổi ca</th>
                                     <th class="text-center">Tổng nghỉ</th>
                                 </tr>
                             </thead>
                             <tbody>
                                 @forelse($stats['weekly_stats'] as $week)
                                 <tr>
                                     <td>Tuần {{ $week['week'] }}</td>
                                     <td>{{ $week['start_date'] }} - {{ $week['end_date'] }}</td>
                                     <td>
                                         @if($week['off_days'] > 0)
                                             <span class="badge badge-warning">{{ $week['off_days'] }} ngày</span>
                                             @if(isset($week['off_details']) && count($week['off_details']) > 0)
                                                 <div class="mt-1">
                                                     @foreach($week['off_details'] as $off)
                                                         <small class="text-muted d-block">
                                                             {{ $off['date'] }}
                                                             @if($off['note'])
                                                                 <br><em>{{ $off['note'] }}</em>
                                                             @endif
                                                         </small>
                                                     @endforeach
                                                 </div>
                                             @endif
                                         @else
                                             <span class="text-muted">-</span>
                                         @endif
                                     </td>
                                     <td>
                                         @if($week['holiday_days'] > 0)
                                             <span class="badge badge-success">{{ $week['holiday_days'] }} ngày</span>
                                             @if(isset($week['holiday_details']) && count($week['holiday_details']) > 0)
                                                 <div class="mt-1">
                                                     @foreach($week['holiday_details'] as $holiday)
                                                         <small class="text-muted d-block">
                                                             {{ $holiday['start_date'] }} - {{ $holiday['end_date'] }}
                                                             @if($holiday['note'])
                                                                 <br><em>{{ $holiday['note'] }}</em>
                                                             @endif
                                                         </small>
                                                     @endforeach
                                                 </div>
                                             @endif
                                         @else
                                             <span class="text-muted">-</span>
                                         @endif
                                     </td>
                                     <td>
                                         @if($week['custom_days'] > 0)
                                             <span class="badge badge-info">{{ $week['custom_days'] }} ngày</span>
                                             @if(isset($week['custom_details']) && count($week['custom_details']) > 0)
                                                 <div class="mt-1">
                                                     @foreach($week['custom_details'] as $custom)
                                                         <small class="text-muted d-block">
                                                             {{ $custom['date'] }}
                                                             @if($custom['start_time'] && $custom['end_time'])
                                                                 <br>{{ $custom['start_time'] }} - {{ $custom['end_time'] }}
                                                             @endif
                                                             @if($custom['note'])
                                                                 <br><em>{{ $custom['note'] }}</em>
                                                             @endif
                                                         </small>
                                                     @endforeach
                                                 </div>
                                             @endif
                                         @else
                                             <span class="text-muted">-</span>
                                         @endif
                                     </td>
                                     <td class="text-center">
                                         <span class="badge badge-danger">{{ $week['total_off'] }}</span>
                                     </td>
                                 </tr>
                                 @empty
                                 <tr>
                                     <td colspan="6" class="text-center">Không có dữ liệu</td>
                                 </tr>
                                 @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chi tiết lịch theo ngày -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chi tiết lịch theo ngày</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Ngày</th>
                                    <th>Thứ</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->schedule_date)->locale('vi')->isoFormat('dddd') }}</td>
                                                                         <td>
                                         @if($schedule->status == 'off')
                                             <span class="badge badge-warning">Nghỉ</span>
                                         @elseif($schedule->status == 'holiday')
                                             <span class="badge badge-success">Nghỉ lễ</span>
                                             @if($schedule->holiday_start_date && $schedule->holiday_end_date)
                                                 <br><small class="text-muted">
                                                     {{ \Carbon\Carbon::parse($schedule->holiday_start_date)->format('d/m/Y') }} - 
                                                     {{ \Carbon\Carbon::parse($schedule->holiday_end_date)->format('d/m/Y') }}
                                                 </small>
                                             @endif
                                         @elseif($schedule->status == 'custom')
                                             <span class="badge badge-info">Làm việc</span>
                                         @endif
                                     </td>
                                    <td>
                                        @if($schedule->status == 'custom' && $schedule->start_time && $schedule->end_time)
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                            {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $schedule->note ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tóm tắt -->
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-3">
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
                                        <h4 class="card-title">{{ $stats['off_days']->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
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
                                        <h4 class="card-title">{{ $stats['holiday_days']->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
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
                                        <h4 class="card-title">{{ $stats['custom_days']->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
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
                                        <h4 class="card-title">{{ $stats['custom_days']->count() }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 