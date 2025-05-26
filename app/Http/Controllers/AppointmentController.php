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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
