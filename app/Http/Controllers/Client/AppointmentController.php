<?php

namespace App\Http\Controllers\Client;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\Checkin;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use Illuminate\Support\Carbon;
use App\Models\UserRedeemedVoucher;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BookingRequest;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }
    /**
     * Display a listing of the resource.
     */
   
public function index(Request $request)
{
    $services = Service::select('id', 'name', 'price', 'duration')->get();
    $branches = Branch::all();

    // Lấy danh sách mã giảm giá khả dụng của người dùng
    $vouchers = Auth::check() ? UserRedeemedVoucher::where('user_id', Auth::id())
        ->where('is_used', false)
        ->with('promotion')
        ->get() : collect();

    // Lấy voucher công khai (required_points null hoặc 0)
    $publicPromotions = \App\Models\Promotion::where(function($q) {
            $q->whereNull('required_points')
              ->orWhere('required_points', 0);
        })
        ->where('is_active', true)
        ->where('quantity', '>', 0)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->get();

    // Mặc định: hiển thị tất cả barber nếu chưa chọn thời gian
    if ($request->filled('appointment_date') && $request->filled('appointment_time')) {
        $barbers = $this->getAvailableBarbers($request->appointment_date, $request->appointment_time);
    } else {
        $barbers = Barber::where('branch_id', $request->input('branch_id'))->get();
    }

    return view('client.booking', compact('barbers', 'services', 'branches', 'vouchers', 'publicPromotions'));
}

    public function getBarbersByBranch($branch_id)
    {
        $barbers = Barber::where('branch_id', $branch_id)->get();
        return response()->json($barbers);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     if (!Auth::check()) {
    //         return redirect()->route('dat-lich')->with('mustLogin', true);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'barber_id' => 'required|exists:barbers,id',
    //         'branch_id' => 'required|exists:branches,id',
    //         'service_id' => 'required|exists:services,id',
    //         'appointment_date' => 'required|date|after_or_equal:today',
    //         'voucher_id' => 'nullable|exists:user_redeemed_vouchers,id,user_id,' . Auth::id(),
    //         'appointment_time' => [
    //             'required',
    //             'regex:/^([01]\d|2[0-3]):([0-5]\d)$/',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 if ($value < '08:00' || $value > '20:00') {
    //                     $fail('Giờ đặt lịch phải từ 08:00 đến 20:00.');
    //                 }

    //                 $date = $request->input('appointment_date');
    //                 if ($date === now()->toDateString() && $value < now()->format('H:i')) {
    //                     $fail('Không thể đặt lịch ở thời điểm đã qua.');
    //                 }
    //             }
    //         ],
    //         'name' => 'nullable|string|max:100|required_if:other_person,1',
    //         'phone' => [
    //             'nullable',
    //             'required_if:other_person,1',
    //             'regex:/^0[0-9]{9}$/'
    //         ],
    //     ], [
    //         'barber_id.required' => 'Vui lòng chọn thợ cắt.',
    //         'barber_id.exists' => 'Thợ cắt không tồn tại.',

    //         'branch_id.required' => 'Vui lòng chọn chi nhánh đặt lịch.',
    //         'branch_id.exists' => 'Chi nhánh không tồn tại.',

    //         'service_id.required' => 'Vui lòng chọn dịch vụ.',
    //         'service_id.exists' => 'Dịch vụ không tồn tại.',

    //         'appointment_date.required' => 'Vui lòng chọn ngày đặt lịch.',
    //         'appointment_date.date' => 'Ngày đặt lịch không hợp lệ.',
    //         'appointment_date.after_or_equal' => 'Ngày đặt lịch phải là hôm nay hoặc sau.',

    //         'appointment_time.required' => 'Vui lòng chọn thời gian đặt lịch.',
    //         'appointment_time.regex' => 'Thời gian phải có định dạng HH:MM (ví dụ: 14:30).',

    //         'name.string' => 'Tên không hợp lệ.',
    //         'name.max' => 'Tên quá dài (tối đa 100 ký tự).',

    //         'required_if' => 'Vui lòng nhập :attribute khi đặt cho người khác.',
    //         'phone.regex' => 'Số điện thoại không hợp lệ. Phải có 10 chữ số và bắt đầu bằng 0.',
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     // Lấy tên và số điện thoại
    //     $name = $request->filled('other_person') ? $request->name : Auth::user()->name;
    //     $phone = $request->filled('other_person') ? $request->phone : Auth::user()->phone;

    //     $appointment = new Appointment();
    //     $appointment->appointment_code = strtoupper(Str::random(8));
    //     $appointment->user_id = Auth::id();
    //     $appointment->barber_id = $request->barber_id;
    //     $appointment->branch_id = $request->branch_id;
    //     $appointment->service_id = $request->service_id;
    //     $appointment->appointment_time = $request->appointment_date . ' ' . $request->appointment_time;
    //     $appointment->status = 'pending';
    //     $appointment->payment_status = 'unpaid';
    //     $appointment->note = $request->note;
    //     $appointment->name = $name;
    //     $appointment->phone = $phone;
    //     $appointment->save();

    //     // Áp dụng mã giảm giá nếu có
    //     if ($request->voucher_id) {
    //         $voucher = UserRedeemedVoucher::where('id', $request->voucher_id)
    //             ->where('user_id', Auth::id())
    //             ->firstOrFail();
    //         $this->appointmentService->applyPromotion($appointment, $voucher);
    //     }

    //     $code = rand(100000, 999999);
    //     Checkin::create([
    //         'appointment_id' => $appointment->id,
    //         'qr_code_value' => $code,
    //         'is_checked_in' => false,
    //         'checkin_time' => null,
    //     ]);

    //     Mail::to(Auth::user()->email)->send(new CheckinCodeMail($code, $appointment));


    //     return redirect()->back()->with('success', 'Đặt lịch thành công!');
    // }

    public function store(BookingRequest $request)
    {
        // Parse appointment datetime
        $datetime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time . ':00');

        // Check for overlapping appointments
        $service = Service::findOrFail($request->service_id);
        $duration = $service->duration;

        // Kiểm tra trùng lặp chính xác theo ràng buộc UNIQUE
    $exactMatch = Appointment::where('barber_id', $request->barber_id)
        ->where('branch_id', $request->branch_id)
        ->where('appointment_time', $datetime)
        ->exists();

    if ($exactMatch) {
        return redirect()->back()->with('error', 'Thời gian này đã được đặt.');
    }

    // Kiểm tra khoảng thời gian trùng lặp, loại trừ trường hợp trùng chính xác
    $existingAppointment = Appointment::where('barber_id', $request->barber_id)
        ->where('branch_id', $request->branch_id)
        ->where('appointment_time', '!=', $datetime) // Loại trừ trùng chính xác
        ->whereHas('service', function ($query) use ($datetime, $duration) {
            $query->whereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime])
                ->orWhereRaw('DATE_ADD(?, INTERVAL ? MINUTE) BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime, $duration]);
        })
        ->exists();

    if ($existingAppointment) {
        return redirect()->back()->with('error', 'Thợ này đã có lịch hẹn trong khoảng thời gian này.');
    }

        // Get name and phone (for self or other person)
        $name = $request->other_person ? $request->name : Auth::user()->name;
        $phone = $request->other_person ? $request->phone : Auth::user()->phone;
        $email = $request->other_person ? $request->email : Auth::user()->email;

        // Create appointment
        $appointment = Appointment::create([
            'appointment_code' => 'APP' . strtoupper(Str::random(6)),
            'user_id' => Auth::id(),
            'barber_id' => $request->barber_id,
            'branch_id' => $request->branch_id,
            'service_id' => $request->service_id,
            'appointment_time' => $datetime,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'note' => $request->note,
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'total_amount' => $service->price ?? 0,
        ]);

        // dd($appointment);

        // Apply voucher if provided
        if ($request->voucher_id) {
            $voucher = UserRedeemedVoucher::findOrFail($request->voucher_id);
            $this->appointmentService->applyPromotion($appointment, $voucher);
        }

        // Create check-in QR code
        $qrCode = rand(100000, 999999);
        Checkin::create([
            'appointment_id' => $appointment->id,
            'qr_code_value' => $qrCode,
            'is_checked_in' => false,
            'checkin_time' => null,
        ]);

        Mail::to($email)->send(new CheckinCodeMail($qrCode, $appointment));

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

    //    public function getAvailableBarbersByDate($branch_id, $date)
    // {
    //     $barbers = Barber::where('branch_id', $branch_id)
    //         ->whereHas('schedules', function ($query) use ($date) {
    //             $query->where('schedule_date', $date)->where('is_available', true);
    //         })->get();
    //     return response()->json($barbers);
    // }
    // public function getAvailableBarbersByDate($branch_id, $date, $time = null)
    // {
    //     Log::info('getAvailableBarbersByDate called', [
    //         'branch_id' => $branch_id,
    //         'date' => $date,
    //         'time' => $time
    //     ]);

    //     // Validate inputs
    //     if (!is_numeric($branch_id) || !Branch::find($branch_id)) {
    //         Log::error('Invalid branch_id', ['branch_id' => $branch_id]);
    //         return response()->json(['error' => 'Invalid branch ID'], 400);
    //     }

    //     try {
    //         $query = Barber::where('branch_id', $branch_id)
    //             ->where('status', 'hoạt động')
    //             ->whereHas('schedules', function ($query) use ($date) {
    //                 $query->where('schedule_date', $date)->where('is_available', true);
    //             });

    //         if ($time) {
    //             $datetime = Carbon::parse($date . ' ' . $time . ':00'); // Ensure correct time format
    //             $query->whereDoesntHave('appointments', function ($query) use ($datetime) {
    //                 $query->where('appointment_time', $datetime)
    //                     ->whereIn('status', ['pending', 'confirmed']);
    //             });
    //         }

    //         $barbers = $query->get();
    //         Log::info('Barbers found:', ['count' => $barbers->count(), 'data' => $barbers]);

    //         return response()->json($barbers);
    //     } catch (\Exception $e) {
    //         Log::error('Error in getAvailableBarbersByDate: ' . $e->getMessage());
    //         return response()->json(['error' => 'Server error'], 500);
    //     }
    // }

    public function getAvailableBarbersByDate($branch_id, $date, $time = null, $service_id = null)
    {
        if (!is_numeric($branch_id) || !Branch::find($branch_id)) {
            return response()->json(['error' => 'Invalid branch ID'], 400);
        }

        try {
            $parsedDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            $datetime = null;
            $parsedTime = null;
            $serviceDuration = null;

            // Lấy duration của dịch vụ nếu có service_id
            if ($service_id) {
                $service = Service::find($service_id);
                if (!$service) {
                    return response()->json(['error' => 'Invalid service ID'], 400);
                }
                $serviceDuration = $service->duration;
            }

            if ($time) {
                $time = urldecode($time);
                $parsedTime = Carbon::parse($time)->format('H:i');
                $datetime = Carbon::parse($date . ' ' . $time . ':00');
            }

            $query = Barber::select('barbers.id', 'barbers.name')
                ->where('branch_id', $branch_id)
                ->where('status', 'active')
                ->whereHas('schedules', function ($query) use ($parsedDate, $parsedTime, $datetime, $serviceDuration) {
                    $query->where('schedule_date', $parsedDate)
                        ->where('is_available', true);
                    if ($parsedTime && $datetime) {
                        $query->whereTime('start_time', '<=', $parsedTime);
                        // Kiểm tra end_time dựa trên duration
                        if ($serviceDuration) {
                            $endAppointmentTime = $datetime->copy()->addMinutes($serviceDuration)->format('H:i:s');
                            $query->whereTime('end_time', '>=', $endAppointmentTime);
                        } else {
                            $query->whereTime('end_time', '>=', $parsedTime);
                        }
                    }
                });

            if ($datetime && $serviceDuration) {
                $query->whereDoesntHave('appointments', function ($query) use ($datetime, $serviceDuration) {
                    $query->whereIn('status', ['pending', 'confirmed'])
                        ->whereHas('service', function ($serviceQuery) use ($datetime, $serviceDuration) {
                            $serviceQuery->whereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime])
                                ->orWhereRaw('DATE_ADD(?, INTERVAL ? MINUTE) BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime, $serviceDuration]);
                        });
                });
            } elseif ($datetime) {
                $query->whereDoesntHave('appointments', function ($query) use ($datetime, $serviceDuration) {
                $query->where(function ($q) use ($datetime, $serviceDuration) {
                    $q->where('appointment_time', $datetime)
                        ->orWhere(function ($subQ) use ($datetime, $serviceDuration) {
                            $subQ->where('appointment_time', '!=', $datetime)
                                 ->whereHas('service', function ($serviceQuery) use ($datetime, $serviceDuration) {
                                     $serviceQuery->whereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime])
                                         ->orWhereRaw('DATE_ADD(?, INTERVAL ? MINUTE) BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime, $serviceDuration ?: 0]);
                                 });
                        });
                });
            });
            }

            // Sử dụng distinct trên id của barbers để tránh trùng lặp
            $barbers = $query->distinct('barbers.id')->get();

            return response()->json($barbers);
        } catch (\Exception $e) {
            Log::error('Error in getAvailableBarbersByDate: ' . $e->getMessage(), [
                'branch_id' => $branch_id,
                'date' => $date,
                'time' => $time,
                'service_id' => $service_id
            ]);
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function completeAppointment($appointmentId)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);
            $appointment->status = 'completed';
            $appointment->save();

            return response()->json(['message' => 'Lịch hẹn đã hoàn thành']);
        } catch (\Exception $e) {
            Log::error('Error in completeAppointment: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }
}
