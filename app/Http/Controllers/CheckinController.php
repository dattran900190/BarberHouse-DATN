<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Checkin;
use App\Models\Appointment;

use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\AppointmentStatusUpdated;


class CheckinController extends Controller
{
    public function index(Request $request)
    {
        $checkins = Checkin::latest()->paginate(10);
        return view('admin.checkins.index', compact('checkins'));
    }

    public function show(Checkin $checkin)
    {
        return view('admin.checkins.show', compact('checkin'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $request->validate([
            'code' => 'required|digits:6'
        ]);

        $checkin = Checkin::where('qr_code_value', $request->code)->first();

        if (!$checkin) {
            return back()->withErrors(['code' => 'Mã không đúng!']);
        }

        $checkin->update([
            'checkin_time' => now(),
            'is_checked_in' => true,
        ]);
        if ($checkin->appointment) {
            $checkin->appointment->update([
                'status' => 'progress', // hoặc 'đang_cắt_tóc' tùy vào hệ thống bạn dùng
            ]);
        }

        event(new AppointmentStatusUpdated($appointment));
        
        // Lấy tab hiện tại từ request
        $currentTab = $request->input('current_tab', 'progress');

         // thông báo chuyển trạng thái đặt lịch
         event(new AppointmentStatusUpdated($appointment));

        return redirect()->route('appointments.index', ['status' => $currentTab])->with('success', 'Check-in thành công!');
    }
}
