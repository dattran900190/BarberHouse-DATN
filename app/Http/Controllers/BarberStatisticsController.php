<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barber;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\BarberSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

class BarberStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $currentYear = date('Y'); // 2025
        $currentMonthNumber = date('n'); // 8
        $selectedYear = $request->input('year', $currentYear);
        $selectedMonth = $request->input('month', $currentMonthNumber);
        $selectedBranch = $request->input('branch_id');

        // Nếu năm hiện tại và tháng vượt quá tháng hiện tại, điều chỉnh về tháng hiện tại
        if ($selectedYear == $currentYear && $selectedMonth != 'all' && $selectedMonth > $currentMonthNumber) {
            $selectedMonth = $currentMonthNumber;
        }

        // Xác định phạm vi tính toán (tháng hay năm)
        $isYearly = $selectedMonth == 'all';
        $currentMonth = Carbon::createFromDate($selectedYear, $isYearly ? 1 : $selectedMonth, 1);

        // Tính số ngày trong khoảng thời gian
        $daysInPeriod = $isYearly ? (
            $selectedYear == $currentYear
            ? Carbon::createFromDate($selectedYear, 1, 1)->startOfDay()->diffInDays(
                Carbon::createFromDate($selectedYear, $currentMonthNumber, 1)->endOfMonth()->endOfDay(),
                false
            ) + 1
            : (Carbon::createFromDate($selectedYear, 1, 1)->isLeapYear() ? 366 : 365)
        ) : $currentMonth->daysInMonth;

        // Đảm bảo $daysInPeriod là số nguyên
        $daysInPeriod = (int) $daysInPeriod;

        // Lấy danh sách chi nhánh
        if ($user->role === 'admin_branch') {
            $branches = Branch::where('id', $user->branch_id)->get();
            $selectedBranch = $user->branch_id;
        } else {
            $branches = Branch::all();
        }

        // Query thống kê
        $query = Barber::with(['branch', 'schedules' => function ($q) use ($selectedYear, $isYearly, $selectedMonth, $currentYear, $currentMonthNumber) {
            if ($isYearly) {
                if ($selectedYear == $currentYear) {
                    $q->whereBetween('schedule_date', [
                        Carbon::createFromDate($selectedYear, 1, 1)->startOfDay(),
                        Carbon::createFromDate($selectedYear, $currentMonthNumber, 1)->endOfMonth()->endOfDay()
                    ]);
                } else {
                    $q->whereYear('schedule_date', $selectedYear);
                }
            } else {
                $q->whereMonth('schedule_date', $selectedMonth)
                    ->whereYear('schedule_date', $selectedYear);
            }
        }]);

        // Lọc theo chi nhánh
        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        } elseif ($user->role === 'admin_branch') {
            $query->where('branch_id', $user->branch_id);
        }

        // Lọc chỉ lấy thợ không có trạng thái 'retired'
        $query->where('status', '!=', 'retired');

        // Xử lý tìm kiếm theo tên thợ
        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Phân trang (10 bản ghi/trang)
        $barbers = $query->paginate(10);

        // Xử lý thống kê cho từng thợ
        $barbers->getCollection()->transform(function ($barber) use ($isYearly, $daysInPeriod, $selectedYear, $selectedMonth) {
            $schedules = $barber->schedules;

            // Thống kê theo loại
            $barber->off_days = $schedules->where('status', 'off')->count();
            $barber->holiday_days = $schedules->where('status', 'holiday')->count();
            $barber->custom_days = $schedules->where('status', 'custom')->count();
            $barber->total_off = $barber->off_days + $barber->holiday_days;

            // Tính ngày làm việc, đảm bảo không âm và là số nguyên
            $barber->working_days = max(0, (int) ($daysInPeriod - ($barber->off_days + $barber->holiday_days + $barber->custom_days)));

            // Thống kê theo tuần (có thể cần điều chỉnh nếu tính cho cả năm)
            $barber->weekly_stats = $this->getWeeklyStats($schedules, Carbon::createFromDate($selectedYear, $isYearly ? 1 : $selectedMonth, 1));

            return $barber;
        });

        // Thống kê tổng quan
        $totalStats = [
            'total_barbers' => $barbers->total(),
            'total_off_days' => $barbers->sum('off_days'),
            'total_holiday_days' => $barbers->sum('holiday_days'),
            'total_custom_days' => $barbers->sum('custom_days'),
            'total_working_days' => $barbers->sum('working_days'),
        ];

        // Danh sách tháng/năm
        $availableMonths = $selectedYear == $currentYear ? range(1, $currentMonthNumber) : range(1, 12);
        $availableYears = range($currentYear - 2, $currentYear);

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
        $currentYear = date('Y'); // 2025
        $currentMonthNumber = date('n'); // 8
        $selectedYear = $request->input('year', $currentYear);
        $selectedMonth = $request->input('month', $currentMonthNumber);
        $startDate = $request->input('start_date'); // Thêm bộ lọc ngày bắt đầu
        $endDate = $request->input('end_date'); // Thêm bộ lọc ngày kết thúc
        $perPage = 6; // Số bản ghi mỗi trang
    
        // Nếu năm hiện tại và tháng vượt quá tháng hiện tại, điều chỉnh về tháng hiện tại
        if ($selectedYear == $currentYear && $selectedMonth != 'all' && $selectedMonth > $currentMonthNumber) {
            $selectedMonth = $currentMonthNumber;
        }
    
        // Xác định phạm vi tính toán (tháng hay năm)
        $isYearly = $selectedMonth == 'all';
        $currentMonth = Carbon::createFromDate($selectedYear, $isYearly ? 1 : $selectedMonth, 1);
    
        // Tính số ngày trong khoảng thời gian
        $daysInPeriod = $isYearly ? (
            $selectedYear == $currentYear
                ? Carbon::createFromDate($selectedYear, 1, 1)->startOfDay()->diffInDays(
                    Carbon::createFromDate($selectedYear, $currentMonthNumber, 1)->endOfMonth()->endOfDay(),
                    false
                ) + 1
                : (Carbon::createFromDate($selectedYear, 1, 1)->isLeapYear() ? 366 : 365)
        ) : $currentMonth->daysInMonth;
    
        // Nếu có bộ lọc ngày, điều chỉnh $daysInPeriod
        if ($startDate && $endDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                if ($start->lte($end)) {
                    $daysInPeriod = $start->diffInDays($end) + 1;
                }
            } catch (\Exception $e) {
                $startDate = null;
                $endDate = null;
            }
        }
    
        // Đảm bảo $daysInPeriod là số nguyên
        $daysInPeriod = (int) $daysInPeriod;
    
        // Chi tiết lịch của thợ
        $schedulesQuery = $barber->schedules()
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('schedule_date', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ]);
            }, function ($query) use ($isYearly, $selectedYear, $currentYear, $currentMonthNumber, $currentMonth) {
                if ($isYearly) {
                    if ($selectedYear == $currentYear) {
                        $query->whereBetween('schedule_date', [
                            Carbon::createFromDate($selectedYear, 1, 1)->startOfDay(),
                            Carbon::createFromDate($selectedYear, $currentMonthNumber, 1)->endOfMonth()->endOfDay()
                        ]);
                    } else {
                        $query->whereYear('schedule_date', $selectedYear);
                    }
                } else {
                    $query->whereMonth('schedule_date', $currentMonth->month)
                          ->whereYear('schedule_date', $currentMonth->year);
                }
            })
            ->orderBy('schedule_date');
    
        // Lấy tất cả schedules để tính tổng
        $schedules = $schedulesQuery->get();
    
        // Lấy trang hiện tại cho từng loại
        $offPage = $request->input('off_page', 1);
        $holidayPage = $request->input('holiday_page', 1);
        $customPage = $request->input('custom_page', 1);
    
        // Phân trang cho từng loại
        $offDays = $schedules->where('status', 'off');
        $holidayDays = $schedules->where('status', 'holiday');
        $customDays = $schedules->where('status', 'custom');
    
        $stats = [
            'off_days' => $offDays->forPage($offPage, $perPage),
            'off_days_count' => $offDays->count(),
            'off_days_paginator' => new \Illuminate\Pagination\LengthAwarePaginator(
                $offDays->forPage($offPage, $perPage),
                $offDays->count(),
                $perPage,
                $offPage,
                ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'off_page']
            ),
            'holiday_days' => $holidayDays->forPage($holidayPage, $perPage),
            'holiday_days_count' => $holidayDays->count(),
            'holiday_days_paginator' => new \Illuminate\Pagination\LengthAwarePaginator(
                $holidayDays->forPage($holidayPage, $perPage),
                $holidayDays->count(),
                $perPage,
                $holidayPage,
                ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'holiday_page']
            ),
            'custom_days' => $customDays->forPage($customPage, $perPage),
            'custom_days_count' => $customDays->count(),
            'custom_days_paginator' => new \Illuminate\Pagination\LengthAwarePaginator(
                $customDays->forPage($customPage, $perPage),
                $customDays->count(),
                $perPage,
                $customPage,
                ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'custom_page']
            ),
            'working_days' => max(0, (int) ($daysInPeriod - (
                $offDays->count() +
                $holidayDays->count() +
                $customDays->count()
            ))),
            'weekly_stats' => $this->getWeeklyStats($schedules, $currentMonth),
        ];
    
        // Danh sách tháng/năm
        $availableMonths = $selectedYear == $currentYear ? range(1, $currentMonthNumber) : range(1, 12);
        $availableYears = range($currentYear - 2, $currentYear);
    
        return view('admin.barber_statistics.show', compact(
            'barber',
            'schedules',
            'stats',
            'selectedMonth',
            'selectedYear',
            'startDate',
            'endDate',
            'availableMonths',
            'availableYears',
            'offPage',
            'holidayPage',
            'customPage'
        ));
    }

    // public function export(Request $request)
    // {
    //     $user = Auth::user();
    //     $selectedMonth = $request->input('month', date('n'));
    //     $selectedYear = $request->input('year', date('Y'));
    //     $selectedBranch = $request->input('branch_id');

    //     $currentMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1);

    //     // Query thống kê
    //     $query = Barber::with(['branch', 'schedules' => function($q) use ($currentMonth) {
    //         $q->whereMonth('schedule_date', $currentMonth->month)
    //           ->whereYear('schedule_date', $currentMonth->year);
    //     }]);

    //     // Lọc theo chi nhánh
    //     if ($selectedBranch) {
    //         $query->where('branch_id', $selectedBranch);
    //     } elseif ($user->role === 'admin_branch') {
    //         $query->where('branch_id', $user->branch_id);
    //     }

    //     $barbers = $query->get()->map(function ($barber) use ($currentMonth) {
    //         $schedules = $barber->schedules;
    //         $holidaySchedules = $schedules->where('status', 'holiday');
    //         $offSchedules = $schedules->where('status', 'off');
    //         $customSchedules = $schedules->where('status', 'custom');

    //         // Thống kê nghỉ lễ chi tiết
    //         $holidayDetails = $this->getHolidayDetails($holidaySchedules);
    //         $holidayInfo = '';
    //         if (!empty($holidayDetails)) {
    //             foreach ($holidayDetails as $holiday) {
    //                 $holidayInfo .= $holiday['start_date'] . ' - ' . $holiday['end_date'];
    //                 if ($holiday['note']) {
    //                     $holidayInfo .= ' (' . $holiday['note'] . ')';
    //                 }
    //                 $holidayInfo .= '; ';
    //             }
    //             $holidayInfo = rtrim($holidayInfo, '; ');
    //         }

    //         // Thống kê ngày nghỉ chi tiết
    //         $offDetails = $this->getOffDetails($offSchedules);
    //         $offInfo = '';
    //         if (!empty($offDetails)) {
    //             foreach ($offDetails as $off) {
    //                 $offInfo .= $off['date'];
    //                 if ($off['note']) {
    //                     $offInfo .= ' (' . $off['note'] . ')';
    //                 }
    //                 $offInfo .= '; ';
    //             }
    //             $offInfo = rtrim($offInfo, '; ');
    //         }

    //         // Thống kê đổi ca chi tiết
    //         $customDetails = $this->getCustomDetails($customSchedules);
    //         $customInfo = '';
    //         if (!empty($customDetails)) {
    //             foreach ($customDetails as $custom) {
    //                 $customInfo .= $custom['date'];
    //                 if ($custom['start_time'] && $custom['end_time']) {
    //                     $customInfo .= ' (' . $custom['start_time'] . ' - ' . $custom['end_time'] . ')';
    //                 }
    //                 if ($custom['note']) {
    //                     $customInfo .= ' - ' . $custom['note'];
    //                 }
    //                 $customInfo .= '; ';
    //             }
    //             $customInfo = rtrim($customInfo, '; ');
    //         }

    //         return [
    //             'Tên thợ' => $barber->name,
    //             'Chi nhánh' => $barber->branch->name,
    //             'Ngày nghỉ (số ngày)' => $schedules->where('status', 'off')->count(),
    //             'Chi tiết ngày nghỉ' => $offInfo ?: 'Không có',
    //             'Nghỉ lễ (số ngày)' => $schedules->where('status', 'holiday')->count(),
    //             'Chi tiết nghỉ lễ' => $holidayInfo ?: 'Không có',
    //             'Đổi ca (số ngày)' => $schedules->where('status', 'custom')->count(),
    //             'Chi tiết đổi ca' => $customInfo ?: 'Không có',
    //             'Tổng ngày nghỉ' => $schedules->whereIn('status', ['off', 'holiday'])->count(),
    //             'Ngày làm việc' => $schedules->where('status', 'custom')->count(),
    //         ];
    //     });

    //     $filename = "thong_ke_tho_{$selectedMonth}_{$selectedYear}.csv";

    //     $headers = [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => "attachment; filename={$filename}",
    //     ];

    //     $callback = function() use ($barbers) {
    //         $file = fopen('php://output', 'w');

    //         // Header
    //         if ($barbers->count() > 0) {
    //             $firstBarber = $barbers->first();
    //             fputcsv($file, array_keys($firstBarber));

    //             // Data
    //             foreach ($barbers as $barber) {
    //                 fputcsv($file, $barber);
    //             }
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    private function getWeeklyStats($schedules, $currentMonth)
    {
        $weeks = [];
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $currentWeek = $startOfMonth->copy();

        while ($currentWeek <= $endOfMonth) {
            $weekStart = $currentWeek->copy()->startOfWeek();
            $weekEnd = $currentWeek->copy()->endOfWeek();

            $weekSchedules = $schedules->filter(function ($schedule) use ($weekStart, $weekEnd) {
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
