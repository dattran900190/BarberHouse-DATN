<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Barber;
use App\Models\BarberSchedule;
use App\Http\Requests\BarberSchedulesRequest;
use Illuminate\Http\Request;

class BarberScheduleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $branchesQuery = Branch::query();

        if ($search) {
            $branchesQuery->where('name', 'like', '%' . $search . '%');
        }

        $branches = $branchesQuery->get();

        return view('admin.barber_schedules.index', compact('branches', 'search'));
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

        return view('admin.barber_schedules.show', compact('branch', 'barbers'));
    }

    public function create($branchId = null)
    {
        if ($branchId) {
            $branch = Branch::findOrFail($branchId);
            $barbers = $branch->barbers()->whereNotNull('branch_id')->get();
        } else {
            $barbers = Barber::whereNotNull('branch_id')->get();
            $branch = null;
        }

        return view('admin.barber_schedules.create', compact('barbers', 'branch'));
    }

    public function store(BarberSchedulesRequest $request)
    {
        $data = $request->validated();

        // Nếu là nghỉ cả ngày thì xóa giờ
        if ($data['status'] === 'off') {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        // Kiểm tra trùng lịch — chỉ khi có giờ
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

        BarberSchedule::create($data);

        $barber = Barber::find($data['barber_id']);
        return redirect()->route('barber_schedules.showBranch', $barber->branch_id)
            ->with('success', 'Thêm lịch thành công!');
    }

    public function edit($id)
    {
        $schedule = BarberSchedule::findOrFail($id);
        $branch = $schedule->barber->branch;
        $barbers = $branch->barbers;

        // Format ngày về Y-m-d cho input date
        if ($schedule->schedule_date) {
            $schedule->schedule_date = \Carbon\Carbon::parse($schedule->schedule_date)->format('Y-m-d');
        }

        return view('admin.barber_schedules.edit', compact('schedule', 'branch', 'barbers'));
    }

    public function update(BarberSchedulesRequest $request, $id)
    {
        $data = $request->validated();

        if ($data['status'] === 'off') {
            $data['start_time'] = null;
            $data['end_time'] = null;
        }

        if ($data['status'] === 'custom') {
            $exists = BarberSchedule::where('barber_id', $data['barber_id'])
                ->where('schedule_date', $data['schedule_date'])
                ->where('id', '!=', $id)
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

        $schedule = BarberSchedule::findOrFail($id);
        $schedule->update($data);

        return redirect()->route('barber_schedules.showBranch', $schedule->barber->branch_id)
            ->with('success', 'Cập nhật lịch thành công!');
    }

    public function destroy($id)
    {
        $schedule = BarberSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Hủy lịch thành công!');
    }
}
