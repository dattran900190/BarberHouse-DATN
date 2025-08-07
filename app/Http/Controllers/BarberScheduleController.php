<?php

namespace App\Http\Controllers;

use App\Models\{Branch, Barber, BarberSchedule};
use App\Http\Requests\BarberSchedulesRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Auth;

class BarberScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $user = Auth::user();

        // Nếu là admin_branch, chỉ lấy chi nhánh của chính họ (không cần phân trang vì chỉ 1 chi nhánh)
        if ($user->role === 'admin_branch') {
            $branches = Branch::where('id', $user->branch_id)
                ->when($search, function ($query) use ($search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10); // vẫn dùng paginate để giữ cấu trúc
        } else {
            // Nếu là admin thường, lấy tất cả chi nhánh có phân trang
            $branches = Branch::when($search, function ($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%");
            })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        $holidays = BarberSchedule::where('status', 'holiday')
            ->select('holiday_start_date', 'holiday_end_date', 'note')
            ->groupBy('holiday_start_date', 'holiday_end_date', 'note')
            ->orderBy('holiday_start_date')
            ->get();

        return view('admin.barber_schedules.index', compact('branches', 'search', 'holidays'));
    }

    public function show($id)
    {

        return redirect()->route('admin.barber_schedules.index')
            ->with('error', 'Trang này không tồn tại hoặc không được hỗ trợ.');
    }

    public function showBranch($branchId)
    {
        $branch = Branch::with(['barbers.schedules'])->findOrFail($branchId);
        $barbers = $branch->barbers;
        $barbers = $branch->barbers()->paginate(10); // 10 thợ mỗi trang
        //phân trang lịch trình thợ 
        foreach ($barbers as $barber) {
            $barber->schedules = $barber->schedules()->paginate(3);
        }
        return view('admin.barber_schedules.show', compact('branch', 'barbers'));
    }

    public function create($branchId = null)
    {
        $branch = $branchId ? Branch::findOrFail($branchId) : null;
        $barbers = $branch ? $branch->barbers()->whereNotNull('branch_id')->get() : Barber::whereNotNull('branch_id')->get();

        return view('admin.barber_schedules.create', compact('barbers', 'branch'));
    }

    public function createHoliday()
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barber_schedules.index')->with('error', 'Bạn không có quyền thêm lịch nghỉ lễ.');
        }
        return view('admin.barber_schedules.create_holiday');
    }

    public function storeHoliday(BarberSchedulesRequest $request)
    {
        $data = $request->validated();

        $start = $data['holiday_start_date'];
        $end = $data['holiday_end_date'];
        $note = $data['note'];

        $period = CarbonPeriod::create($start, $end);

        $branches = Branch::with('barbers')->get();

        foreach ($branches as $branch) {
            foreach ($branch->barbers as $barber) {
                foreach ($period as $date) {
                    $scheduleDate = $date->format('Y-m-d');

                    // ❗ Chỉ tạo nếu chưa tồn tại
                    $exists = BarberSchedule::where('barber_id', $barber->id)
                        ->where('schedule_date', $scheduleDate)
                        ->where('status', 'holiday')
                        ->where('note', $note)
                        ->exists();

                    if (!$exists) {
                        BarberSchedule::create([
                            'barber_id' => $barber->id,
                            'branch_id' => $barber->branch_id,
                            'schedule_date' => $scheduleDate,
                            'holiday_start_date' => $start,
                            'holiday_end_date' => $end,
                            'status' => 'holiday',
                            'is_available' => false,
                            'note' => $note,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('barber_schedules.index')->with('success', 'Tạo lịch nghỉ lễ thành công!');
    }

    public function editHoliday($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barber_schedules.index')->with('error', 'Bạn không có quyền sửa lịch nghỉ lễ.');
        }
        $schedule = BarberSchedule::findOrFail($id);

        $holiday = [
            'holiday_start_date' => $schedule->holiday_start_date,
            'holiday_end_date' => $schedule->holiday_end_date,
            'note' => $schedule->note,
        ];

        return view('admin.barber_schedules.edit_holiday', compact('holiday', 'id'));
    }

    public function updateHoliday(BarberSchedulesRequest $request, $id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('barber_schedules.index')->with('error', 'Bạn không có quyền sửa lịch nghỉ lễ.');
        }
        $data = $request->validated();

        $schedule = BarberSchedule::findOrFail($id);

        // Xoá toàn bộ lịch nghỉ theo start, end, note cũ
        BarberSchedule::where('status', 'holiday')
            ->where('holiday_start_date', $schedule->holiday_start_date)
            ->where('holiday_end_date', $schedule->holiday_end_date)
            ->where('note', $schedule->note)
            ->delete();

        // Tạo lại toàn bộ lịch mới
        $period = CarbonPeriod::create($data['holiday_start_date'], $data['holiday_end_date']);
        $barbers = Barber::all();

        foreach ($barbers as $barber) {
            foreach ($period as $date) {
                BarberSchedule::create([
                    'barber_id' => $barber->id,
                    'branch_id' => $barber->branch_id,
                    'schedule_date' => $date->format('Y-m-d'),
                    'holiday_start_date' => $data['holiday_start_date'],
                    'holiday_end_date' => $data['holiday_end_date'],
                    'status' => 'holiday',
                    'is_available' => false,
                    'note' => $data['note'],
                ]);
            }
        }

        return redirect()->route('barber_schedules.index')->with('success', 'Cập nhật lịch nghỉ lễ thành công!');
    }

    public function deleteHoliday($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá lịch nghỉ lễ.'
            ], 403);
        }

        $schedule = BarberSchedule::findOrFail($id);

        // Lấy danh sách barber_id bị ảnh hưởng
        $affectedBarbers = BarberSchedule::where('status', 'holiday')
            ->where('holiday_start_date', $schedule->holiday_start_date)
            ->where('holiday_end_date', $schedule->holiday_end_date)
            ->where('note', $schedule->note)
            ->pluck('barber_id')
            ->unique()
            ->toArray();

        // Xoá toàn bộ lịch nghỉ lễ giống nhau
        BarberSchedule::where('status', 'holiday')
            ->where('holiday_start_date', $schedule->holiday_start_date)
            ->where('holiday_end_date', $schedule->holiday_end_date)
            ->where('note', $schedule->note)
            ->delete();

        // Kiểm tra từng thợ xem còn lịch nghỉ nào không → nếu không còn thì cập nhật status về idle
        foreach ($affectedBarbers as $barberId) {
            $stillHasHoliday = BarberSchedule::where('barber_id', $barberId)
                ->where('status', 'holiday')
                ->exists();

            if (!$stillHasHoliday) {
                Barber::where('id', $barberId)->update(['status' => 'idle']);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá lịch nghỉ lễ thành công!'
        ]);
    }


    /**
     * Kiểm tra trùng lịch cho thợ trong ngày cụ thể
     */
    private function checkScheduleConflict($barberId, $scheduleDate, $status, $startTime = null, $endTime = null, $excludeId = null)
    {
        $query = BarberSchedule::where('barber_id', $barberId)
            ->where('schedule_date', $scheduleDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existingSchedules = $query->get();

        foreach ($existingSchedules as $existing) {
            // Nếu có lịch nghỉ cả ngày hoặc nghỉ lễ trong ngày đó
            if (in_array($existing->status, ['off', 'holiday'])) {
                return [
                    'conflict' => true,
                    'message' => $existing->status === 'off'
                        ? 'Thợ đã có lịch nghỉ cả ngày trong ngày này.'
                        : 'Ngày này là lịch nghỉ lễ.'
                ];
            }

            // Nếu đang tạo lịch nghỉ cả ngày nhưng đã có lịch khác
            if ($status === 'off') {
                return [
                    'conflict' => true,
                    'message' => 'Không thể tạo lịch nghỉ cả ngày vì thợ đã có lịch làm việc trong ngày này.'
                ];
            }

            // Kiểm tra trùng giờ cho lịch custom
            if ($status === 'custom' && $existing->status === 'custom' && $existing->start_time && $existing->end_time) {
                $existingStart = Carbon::parse($existing->start_time);
                $existingEnd = Carbon::parse($existing->end_time);
                $newStart = Carbon::parse($startTime);
                $newEnd = Carbon::parse($endTime);

                // Kiểm tra overlap
                if ($newStart->lt($existingEnd) && $newEnd->gt($existingStart)) {
                    return [
                        'conflict' => true,
                        'message' => "Thời gian bị trùng với lịch hiện tại ({$existing->start_time} - {$existing->end_time})."
                    ];
                }
            }
        }

        return ['conflict' => false];
    }

    public function store(BarberSchedulesRequest $request)
    {
        $data = $request->validated();

        if ($data['status'] === 'holiday') {
            return $this->storeHoliday($request);
        }

        if ($data['status'] === 'off') {
            $data['start_time'] = null;
            $data['end_time'] = null;

            // 👉 Cập nhật trạng thái thợ là 'on_leave'
            Barber::where('id', $data['barber_id'])->update(['status' => 'on_leave']);
        }

        // ❗ Kiểm tra trùng lịch trong cùng ngày (với bất kỳ status nào)
        $exists = BarberSchedule::where('barber_id', $data['barber_id'])
            ->where('schedule_date', $data['schedule_date'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Đã tồn tại lịch làm việc cho thợ này trong ngày đã chọn.'])->withInput();
        }

        // Nếu là custom, tiếp tục kiểm tra trùng giờ
        if ($data['status'] === 'custom') {
            $exists = BarberSchedule::where('barber_id', $data['barber_id'])
                ->where('schedule_date', $data['schedule_date'])
                ->where(function ($query) use ($data) {
                    $query->where(function ($q) use ($data) {
                        $q->where('start_time', '<=', $data['start_time'])
                            ->where('end_time', '>', $data['start_time']);
                    })->orWhere(function ($q) use ($data) {
                        $q->where('start_time', '<', $data['end_time'])
                            ->where('end_time', '>=', $data['end_time']);
                    })->orWhere(function ($q) use ($data) {
                        $q->where('start_time', '>=', $data['start_time'])
                            ->where('end_time', '<=', $data['end_time']);
                    });
                })->exists();

            if ($exists) {
                return back()->withErrors(['msg' => 'Thời gian bị trùng với lịch hiện tại của thợ.'])->withInput();
            }
        }

        $barber = Barber::findOrFail($data['barber_id']);
        $data['branch_id'] = $barber->branch_id;

        BarberSchedule::create($data);

        return redirect()->route('barber_schedules.showBranch', $barber->branch_id)
            ->with('success', 'Thêm lịch thành công!');
    }


    public function edit($id)
    {
        $schedule = BarberSchedule::findOrFail($id);
        $branch = $schedule->barber->branch;
        $barbers = $branch->barbers;

        // Sửa tại đây
        if ($schedule->schedule_date) {
            $schedule->schedule_date = Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        }

        return view('admin.barber_schedules.edit', compact('schedule', 'branch', 'barbers'));
    }

    public function update(BarberSchedulesRequest $request, $id)
    {
        $data = $request->validated();
        if ($data['status'] !== 'custom') {
            $exists = BarberSchedule::where('barber_id', $data['barber_id'])
                ->where('schedule_date', $data['schedule_date'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['msg' => 'Đã tồn tại lịch khác trong ngày này.'])->withInput();
            }
        }

        // Kiểm tra trùng lịch (loại trừ record hiện tại)
        $conflictCheck = $this->checkScheduleConflict(
            $data['barber_id'],
            $data['schedule_date'],
            $data['status'],
            $data['start_time'] ?? null,
            $data['end_time'] ?? null,
            $id // loại trừ record hiện tại
        );

        if ($conflictCheck['conflict']) {
            return back()->withErrors(['msg' => $conflictCheck['message']])->withInput();
        }

        // Xử lý dữ liệu theo status
        if ($data['status'] === 'off') {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        $schedule = BarberSchedule::findOrFail($id);
        $schedule->update($data);

        return redirect()->route('barber_schedules.showBranch', $schedule->barber->branch_id)
            ->with('success', 'Cập nhật lịch thành công!');
    }

    public function destroy($id)
    {
        try {
            $schedule = BarberSchedule::findOrFail($id);
            $schedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa lịch làm việc thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
