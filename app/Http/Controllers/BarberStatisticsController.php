<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\BarberSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarberStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedMonth = $request->input('month', date('n'));
        $selectedYear = $request->input('year', date('Y'));
        $selectedBranch = $request->input('branch_id');
        
        $currentMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        
        // Lấy danh sách chi nhánh
        if ($user->role === 'admin_branch') {
            $branches = Branch::where('id', $user->branch_id)->get();
            $selectedBranch = $user->branch_id;
        } else {
            $branches = Branch::all();
        }

        // Query thống kê
        $query = Barber::with(['branch', 'schedules' => function($q) use ($currentMonth) {
            $q->whereMonth('schedule_date', $currentMonth->month)
              ->whereYear('schedule_date', $currentMonth->year);
        }]);

        // Lọc theo chi nhánh
        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        } elseif ($user->role === 'admin_branch') {
            $query->where('branch_id', $user->branch_id);
        }

        $barbers = $query->get()->map(function ($barber) use ($currentMonth) {
            $schedules = $barber->schedules;
            
            // Thống kê theo loại
            $barber->off_days = $schedules->where('status', 'off')->count();
            $barber->holiday_days = $schedules->where('status', 'holiday')->count();
            $barber->custom_days = $schedules->where('status', 'custom')->count();
            $barber->total_off = $barber->off_days + $barber->holiday_days;
            
            // Tính ngày làm việc thực tế
            $barber->working_days = $schedules->where('status', 'custom')->count();
            
            // Thống kê theo tuần
            $barber->weekly_stats = $this->getWeeklyStats($schedules, $currentMonth);
            
            return $barber;
        });

        // Thống kê tổng quan
        $totalStats = [
            'total_barbers' => $barbers->count(),
            'total_off_days' => $barbers->sum('off_days'),
            'total_holiday_days' => $barbers->sum('holiday_days'),
            'total_custom_days' => $barbers->sum('custom_days'),
            'total_working_days' => $barbers->sum('working_days'),
        ];

        // Danh sách tháng/năm
        $availableMonths = range(1, 12);
        $availableYears = range(date('Y') - 2, date('Y'));

        return view('admin.barber_statistics.index', compact(
            'barbers',
            'branches',
            'selectedMonth',
            'selectedYear',
            'selectedBranch',
            'totalStats',
            'availableMonths',
            'availableYears'
        ));
    }

    public function show(Barber $barber, Request $request)
    {
        $selectedMonth = $request->input('month', date('n'));
        $selectedYear = $request->input('year', date('Y'));
        
        $currentMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        
        // Chi tiết lịch của thợ
        $schedules = $barber->schedules()
            ->whereMonth('schedule_date', $currentMonth->month)
            ->whereYear('schedule_date', $currentMonth->year)
            ->orderBy('schedule_date')
            ->get();

        // Thống kê chi tiết
        $stats = [
            'off_days' => $schedules->where('status', 'off'),
            'holiday_days' => $schedules->where('status', 'holiday'),
            'custom_days' => $schedules->where('status', 'custom'),
            'weekly_stats' => $this->getWeeklyStats($schedules, $currentMonth),
        ];

        // Danh sách tháng/năm
        $availableMonths = range(1, 12);
        $availableYears = range(date('Y') - 2, date('Y'));

        return view('admin.barber_statistics.show', compact(
            'barber',
            'schedules',
            'stats',
            'selectedMonth',
            'selectedYear',
            'availableMonths',
            'availableYears'
        ));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $selectedMonth = $request->input('month', date('n'));
        $selectedYear = $request->input('year', date('Y'));
        $selectedBranch = $request->input('branch_id');
        
        $currentMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        
        // Query thống kê
        $query = Barber::with(['branch', 'schedules' => function($q) use ($currentMonth) {
            $q->whereMonth('schedule_date', $currentMonth->month)
              ->whereYear('schedule_date', $currentMonth->year);
        }]);

        // Lọc theo chi nhánh
        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        } elseif ($user->role === 'admin_branch') {
            $query->where('branch_id', $user->branch_id);
        }

        $barbers = $query->get()->map(function ($barber) use ($currentMonth) {
            $schedules = $barber->schedules;
            $holidaySchedules = $schedules->where('status', 'holiday');
            $offSchedules = $schedules->where('status', 'off');
            $customSchedules = $schedules->where('status', 'custom');
            
            // Thống kê nghỉ lễ chi tiết
            $holidayDetails = $this->getHolidayDetails($holidaySchedules);
            $holidayInfo = '';
            if (!empty($holidayDetails)) {
                foreach ($holidayDetails as $holiday) {
                    $holidayInfo .= $holiday['start_date'] . ' - ' . $holiday['end_date'];
                    if ($holiday['note']) {
                        $holidayInfo .= ' (' . $holiday['note'] . ')';
                    }
                    $holidayInfo .= '; ';
                }
                $holidayInfo = rtrim($holidayInfo, '; ');
            }
            
            // Thống kê ngày nghỉ chi tiết
            $offDetails = $this->getOffDetails($offSchedules);
            $offInfo = '';
            if (!empty($offDetails)) {
                foreach ($offDetails as $off) {
                    $offInfo .= $off['date'];
                    if ($off['note']) {
                        $offInfo .= ' (' . $off['note'] . ')';
                    }
                    $offInfo .= '; ';
                }
                $offInfo = rtrim($offInfo, '; ');
            }
            
            // Thống kê đổi ca chi tiết
            $customDetails = $this->getCustomDetails($customSchedules);
            $customInfo = '';
            if (!empty($customDetails)) {
                foreach ($customDetails as $custom) {
                    $customInfo .= $custom['date'];
                    if ($custom['start_time'] && $custom['end_time']) {
                        $customInfo .= ' (' . $custom['start_time'] . ' - ' . $custom['end_time'] . ')';
                    }
                    if ($custom['note']) {
                        $customInfo .= ' - ' . $custom['note'];
                    }
                    $customInfo .= '; ';
                }
                $customInfo = rtrim($customInfo, '; ');
            }
            
            return [
                'Tên thợ' => $barber->name,
                'Chi nhánh' => $barber->branch->name,
                'Ngày nghỉ (số ngày)' => $schedules->where('status', 'off')->count(),
                'Chi tiết ngày nghỉ' => $offInfo ?: 'Không có',
                'Nghỉ lễ (số ngày)' => $schedules->where('status', 'holiday')->count(),
                'Chi tiết nghỉ lễ' => $holidayInfo ?: 'Không có',
                'Đổi ca (số ngày)' => $schedules->where('status', 'custom')->count(),
                'Chi tiết đổi ca' => $customInfo ?: 'Không có',
                'Tổng ngày nghỉ' => $schedules->whereIn('status', ['off', 'holiday'])->count(),
                'Ngày làm việc' => $schedules->where('status', 'custom')->count(),
            ];
        });

        $filename = "thong_ke_tho_{$selectedMonth}_{$selectedYear}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($barbers) {
            $file = fopen('php://output', 'w');
            
            // Header
            if ($barbers->count() > 0) {
                $firstBarber = $barbers->first();
                fputcsv($file, array_keys($firstBarber));
                
                // Data
                foreach ($barbers as $barber) {
                    fputcsv($file, $barber);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getWeeklyStats($schedules, $currentMonth)
    {
        $weeks = [];
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        $currentWeek = $startOfMonth->copy();
        
        while ($currentWeek <= $endOfMonth) {
            $weekStart = $currentWeek->copy()->startOfWeek();
            $weekEnd = $currentWeek->copy()->endOfWeek();
            
            $weekSchedules = $schedules->filter(function($schedule) use ($weekStart, $weekEnd) {
                $scheduleDate = Carbon::parse($schedule->schedule_date);
                return $scheduleDate->between($weekStart, $weekEnd);
            });
            
            // Thống kê nghỉ lễ chi tiết
            $holidayDetails = $this->getHolidayDetails($weekSchedules->where('status', 'holiday'));
            
            // Thống kê ngày nghỉ chi tiết
            $offDetails = $this->getOffDetails($weekSchedules->where('status', 'off'));
            
            // Thống kê đổi ca chi tiết
            $customDetails = $this->getCustomDetails($weekSchedules->where('status', 'custom'));
            
            $weeks[] = [
                'week' => $currentWeek->format('W'),
                'start_date' => $weekStart->format('d/m/Y'),
                'end_date' => $weekEnd->format('d/m/Y'),
                'off_days' => $weekSchedules->where('status', 'off')->count(),
                'off_details' => $offDetails,
                'holiday_days' => $weekSchedules->where('status', 'holiday')->count(),
                'holiday_details' => $holidayDetails,
                'custom_days' => $weekSchedules->where('status', 'custom')->count(),
                'custom_details' => $customDetails,
                'total_off' => $weekSchedules->whereIn('status', ['off', 'holiday'])->count(),
            ];
            
            $currentWeek->addWeek();
        }
        
        return $weeks;
    }

    private function getOffDetails($offSchedules)
    {
        $offDetails = [];
        
        foreach ($offSchedules as $schedule) {
            $offDetails[] = [
                'date' => Carbon::parse($schedule->schedule_date)->format('d/m/Y'),
                'note' => $schedule->note,
            ];
        }
        
        return $offDetails;
    }

    private function getCustomDetails($customSchedules)
    {
        $customDetails = [];
        
        foreach ($customSchedules as $schedule) {
            $customDetails[] = [
                'date' => Carbon::parse($schedule->schedule_date)->format('d/m/Y'),
                'start_time' => $schedule->start_time ? Carbon::parse($schedule->start_time)->format('H:i') : null,
                'end_time' => $schedule->end_time ? Carbon::parse($schedule->end_time)->format('H:i') : null,
                'note' => $schedule->note,
            ];
        }
        
        return $customDetails;
    }

    private function getHolidayDetails($holidaySchedules)
    {
        $holidayGroups = [];
        
        foreach ($holidaySchedules as $schedule) {
            $key = $schedule->holiday_start_date . '_' . $schedule->holiday_end_date . '_' . $schedule->note;
            
            if (!isset($holidayGroups[$key])) {
                $holidayGroups[$key] = [
                    'start_date' => Carbon::parse($schedule->holiday_start_date)->format('d/m/Y'),
                    'end_date' => Carbon::parse($schedule->holiday_end_date)->format('d/m/Y'),
                    'note' => $schedule->note,
                    'days_count' => 0
                ];
            }
            
            $holidayGroups[$key]['days_count']++;
        }
        
        return array_values($holidayGroups);
    }
} 