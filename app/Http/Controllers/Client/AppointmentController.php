<?php

namespace App\Http\Controllers\Client;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\Checkin;
use App\Models\Service;
use App\Models\Promotion;
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
use App\Models\CancelledAppointment;
use App\Services\AppointmentService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BookingRequest;
use App\Models\Review;
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
        $publicPromotions = Promotion::where(function ($q) {
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
        $userId = Auth::id();
        $search = $request->input('search');
        $status = $request->input('status');
        $allAppointments = collect();

        // Hàm xây dựng truy vấn chung
        $buildQuery = function ($query, $search, $status) {
            $query->with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name'])
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('appointment_code', 'like', '%' . $search . '%')
                            ->orWhereHas('service', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('barber', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->when($status, function ($q) use ($status) {
                    if ($status === 'canceled') { // Dùng 'canceled' để khớp với dropdown
                        $q->whereIn('cancellation_type', ['canceled', 'no-show']);
                    } else {
                        $q->where('status', $status);
                    }
                });
        };

        // Nếu có tìm kiếm hoặc lọc trạng thái, lấy tất cả lịch hẹn từ cả hai bảng
        if ($search || $status) {
            // Lấy từ bảng appointments
            $appointmentQuery = Appointment::where('user_id', $userId);
            $buildQuery($appointmentQuery, $search, $status);
            $appointmentsResult = $appointmentQuery->get();

            // Lấy từ bảng cancelled_appointments
            if (!$status || $status === 'canceled') {
                $canceledQuery = CancelledAppointment::where('user_id', $userId);
                $buildQuery($canceledQuery, $search, $status);
                $canceledResult = $canceledQuery->get();
            } else {
                $canceledResult = collect();
            }

            // Kết hợp kết quả
            $allAppointments = $appointmentsResult->merge($canceledResult)
                ->sortByDesc('updated_at')
                ->take(5);
        } else {
            // Lấy lịch hẹn từ cả hai bảng mà không lọc
            $appointmentQuery = Appointment::where('user_id', $userId);
            $buildQuery($appointmentQuery, null, null);
            $appointmentsResult = $appointmentQuery->get();

            $canceledQuery = CancelledAppointment::where('user_id', $userId);
            $buildQuery($canceledQuery, null, null);
            $canceledResult = $canceledQuery->get();

            $allAppointments = $appointmentsResult->merge($canceledResult)
                ->sortByDesc('updated_at')
                ->take(5);
        }

        // Phân trang thủ công
        $perPage = 5;
        $page = Paginator::resolveCurrentPage() ?: 1;
        $appointments = new \Illuminate\Pagination\LengthAwarePaginator(
            $allAppointments->forPage($page, $perPage),
            $allAppointments->count(),
            $perPage,
            $page,
            ['path' => route('client.appointmentHistory')]
        );

        return view('client.appointmentHistory', compact('appointments', 'search', 'status'));
    }

    public function detailAppointmentHistory($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())
            ->with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name', 'review'])
            ->findOrFail($id);

        return view('client.detailAppointmentHistory', compact('appointment'));
    }
    public function cancel(Request $request, Appointment $appointment)
    {
        // Kiểm tra quyền sở hữu
        if ($appointment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền hủy lịch hẹn này.'
            ], 403);
        }

        // Kiểm tra trạng thái hợp lệ
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Lịch hẹn này không thể hủy.'
            ], 400);
        }

        // Validate lý do hủy
        $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:500',
        ], [
            'cancellation_reason.required' => 'Vui lòng nhập lý do hủy.',
            'cancellation_reason.min' => 'Lý do hủy phải có ít nhất 5 ký tự.',
            'cancellation_reason.max' => 'Lý do hủy không được vượt quá 500 ký tự.',
        ]);

        // Tạo bản ghi trong cancelled_appointments
        CancelledAppointment::create(array_merge($appointment->toArray(), [
            'status' => 'cancelled',
            'status_before_cancellation' => $appointment->status,
            'cancellation_type' => 'Khách hàng huỷ',
            'cancellation_reason' => $request->input('cancellation_reason'),
        ]));

        // Xóa bản ghi khỏi bảng appointments
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hủy.'
        ]);
    }
    public function store(BookingRequest $request)
    {
        try {
            // Kiểm tra đăng nhập
            if (!Auth::check()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn cần đăng nhập để đặt lịch.'
                    ], 401);
                }
                return redirect()->route('dat-lich')->with('mustLogin', true);
            }

            // Parse appointment datetime
            $datetime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time . ':00');

            // Check for overlapping appointments
            $service = Service::findOrFail($request->service_id);
            $duration = $service->duration;

            // Kiểm tra trùng lặp chính xác
            $exactMatch = Appointment::where('barber_id', $request->barber_id)
                ->where('branch_id', $request->branch_id)
                ->where('appointment_time', $datetime)
                ->whereIn('status', ['pending', 'confirmed', 'pending_cancellation'])
                ->exists();

            if ($exactMatch) {
                session()->flash('errors', ['Thời gian này đã được đặt.']);
                return response()->json([
                    'success' => false,
                    'message' => 'Thời gian này đã được đặt.'
                ], 422);
            }

            // Kiểm tra khoảng thời gian trùng lặp
            $existingAppointment = Appointment::where('barber_id', $request->barber_id)
                ->where('branch_id', $request->branch_id)
                ->where('appointment_time', '!=', $datetime)
                ->whereIn('status', ['pending', 'confirmed', 'pending_cancellation'])
                ->whereHas('service', function ($query) use ($datetime, $duration) {
                    $query->whereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime])
                        ->orWhereRaw('DATE_ADD(?, INTERVAL ? MINUTE) BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime, $duration]);
                })
                ->exists();

            if ($existingAppointment) {
                session()->flash('errors', ['Thợ này đã có lịch hẹn trong khoảng thời gian này.']);
                return response()->json([
                    'success' => false,
                    'message' => 'Thợ này đã có lịch hẹn trong khoảng thời gian này.'
                ], 422);
            }

            // Get name, phone, and email (for self or other person)
            $name = $request->other_person ? $request->name : Auth::user()->name;
            $phone = $request->other_person ? $request->phone : Auth::user()->phone;
            $email = $request->other_person ? $request->email : Auth::user()->email;

            // Calculate total amount and discount
            $totalAmount = $service->price ?? 0;
            $discountAmount = 0;
            $promotion = null;

            // Xử lý voucher
            if ($request->voucher_code) {
                $code = trim($request->voucher_code);
                $redeemedVoucher = UserRedeemedVoucher::whereHas('promotion', function ($q) use ($code) {
                    $q->where('code', $code);
                })
                    ->where('user_id', Auth::id())
                    ->where('is_used', false)
                    ->first();

                if ($redeemedVoucher) {
                    $promotion = $redeemedVoucher->promotion;
                } else {
                    $promotion = Promotion::where('code', $code)
                        ->where(function ($q) {
                            $q->whereNull('required_points')->orWhere('required_points', 0);
                        })
                        ->where('is_active', true)
                        ->where('quantity', '>', 0)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->first();
                }

                if ($promotion) {
                    if ($promotion->discount_type === 'fixed') {
                        $discountAmount = $promotion->discount_value;
                    } else {
                        $discountAmount = $totalAmount * $promotion->discount_value / 100;
                    }
                    $totalAmount -= $discountAmount;
                }
            }

            // Create appointment
            $appointment = Appointment::create([
                'appointment_code' => 'APP' . date('YmdHis') . strtoupper(Str::random(3)),
                'user_id' => Auth::id(),
                'barber_id' => $request->barber_id,
                'branch_id' => $request->branch_id,
                'service_id' => $request->service_id,
                'appointment_time' => $datetime,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'unpaid',
                'note' => $request->note,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'cancellation_reason' => null,
                'rejection_reason' => null,
                'status_before_cancellation' => null,
                'promotion_id' => $promotion ? $promotion->id : null,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
            ]);

            // Xử lý voucher
            if ($promotion && $redeemedVoucher) {
                $this->appointmentService->applyPromotion($appointment, $redeemedVoucher);
            } elseif ($promotion) {
                $this->appointmentService->applyPromotion($appointment, null, $promotion);
            }

            // Gửi thông báo
            try {
                event(new NewAppointment($appointment));
                Mail::to($appointment->email)->send(new PendingBookingMail($appointment));
            } catch (\Exception $e) {
                Log::error('Lỗi khi gửi sự kiện NewAppointment hoặc email', ['error' => $e->getMessage()]);
            }

            // Nếu chọn VNPay, trả về appointment_id để chuyển hướng
            if ($request->payment_method === 'vnpay') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Lịch hẹn đã được tạo, đang chuyển hướng đến thanh toán VNPay.',
                        'appointment_id' => $appointment->id,
                    ]);
                }
            }

            // Nếu không phải VNPay, trả về thông báo thành công
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đặt lịch thành công!'
                ]);
            }
            return redirect()->route('appointments.index')->with('success', 'Đặt lịch thành công!');
        } catch (\Exception $e) {
            Log::error('Booking error: ' . $e->getMessage());
            session()->flash('errors', ['Lỗi khi đặt lịch: ' . $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đặt lịch: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableBarbersByDate($branch_id, $date, $time = null, $service_id = null)
    {
        // 1. Validate branch
        if (!is_numeric($branch_id) || !Branch::find($branch_id)) {
            return response()->json(['error' => 'Invalid branch ID'], 400);
        }

        try {
            $parsedDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            $parsedTime = null;
            $datetime = null;
            $serviceDuration = null;

            // 2. Lấy duration nếu có service
            if ($service_id) {
                $service = Service::find($service_id);
                if (!$service) {
                    return response()->json(['error' => 'Invalid service ID'], 400);
                }
                $serviceDuration = $service->duration;
            }

            // 3. Parse time nếu client truyền lên
            if ($time && $time !== 'null') {
                // Chuẩn hoá SA/CH -> 24h
                $rawTime = $time;
                if (preg_match('/^(\d{1,2}):([0-5]\d)\s*(SA|CH)$/iu', $rawTime, $m)) {
                    $hour = (int) $m[1];
                    $minute = $m[2];
                    $suf = strtoupper($m[3]);
                    if ($suf === 'SA') {
                        if ($hour === 12)
                            $hour = 0;
                    } else {
                        if ($hour < 12)
                            $hour += 12;
                    }
                    $time = sprintf('%02d:%02d', $hour, $minute);
                }

                $parsedTime = Carbon::createFromFormat('H:i', $time)->format('H:i');
                $datetime = Carbon::parse("{$date} {$time}:00");
            }

            // 4. Build query
            $query = Barber::query()
                ->select('barbers.id', 'barbers.name')
                ->where('branch_id', $branch_id)
                ->where('status', 'idle')
                // Loại bỏ thợ “off” trong ngày $parsedDate
                ->whereDoesntHave('schedules', function ($q) use ($parsedDate) {
                    $q->where('schedule_date', $parsedDate)
                        ->where('status', 'off');
                });

            // 5. Nếu filter theo giờ
            if ($parsedTime) {
                $appointmentEnd = $serviceDuration
                    ? $datetime->copy()->addMinutes($serviceDuration)->format('H:i')
                    : $parsedTime;

                $query->where(function ($q) use ($parsedDate, $parsedTime, $appointmentEnd) {
                    $q->whereHas('schedules', function ($qs) use ($parsedDate, $parsedTime, $appointmentEnd) {
                        $qs->where('schedule_date', $parsedDate)
                            ->where('status', 'custom')
                            ->whereTime('start_time', '<=', $parsedTime)
                            ->whereTime('end_time', '>=', $appointmentEnd);
                    })
                        ->orWhereDoesntHave('schedules', function ($qs) use ($parsedDate) {
                            $qs->where('schedule_date', $parsedDate)
                                ->where('status', 'custom');
                        });
                });
            }

            // 6. Kiểm tra tính khả dụng của thợ
            if ($datetime && $serviceDuration) {
                $query->whereDoesntHave('appointments', function ($q) use ($datetime, $serviceDuration) {
                    $q->whereIn('status', ['pending', 'confirmed'])
                        ->whereHas('service', function ($sq) use ($datetime, $serviceDuration) {
                            $sq->whereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime])
                                ->orWhereRaw('DATE_ADD(?, INTERVAL ? MINUTE) BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL services.duration MINUTE)', [$datetime, $serviceDuration]);
                        });
                });
            } elseif ($datetime) {
                $query->whereDoesntHave('appointments', function ($q) use ($datetime) {
                    $q->whereIn('status', ['pending', 'confirmed'])
                        ->where('appointment_time', $datetime);
                });
            }

            $barbers = $query->distinct('barbers.id')->get();
            return response()->json($barbers);
        } catch (\Exception $e) {
            Log::error('Error in getAvailableBarbersByDate: ' . $e->getMessage(), compact('branch_id', 'date', 'time', 'service_id'));
            return response()->json(['error' => 'Server error'], 500);
        }
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
}
