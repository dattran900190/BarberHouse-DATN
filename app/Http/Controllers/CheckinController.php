<?php

namespace App\Http\Controllers;

use App\Mail\CheckinCodeMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Appointment;

use App\Models\Checkin;
use Illuminate\Http\Request;
use Carbon\Carbon;


class CheckinController extends Controller
{
    public function index()
    {
        $checkins = Checkin::latest()->paginate(10);
        return view('admin.checkins.index', compact('checkins'));
    }

    public function create()
    {
        
    }
    public function verifyForm()
{
    return view('admin.checkins.verify');
}

public function verifyCode(Request $request)
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


   public function store(Request $request)
{

}

}

