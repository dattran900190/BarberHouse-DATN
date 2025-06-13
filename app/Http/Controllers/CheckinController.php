<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Checkin;
use App\Models\Appointment;

use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class CheckinController extends Controller
{
    public function index(Request $request)
    {
        $checkins = Checkin::latest()->paginate(10);

    

        return view('admin.checkins.index', compact('checkins' ));
    }



    public function show(Checkin $checkin)
    {
        return view('admin.checkins.show', compact('checkin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);

        $checkin = Checkin::where('qr_code_value', $request->code)->first();

        if (!$checkin) {
            return back()->withErrors(['code' => 'Mã không đúng!']);
        }

        if ($checkin->is_checked_in) {
            return back()->withErrors(['code' => 'Mã này đã được sử dụng!']);
        }

        $checkin->update([
            'checkin_time' => now(),
            'is_checked_in' => true,
        ]);

        return redirect()->route('checkins.index')->with('success', 'Check-in thành công!');
    }

}

