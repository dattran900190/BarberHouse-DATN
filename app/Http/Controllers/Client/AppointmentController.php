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
use App\Events\NewAppointment;
use Illuminate\Support\Carbon;
use App\Mail\PendingBookingMail;
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
        $publicPromotions = \App\Models\Promotion::where(function ($q) {
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

    public function appointmentHistory(Request $request)
    {
        $query = Appointment::where('user_id', Auth::id())
            ->with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name']);

        // Lọc theo trạng thái
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Tìm kiếm theo mã đặt lịch, dịch vụ, hoặc thợ
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('appointment_code', 'like', '%' . $search . '%')
                    ->orWhereHas('service', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('barber', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $appointments = $query->orderBy('id', 'DESC')->paginate(5);

        return view('client.appointmentHistory', compact('appointments'));
    }

    public function detailAppointmentHistory($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())
            ->with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name'])
            ->findOrFail($id);

        return view('client.detailAppointmentHistory', compact('appointment'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        // Kiểm tra quyền sở hữu
        if ($appointment->user_id !== Auth::id()) {
            return redirect()->route('client.appointmentHistory')
                ->with('error', 'Bạn không có quyền hủy lịch hẹn này.');
        }

        // Kiểm tra trạng thái hợp lệ
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->route('client.appointmentHistory')
                ->with('error', 'Lịch hẹn này không thể hủy.');
        }

        // Validate lý do hủy
        $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:500',
        ], [
            'cancellation_reason.required' => 'Vui lòng nhập lý do hủy.',
            'cancellation_reason.min' => 'Lý do hủy phải có ít nhất 5 ký tự.',
            'cancellation_reason.max' => 'Lý do hủy không được vượt quá 500 ký tự.',
        ]);

        // Cập nhật trạng thái và lý do
        $appointment->update([
            'status' => 'pending_cancellation',
            'status_before_cancellation' => $appointment->status, // Lưu trạng thái hiện tại
            'cancellation_reason' => $request->input('cancellation_reason'),
        ]);

        // (Tùy chọn) Gửi thông báo cho admin
        // \App\Models\Admin::all()->each(function ($admin) use ($appointment) {
        //     $admin->notify(new CancellationRequest($appointment));
        // });

        return redirect()->route('client.appointmentHistory')
            ->with('success', 'Yêu cầu hủy lịch hẹn ' . $appointment->appointment_code . ' đã được gửi.');
    }


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
            'appointment_code' => 'APP' . date('YmdHis') . strtoupper(Str::random(3)),
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
            'cancellation_reason' => null,
            'rejection_reason' => null,
            'status_before_cancellation' => null,
            'total_amount' => $service->price ?? 0,
        ]);

        // dd($appointment);

   if ($request->voucher_code) {
    $code = trim($request->voucher_code);

    // Ưu tiên tìm trong danh sách voucher đã đổi
    $redeemedVoucher = UserRedeemedVoucher::whereHas('promotion', function ($q) use ($code) {
        $q->where('code', $code);
    })
    ->where('user_id', Auth::id())
    ->where('is_used', false)
    ->first();

    if ($redeemedVoucher) {
        $this->appointmentService->applyPromotion($appointment, $redeemedVoucher);
    } else {
        // Nếu không tìm thấy, thử tìm trong promotions công khai
        $promotion = \App\Models\Promotion::where('code', $code)
            ->where(function ($q) {
                $q->whereNull('required_points')->orWhere('required_points', 0);
            })
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();

        if ($promotion) {
            $this->appointmentService->applyPromotion($appointment, null, $promotion);
        }
    }
}


        try {
            Log::info('Kích hoạt sự kiện NewAppointment', [$appointment->toArray()]);
            event(new NewAppointment($appointment));
            Log::info('Sự kiện NewAppointment đã gửi');
        } catch (\Exception $e) {
            Log::error('Lỗi khi gửi sự kiện NewAppointment', ['error' => $e->getMessage()]);
        }
        Mail::to($appointment->email)->send(new PendingBookingMail($appointment));


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

            // Xử lý $time
            if ($time && $time !== 'null') {
                $time = urldecode($time);
                if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time)) {
                    return response()->json(['error' => 'Invalid time format'], 400);
                }
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

    public function getBarbersByBranch($branch_id)
    {
        $barbers = Barber::where('branch_id', $branch_id)->get();
        return response()->json($barbers);
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

    public function confirmAppointment($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->status = 'confirmed';
        $appointment->save();

        // // Create check-in QR code
        $qrCode = rand(100000, 999999);
        Checkin::create([
            'appointment_id' => $appointment->id,
            'qr_code_value' => $qrCode,
            'is_checked_in' => false,
            'checkin_time' => null,
        ]);
        $checkin = Checkin::where('appointment_id', $appointment->id)->first();
        Mail::to($appointment->email)->send(new CheckinCodeMail($checkin->qr_code_value, $appointment));

        return response()->json(['message' => 'Lịch hẹn đã được xác nhận']);
    }
}
