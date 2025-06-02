<?php

namespace App\Http\Controllers;

use App\Models\BarberSchedule;
use App\Models\Barber;
use App\Http\Requests\BarberSchedulesRequest;

class BarberScheduleController extends Controller
{
    public function index()
    {
        // Thay schedule_date thành date
        $schedules = BarberSchedule::with('barber')->orderBy('schedule_date', 'desc')->paginate(10);

        return view('admin.barber_schedules.index', compact('schedules'));
    }

    public function create()
    {
        $barbers = Barber::all();
        return view('admin.barber_schedules.create', compact('barbers'));
    }

    public function store(BarberSchedulesRequest $request)
    {
        // Kiểm tra lịch cắt tóc đã tồn tại chưa
        $exists = BarberSchedule::where('barber_id', $request->barber_id)
            ->where('schedule_date', $request->schedule_date)
            ->where('start_time', $request->start_time)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['duplicate' => 'Lịch cắt tóc đã tồn tại cho thợ này vào thời gian này.']);
        }

        // Tạo lịch nếu không trùng
        BarberSchedule::create($request->validated());

        return redirect()->route('barber_schedules.index')
            ->with('success', 'Lịch hẹn đã được tạo thành công');
    }



    public function show(BarberSchedule $barberSchedule)
    {
        $barberSchedule->load('barber');
        return view('admin.barber_schedules.show', compact('barberSchedule'));
    }

    public function edit(BarberSchedule $barberSchedule)
    {
        $barbers = Barber::all();
        return view('admin.barber_schedules.edit', compact('barberSchedule', 'barbers'));
    }

    public function update(BarberSchedulesRequest $request, BarberSchedule $barberSchedule)
    {
        $data = $request->validated();

        $barberSchedule->update($data);

        return redirect()->route('barber_schedules.index')->with('success', 'Lịch hẹn đã được cập nhật thành công');
    }

    public function destroy(BarberSchedule $barberSchedule)
    {
        $barberSchedule->delete();

        return redirect()->route('barber_schedules.index')->with('success', 'Lịch hẹn đã được xóa thành công');
    }
}