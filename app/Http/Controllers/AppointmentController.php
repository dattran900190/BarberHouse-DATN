<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $appointments = Appointment::with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name'])
            ->when($search, function ($query, $search) {
                // tìm theo tên user và tên barber
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('barber', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('barber', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(5);

        // $appointmentsDebug = Appointment::with(['service','branch'])->take(1)->get();
        // dd($appointments->toArray());

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'barber', 'service', 'branch', 'promotion']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $appointments = Appointment::all(); 
        return view('admin.appointments.edit', compact('appointment', 'appointment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
{
    $appointment->update($request->only(['appointment_time', 'status', 'payment_status', 'note']));

    return redirect()->route('appointments.index')->with('success', 'Cập nhật lịch hẹn thành công.');
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Appointment $appointment)
{
    $appointment->update(['status' => 'cancelled']);

    if ($appointment->status == 'cancelled') {
        return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được huỷ trước đó.');
    }

    return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được huỷ.');
}
}
