<?php

namespace App\Http\Controllers\Client;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BookingRequest;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $services = Service::all();
        $branches = Branch::all();

        // Mặc định: hiển thị tất cả barber nếu chưa chọn thời gian
        if ($request->filled('appointment_date') && $request->filled('appointment_time')) {
            $barbers = $this->getAvailableBarbers($request->appointment_date, $request->appointment_time);
        } else {
            $barbers = Barber::all();
        }

        return view('client.booking', compact('barbers', 'services', 'branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookingRequest $request)
    {
        $appointment = new Appointment();
        $appointment->appointment_code = strtoupper(Str::random(8));
        $appointment->user_id = Auth::id();
        $appointment->barber_id = null; // sẽ gán sau khi xác nhận
        $appointment->branch_id = $request->branch_id;
        $appointment->service_id = $request->service_id;
        $appointment->appointment_time = $request->appointment_date . ' ' . $request->appointment_time;
        $appointment->status = 'pending';
        $appointment->payment_status = 'unpaid';
        $appointment->save();

        return redirect()->back()->with('success', 'Đặt lịch thành công!');
    }


    public function getAvailableBarbers($date, $time)
    {
        $datetime = Carbon::parse($date . ' ' . $time);

        // Lấy danh sách barber KHÔNG có lịch hẹn vào thời điểm này
        $availableBarbers = Barber::whereDoesntHave('appointments', function ($query) use ($datetime) {
            $query->where('appointment_time', $datetime)
                ->whereIn('status', ['pending', 'confirmed']); // chỉ tính lịch chưa bị hủy
        })->get();

        return $availableBarbers;
    }
}
