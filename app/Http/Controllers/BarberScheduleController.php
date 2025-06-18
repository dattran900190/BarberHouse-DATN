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

    // Cập nhật method create để nhận branchId
    public function create($branchId = null)
    {
        if ($branchId) {
            // Nếu có branchId, chỉ lấy thợ của chi nhánh đó
            $branch = Branch::findOrFail($branchId);
            $barbers = $branch->barbers()->whereNotNull('branch_id')->get();
        } else {
            // Nếu không có branchId, lấy tất cả thợ (trường hợp truy cập trực tiếp)
            $barbers = Barber::whereNotNull('branch_id')->get();
            $branch = null;
        }

        return view('admin.barber_schedules.create', compact('barbers', 'branch'));
    }

    public function store(BarberSchedulesRequest $request)
    {
        $data = $request->validated();

        // Kiểm tra trùng lịch - logic cải thiện
        $exists = BarberSchedule::where('barber_id', $data['barber_id'])
            ->where('schedule_date', $data['schedule_date'])
            ->where(function ($query) use ($data) {
                // Kiểm tra overlap: lịch mới có bị trùng với lịch cũ không
                $query->where(function ($q) use ($data) {
                    // Trường hợp 1: start_time mới nằm trong khoảng thời gian cũ
                    $q->where('start_time', '<=', $data['start_time'])
                        ->where('end_time', '>', $data['start_time']);
                })->orWhere(function ($q) use ($data) {
                    // Trường hợp 2: end_time mới nằm trong khoảng thời gian cũ  
                    $q->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function ($q) use ($data) {
                    // Trường hợp 3: lịch mới bao trùm lịch cũ
                    $q->where('start_time', '>=', $data['start_time'])
                        ->where('end_time', '<=', $data['end_time']);
                });
            })->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Thời gian đã bị trùng lịch với thợ cắt tóc này!'])->withInput();
        }

        BarberSchedule::create($data);

        $barber = Barber::find($data['barber_id']);
        return redirect()->route('barber_schedules.showBranch', $barber->branch_id)
            ->with('success', 'Thêm lịch thành công!');
    }

    public function edit($id)
    {
        $schedule = BarberSchedule::findOrFail($id);
        $branch = $schedule->barber->branch; // lấy chi nhánh của thợ này
        $barbers = $branch->barbers; // danh sách thợ cùng chi nhánh để chọn

        return view('admin.barber_schedules.edit', compact('schedule', 'branch', 'barbers'));
    }

    public function update(BarberSchedulesRequest $request, $id)
    {
        $data = $request->validated();

        // Kiểm tra trùng lịch (bỏ qua lịch hiện tại) - logic cải thiện
        $exists = BarberSchedule::where('barber_id', $data['barber_id'])
            ->where('schedule_date', $data['schedule_date'])
            ->where('id', '!=', $id) // loại trừ chính lịch này
            ->where(function ($query) use ($data) {
                // Kiểm tra overlap: lịch mới có bị trùng với lịch cũ không
                $query->where(function ($q) use ($data) {
                    // Trường hợp 1: start_time mới nằm trong khoảng thời gian cũ
                    $q->where('start_time', '<=', $data['start_time'])
                        ->where('end_time', '>', $data['start_time']);
                })->orWhere(function ($q) use ($data) {
                    // Trường hợp 2: end_time mới nằm trong khoảng thời gian cũ  
                    $q->where('start_time', '<', $data['end_time'])
                        ->where('end_time', '>=', $data['end_time']);
                })->orWhere(function ($q) use ($data) {
                    // Trường hợp 3: lịch mới bao trùm lịch cũ
                    $q->where('start_time', '>=', $data['start_time'])
                        ->where('end_time', '<=', $data['end_time']);
                });
            })->exists();

        if ($exists) {
            return back()->withErrors(['msg' => 'Thời gian đã bị trùng lịch với thợ cắt tóc này!'])->withInput();
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
