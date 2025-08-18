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

    // public function store(Request $request, Appointment $appointment)
    // {
    //     // nếu là admin chi nhánh thì chỉ được phép check-in tại chi nhánh của mình
    //     if (Auth::user()->role === 'admin_branch' && $appointment->branch_id !== Auth::user()->branch_id) {
    //         return redirect()->route('appointments.index')->with('error', 'Bạn chỉ được phép Check-in tại chi nhánh của mình.');
    //     }

    //     $request->validate([
    //         'code' => 'required|digits:6'
    //     ]);

    //     $checkin = Checkin::where('qr_code_value', $request->code)->first();

    //     if (!$checkin) {
    //         return back()->withErrors(['code' => 'Mã không đúng!']);
    //     }

    //     $checkin->update([
    //         'checkin_time' => now(),
    //         'is_checked_in' => true,
    //     ]);
    //     if ($checkin->appointment) {
    //         $checkin->appointment->update([
    //             'status' => 'progress', // hoặc 'đang_cắt_tóc' tùy vào hệ thống bạn dùng
    //         ]);
    //     }

    //     // Lấy tab hiện tại từ request
    //     $currentTab = $request->input('current_tab', 'progress');

    //     // thông báo chuyển trạng thái đặt lịch
    //     event(new AppointmentStatusUpdated($checkin->appointment));

    //     return redirect()->route('appointments.index', ['status' => $currentTab])->with('success', 'Check-in thành công!');
    // }

    public function checkin(Request $request, Appointment $appointment)
    {
        try {
            // Kiểm tra quyền admin chi nhánh
            if (Auth::user()->role === 'admin_branch' && $appointment->branch_id !== Auth::user()->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ được phép Check-in tại chi nhánh của mình.'
                ], 403);
            }

            // Validation
            $request->validate([
                'code' => 'required|digits:6'
            ]);

            // Kiểm tra trạng thái lịch hẹn
            if ($appointment->status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể check-in lịch hẹn đã được xác nhận.'
                ], 400);
            }

            // Kiểm tra mã check-in
            $checkin = Checkin::where('qr_code_value', $request->code)
                ->where('appointment_id', $appointment->id)
                ->first();

            if (!$checkin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã check-in không đúng hoặc không khớp với lịch hẹn này.'
                ], 400);
            }

            // Kiểm tra xem đã check-in chưa
            if ($checkin->is_checked_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này đã được check-in rồi.'
                ], 400);
            }

            // Thực hiện check-in
            $checkin->update([
                'checkin_time' => now(),
                'is_checked_in' => true,
            ]);

            // Cập nhật trạng thái lịch hẹn
            $appointment->update([
                'status' => 'progress',
            ]);

            $currentTab = $request->input('current_tab', 'progress');
            event(new AppointmentStatusUpdated($appointment));

            return response()->json([
                'success' => true,
                'message' => 'Check-in thành công! Lịch hẹn đã chuyển sang trạng thái "Đang làm tóc".',
                'redirect_url' => route('appointments.index', ['status' => $currentTab])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thực hiện check-in: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkCheckinCode(Request $request, $appointmentId)
{
    $appointment = Appointment::findOrFail($appointmentId);
    $checkin = Checkin::where('appointment_id', $appointmentId)
        ->where('qr_code_value', $request->code)
        ->first();

    return response()->json(['valid' => $checkin ? true : false, 'message' => $checkin ? 'Mã check-in hợp lệ' : 'Mã check-in không hợp lệ hoặc không khớp với lịch hẹn này.']);
}
}
