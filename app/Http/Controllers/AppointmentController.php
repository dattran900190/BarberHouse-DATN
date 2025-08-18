<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Branch;
use App\Models\Review;
use App\Models\Checkin;
use App\Models\Service;
use App\Models\Promotion;
use App\Models\Appointment;
use Illuminate\Support\Str;
use App\Mail\CustomerNoShow;
use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\UserRedeemedVoucher;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminCancelBookingMail;
use App\Models\CancelledAppointment;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use App\Events\AppointmentStatusUpdated;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\BookingAdminRequest;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }
   public function index(Request $request)
{
    $search = $request->input('search');
    $activeTab = $request->input('status', 'pending');
    $allAppointments = collect();
    $statuses = ['pending', 'confirmed', 'checked-in', 'progress', 'completed'];
    $appointments = [];
    $user = Auth::user();

    // Query builder cho Appointments
    $buildAppointmentQuery = function ($query, $search) use ($user) {
        $query->with(['user:id,name', 'barber:id,name', 'service:id,name'])
            ->when($user->role === 'admin_branch', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($subQuery) use ($search) {
                    $searchableFields = [
                        'name',
                        'phone',
                        'email',
                        'appointment_code',
                        'additional_services',
                        'status',
                        'payment_method',
                        'payment_status',
                        'note',
                        'cancellation_reason',
                    ];

                    foreach ($searchableFields as $field) {
                        $subQuery->orWhere($field, 'like', "%{$search}%");
                    }

                    // ✅ Search theo ngày nhập d/m/Y
                    try {
                        $date = Carbon::createFromFormat('d/m/Y', $search)->format('Y-m-d');
                        $subQuery->orWhereDate('appointment_time', $date);
                    } catch (\Exception $e) {
                        // bỏ qua nếu không phải dạng ngày
                    }

                    // Search quan hệ
                    $subQuery->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('barber', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('service', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'DESC');
    };

    // Query builder cho Cancelled
    $buildCancelledQuery = function ($query, $search) use ($user) {
        $query->with(['user:id,name', 'barber:id,name', 'service:id,name'])
            ->when($user->role === 'admin_branch', fn($q) => $q->where('branch_id', $user->branch_id))
            ->when($search, function ($q) use ($search) {
                 $q->where(function ($subQuery) use ($search) {
                    $searchableFields = [
                        'name',
                        'phone',
                        'email',
                        'appointment_code',
                        'additional_services',
                        'status',
                        'payment_method',
                        'payment_status',
                        'note',
                        'cancellation_reason',
                    ];

                    foreach ($searchableFields as $field) {
                        $subQuery->orWhere($field, 'like', "%{$search}%");
                    }

                    // ✅ Search theo ngày nhập d/m/Y
                    try {
                        $date = Carbon::createFromFormat('d/m/Y', $search)->format('Y-m-d');
                        $subQuery->orWhereDate('appointment_time', $date);
                    } catch (\Exception $e) {
                        // bỏ qua nếu không phải dạng ngày
                    }

                    // Search quan hệ
                    $subQuery->orWhereHas('user', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('barber', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('service', fn($q2) => $q2->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('created_at', 'DESC');
    };

    // Nếu search mà không có status cụ thể → tìm cả 2 bảng
    if ($search) {
    if (!$request->has('status')) {
        // search tất cả (appointments + cancelled)
        $appointmentQuery = Appointment::query();
        $buildAppointmentQuery($appointmentQuery, $search);
        $appointmentsResult = $appointmentQuery->get();

        $cancelledQuery = CancelledAppointment::query();
        $buildCancelledQuery($cancelledQuery, $search);
        $cancelledResult = $cancelledQuery->get();

        $allAppointments = $appointmentsResult->merge($cancelledResult);

        if ($allAppointments->count() > 0) {
            $activeTab = $allAppointments->first()->status ?? 'cancelled';
        }
    } else {
        // search theo đúng status được chọn
        if ($activeTab === 'cancelled') {
            $cancelledQuery = CancelledAppointment::query();
            $buildCancelledQuery($cancelledQuery, $search);
            $allAppointments = $cancelledQuery->get();
        } else {
            $appointmentQuery = Appointment::where('status', $activeTab);
            $buildAppointmentQuery($appointmentQuery, $search);
            $allAppointments = $appointmentQuery->get();
        }
    }
}


    // Lấy danh sách phân trang cho từng tab
    foreach ($statuses as $status) {
        $query = Appointment::where('status', $status);
        $buildAppointmentQuery($query, $search);
        $appointments[$status . 'Appointments'] = $query->paginate(10, ['*'], $status . '_page');
    }

    $cancelledQuery = CancelledAppointment::query();
    $buildCancelledQuery($cancelledQuery, $search);
    $appointments['cancelledAppointments'] = $cancelledQuery->paginate(10, ['*'], 'cancelled_page');

    // Trả về view
    return view('admin.appointments.index', array_merge(
        compact('activeTab', 'allAppointments', 'search'),
        $appointments
    ));
}


    public function markNoShow(Request $request, Appointment $appointment)
    {
        try {
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể đánh dấu no-show cho lịch hẹn ở trạng thái chờ xác nhận hoặc đã xác nhận.'
                ], 400);
            }

            Checkin::where('appointment_id', $appointment->id)->delete();

            CancelledAppointment::create(array_merge($appointment->toArray(), [
                'status' => 'cancelled',
                'payment_status' => $appointment->payment_status,
                'payment_method' => $appointment->payment_method,
                'cancellation_type' => 'no-show',
                'status_before_cancellation' => $appointment->status,
                'additional_services' => $appointment->additional_services,
                'cancellation_reason' => $request->input('no_show_reason', 'Khách hàng không đến'),
                'appointment_time' => $appointment->appointment_time
                    ? Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s')
                    : null,
            ]));

            DB::table('checkins')->where('appointment_id', $appointment->id)->delete();
            DB::table('refund_requests')->where('appointment_id', $appointment->id)->delete();

            $appointment->delete();

            Mail::to($appointment->email)->queue(new CustomerNoShow($appointment));

            // Lấy tab hiện tại từ request
            $currentTab = $request->input('current_tab', 'pending');

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu lịch hẹn ' . $appointment->appointment_code . ' là no-show.',
                'redirect_url' => route('appointments.index', ['status' => $currentTab])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi đánh dấu no-show: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function confirm(Request $request, Appointment $appointment)
    {
        try {
            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này không ở trạng thái chờ xác nhận.'
                ], 400);
            }

            $appointment->status = 'confirmed';
            $appointment->save();

            $qrCode = rand(100000, 999999);
            Checkin::create([
                'appointment_id' => $appointment->id,
                'qr_code_value' => $qrCode,
                'is_checked_in' => false,
                'checkin_time' => null,
            ]);

            $additionalServices = [];
            if (!empty($appointment->additional_services)) {
                $serviceIds = is_array($appointment->additional_services)
                    ? $appointment->additional_services
                    : json_decode($appointment->additional_services, true);
                $additionalServices = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();
            }

            $checkin = Checkin::where('appointment_id', $appointment->id)->first();
            Mail::to($appointment->email)->queue(new CheckinCodeMail($checkin->qr_code_value, $appointment, $additionalServices));

            event(new AppointmentStatusUpdated($appointment));

            // Lấy tab hiện tại từ request
            $currentTab = $request->input('current_tab', 'pending');

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được xác nhận.',
                'redirect_url' => route('appointments.index', ['status' => $currentTab])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xác nhận lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completed(Request $request, Appointment $appointment)
    {
        try {
            $appointment->status = 'completed';
            $appointment->payment_status = 'paid';
            $appointment->save();

            event(new AppointmentStatusUpdated($appointment));

            // Lấy tab hiện tại từ request
            $currentTab = $request->input('current_tab', 'pending');

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hoàn thành.',
                'redirect_url' => route('appointments.index', ['status' => $currentTab])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi hoàn thành lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        try {
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này không thể hủy.'
                ], 400);
            }

            $checkCancelledAppointment = CancelledAppointment::where('id', $appointment->id)->first();
            if ($checkCancelledAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này đã bị hủy trước đó.'
                ], 400);
            }

            $appointmentData = $appointment->toArray();
            $appointmentData['appointment_time'] = $appointment->appointment_time
                ? Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s')
                : null;
              
              // Hoàn lại voucher nếu có
            $oldPromotionId = $appointment->promotion_id;
            if ($oldPromotionId) {
                $oldPromotion = Promotion::find($oldPromotionId);
                if ($oldPromotion) {
                    // Chỉ hoàn lại quantity cho voucher công khai (required_points là null)
                    if (is_null($oldPromotion->required_points)) {
                        $oldPromotion->increment('quantity'); // Hoàn lại số lượng voucher
                    }

                    // Nếu voucher từ bảng UserRedeemedVoucher thì mở lại (cho voucher cá nhân)
                    $oldRedeemed = UserRedeemedVoucher::where('user_id', $appointment->user_id)
                        ->where('promotion_id', $oldPromotionId)
                        ->where('is_used', true)
                        ->first();
                    if ($oldRedeemed) {
                        $oldRedeemed->update(['is_used' => false]);
                    }
                }
            }
            // Tạo bản ghi CancelledAppointment
            $cancelledAppointment = CancelledAppointment::create(array_merge($appointmentData, [
                'status' => 'cancelled',
                'payment_status' => $appointment->payment_status,
                'cancellation_type' => 'admin_cancel',
                'status_before_cancellation' => $appointment->status,
                'additional_services' => $appointment->additional_services,
                'payment_method' => $appointment->payment_method,
                'note' => $appointment->note,
                'cancellation_reason' => $request->input('cancellation_reason', 'Không có lý do cụ thể'),
            ]));

            // Xóa các bản ghi liên quan
            DB::table('checkins')->where('appointment_id', $appointment->id)->delete();
            DB::table('refund_requests')->where('appointment_id', $appointment->id)->delete();

            // Gửi email với dữ liệu từ CancelledAppointment
            Mail::to($cancelledAppointment->email)->queue(new AdminCancelBookingMail($cancelledAppointment));

            // Gắn trạng thái để payload broadcast hiển thị đúng
            $appointment->status = 'cancelled';
            $appointment->delete();

            event(new AppointmentStatusUpdated($appointment));

            // Lấy tab hiện tại từ request
            $currentTab = $request->input('current_tab', 'pending');

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hủy.',
                'redirect_url' => route('appointments.index', ['status' => $currentTab])
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi hủy lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Appointment $appointment)
    {
        // kiểm tra nếu không phải admin chi nhánh 1 thì không thể xem được chi tiết của chi nhánh 2
        if (Auth::user()->role === 'admin_branch' && Auth::user()->branch_id !== $appointment->branch_id) {
            return redirect()->route('appointments.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $appointment->load(['user', 'barber', 'service', 'branch', 'promotion']);
        $isCancelled = false;

        $otherBarberAppointments = Appointment::where('barber_id', $appointment->barber_id)
            ->where('id', '!=', $appointment->id)
            ->latest('appointment_time')
            ->limit(5)
            ->get();

        $otherUserAppointments = Appointment::where('user_id', $appointment->user_id)
            ->where('id', '!=', $appointment->id)
            ->latest('appointment_time')
            ->limit(5)
            ->get();

        $additionalServicesIds = json_decode($appointment->additional_services, true) ?? [];
        $additionalServices = Service::whereIn('id', $additionalServicesIds)
            ->withTrashed()
            ->get();
        $review = Review::where('appointment_id', $appointment->id)->first();

        return view('admin.appointments.show', compact(
            'appointment',
            'otherBarberAppointments',
            'otherUserAppointments',
            'isCancelled',
            'additionalServices',
            'review'
        ));
    }

    public function showCancelled(CancelledAppointment $cancelledAppointment)
    {
        $appointment = $cancelledAppointment;
        $appointment->load(['user', 'barber', 'service', 'branch', 'promotion']);
        $isCancelled = true;

        $additionalServicesIds = json_decode($appointment->additional_services, true) ?? [];
        $additionalServices = Service::whereIn('id', $additionalServicesIds)
            ->withTrashed()
            ->get();

        $otherBarberAppointments = Appointment::where('barber_id', $appointment->barber_id)
            ->latest('appointment_time')
            ->limit(5)
            ->get();

        $otherUserAppointments = Appointment::where('user_id', $appointment->user_id)
            ->latest('appointment_time')
            ->limit(5)
            ->get();

        return view('admin.appointments.show', compact(
            'appointment',
            'otherBarberAppointments',
            'otherUserAppointments',
            'isCancelled',
            'additionalServices'
        ));
    }

    public function create(Request $request)
    {
        $services = Service::select('id', 'name', 'price', 'duration', 'is_combo')->get();
        $branches = Branch::all();

        // Lấy ngày và giờ hiện tại làm mặc định
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentTime = Carbon::now()->ceilMinutes(5)->format('H:i'); // Làm tròn lên 15 phút gần nhất

        // Lấy danh sách mã giảm giá khả dụng của người dùng
        $vouchers = Auth::check() ? UserRedeemedVoucher::where('user_id', Auth::id())
            ->where('is_used', false)
            ->with('promotion')
            ->get()
            ->filter(function ($voucher) {
                $promotion = $voucher->promotion;
                return $promotion &&
                    $promotion->is_active &&
                    $promotion->quantity > 0 &&
                    now()->gte($promotion->start_date) &&
                    now()->lte($promotion->end_date);
            }) : collect();

        // Lấy voucher công khai
        $publicPromotions = Promotion::where(function ($q) {
            $q->whereNull('required_points')
                ->orWhere('required_points', 0);
        })
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get()
            ->filter(function ($promotion) {
                if (Auth::check() && $promotion->usage_limit !== null) {
                    $usage_count = Appointment::where('user_id', Auth::id())
                        ->where('promotion_id', $promotion->id)
                        ->whereIn('status', ['pending', 'unconfirmed', 'confirmed', 'completed', 'checked-in', 'progress', 'completed'])
                        ->count();
                    return $usage_count < $promotion->usage_limit;
                }
                return true;
            });

        // Logic hiển thị barber
        if ($request->filled('appointment_date') && $request->filled('appointment_time')) {
            $barbers = $this->getAvailableBarbers($request->appointment_date, $request->appointment_time);
        } elseif ($request->filled('branch_id')) {
            $barbers = Barber::select('id', 'name', 'avatar', 'rating_avg', 'skill_level')
                ->where('branch_id', $request->input('branch_id'))
                ->where('status', 'idle')
                ->get();
        } else {
            $barbers = Barber::select('id', 'name', 'avatar', 'rating_avg', 'skill_level')
                ->where('status', 'idle')
                ->get();
        }

        return view('admin.appointments.create', compact('barbers', 'services', 'branches', 'vouchers', 'publicPromotions', 'currentDate', 'currentTime'));
    }

    public function createAppointment(BookingAdminRequest $request)
    {
        try {
            // Phân tích ngày giờ cuộc hẹn
            $datetime = Carbon::parse($request->appointment_date . ' ' . $request->appointment_time . ':00');

            // Tính thời lượng và kiểm tra dịch vụ
            $durationData = $this->calculateAppointmentDuration($request, $request->service_id);
            $service = $durationData['service'];
            $totalDuration = $durationData['total_duration'];
            $additionalServicesInput = $durationData['additional_services'];

            // Kiểm tra trùng lặp lịch hẹn
            $appointments = Appointment::with('service')
                ->where('barber_id', $request->barber_id)
                ->where('branch_id', $request->branch_id)
                ->whereIn('status', ['pending', 'confirmed', 'pending_cancellation'])
                ->whereDate('appointment_time', $datetime->format('Y-m-d'))
                ->get();


            $start = $datetime;
            $end = $datetime->copy()->addMinutes($totalDuration);

            $conflict = $appointments->first(function ($appointment) use ($start, $end) {
                $appointmentStart = Carbon::parse($appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration ?? 0);

                return $start->lt($appointmentEnd) && $end->gt($appointmentStart);
            });

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thợ này đã có lịch hẹn trong khoảng thời gian này.'
                ], 422);
            }

            // Kiểm tra trùng lặp lịch hẹn
            $existingAppointment = Appointment::where('branch_id', $request->branch_id)
                ->where('barber_id', $request->barber_id)
                ->where('appointment_time', $datetime)
                ->whereIn('status', ['unconfirmed', 'pending'])
                ->first();

            if ($existingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khung giờ này đã có lịch hẹn đang chờ xác nhận hoặc đã được đặt. Vui lòng chọn khung giờ khác.',
                ], 422);
            }

            // Lấy thông tin người đặt
            $name = $request->name ?? 'không xác định';

            // Tính tổng giá trị lịch hẹn và xử lý voucher
            $voucherResult = $this->handleVoucher($request, $service);
            if ($voucherResult['error']) {
                return response()->json([
                    'success' => false,
                    'message' => $voucherResult['message']
                ], 422);
            }
            [$totalAmount, $discountAmount, $promotion, $redeemedVoucher, $additionalServices] = $voucherResult['data'];

            // Tạo lịch hẹn
            $appointment = Appointment::create([
                'appointment_code' => 'APP' . date('YmdHis') . strtoupper(Str::random(3)),
                'user_id' => Auth::id() ?? null,
                'barber_id' => $request->barber_id,
                'branch_id' => $request->branch_id,
                'service_id' => $request->service_id,
                'appointment_time' => $datetime,
                'duration' => $totalDuration,
                'status' => 'progress', // Trạng thái ban đầu là 'progress'
                'payment_status' => 'unpaid', // Trạng thái thanh toán ban đầu là 'unpaid'
                'payment_method' => 'cash',

                'note' => $request->note,
                'name' => $name,
                'promotion_id' => $promotion ? $promotion->id : null,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'additional_services' => json_encode($additionalServices),
            ]);

            // Xử lý voucher
            if ($promotion && $redeemedVoucher) {
                $this->appointmentService->applyPromotion($appointment, $redeemedVoucher);
            } elseif ($promotion) {
                $this->appointmentService->applyPromotion($appointment, null, $promotion);
            }

            // trả về JSON thành công
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lịch hẹn đã được tạo thành công!',
                    'appointment_id' => $appointment->id,
                ]);
            }

            return redirect()->route('appointments.index', ['page' => $request->page ?? 1])
                ->with('success', 'Lịch hẹn đã được tạo thành công!');
        } catch (QueryException $e) {
            // Lỗi duplicate key 1062
            if ($e->errorInfo[1] == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khung giờ này đã có người đặt. Vui lòng chọn khung giờ khác.'
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {

            session()->flash('error', 'Lỗi khi đặt lịch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đặt lịch: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function handleVoucher($request, $service)
    {
        $totalAmount = $service->price ?? 0;
        $additionalServices = json_decode($request->input('additional_services', '[]'), true) ?? [];
        $additionalServicesTotal = Service::whereIn('id', $additionalServices)->sum('price');
        $totalAmount += $additionalServicesTotal;

        $discountAmount = 0;
        $promotion = null;
        $redeemedVoucher = null;

        // Xử lý voucher
        if ($request->voucher_code) {
            $code = trim($request->voucher_code);
            $user_id = Auth::id();

            // Trường hợp voucher cá nhân
            $redeemedVoucher = UserRedeemedVoucher::whereHas('promotion', function ($q) use ($code) {
                $q->where('code', $code);
            })
                ->where('user_id', $user_id)
                ->where('is_used', false)
                ->first();

            if ($redeemedVoucher) {
                $promotion = $redeemedVoucher->promotion;

                // Kiểm tra usage_limit
                $usage_count = Appointment::where('user_id', $user_id)
                    ->where('promotion_id', $promotion->id)
                    ->whereIn('status', ['pending', 'confirmed', 'completed'])
                    ->count();

                if ($promotion->usage_limit !== null && $usage_count >= $promotion->usage_limit) {
                    session()->flash('error', 'Bạn đã sử dụng voucher này quá số lần cho phép.');
                    return [
                        'error' => true,
                        'message' => 'Bạn đã sử dụng voucher này quá số lần cho phép.'
                    ];
                }

                // Kiểm tra min_order_value
                if ($promotion->min_order_value !== null && $totalAmount < $promotion->min_order_value) {
                    session()->flash('error', "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher.");
                    return [
                        'error' => true,
                        'message' => "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher."
                    ];
                }

                if ($promotion->discount_type === 'fixed') {
                    $discountAmount = $promotion->discount_value;
                } else {
                    $discountAmount = $totalAmount * $promotion->discount_value / 100;
                }
                $totalAmount -= $discountAmount;
            } else {
                // Trường hợp voucher công khai
                $promotion = Promotion::where('code', $code)
                    ->where(function ($q) {
                        $q->whereNull('required_points')->orWhere('required_points', 0);
                    })
                    ->where('is_active', true)
                    ->where('quantity', '>', 0)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($promotion) {
                    // Kiểm tra usage_limit
                    $usage_count = Appointment::where('user_id', $user_id)
                        ->where('promotion_id', $promotion->id)
                        ->whereIn('status', ['pending', 'unconfirmed', 'confirmed', 'completed', 'checked-in', 'progress', 'completed'])
                        ->count();

                    if ($promotion->usage_limit !== null && $usage_count >= $promotion->usage_limit) {
                        session()->flash('error', 'Bạn đã sử dụng voucher công khai này quá số lần cho phép.');
                        return [
                            'error' => true,
                            'message' => 'Bạn đã sử dụng voucher công khai này quá số lần cho phép.'
                        ];
                    }

                    // Kiểm tra min_order_value
                    if ($promotion->min_order_value !== null && $totalAmount < $promotion->min_order_value) {
                        session()->flash('error', "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher.");
                        return [
                            'error' => true,
                            'message' => "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher."
                        ];
                    }

                    if ($promotion->discount_type === 'fixed') {
                        $discountAmount = $promotion->discount_value;
                    } else {
                        $discountAmount = $totalAmount * $promotion->discount_value / 100;
                    }
                    $totalAmount -= $discountAmount;
                } else {
                    session()->flash('error', 'Mã voucher không tồn tại hoặc đã hết hạn.');
                    return [
                        'error' => true,
                        'message' => 'Mã voucher không tồn tại hoặc đã hết hạn.'
                    ];
                }
            }
        }

        return [
            'error' => false,
            'data' => [$totalAmount, $discountAmount, $promotion, $redeemedVoucher, $additionalServices]
        ];
    }

    function calculateAppointmentDuration(Request $request, $service_id)
    {
        // Kiểm tra dịch vụ chính
        $service = Service::findOrFail($service_id);
        $mainDuration = $service->duration ?? 0;

        // Kiểm tra dịch vụ bổ sung
        $additionalServices = $request->additional_services ? json_decode($request->additional_services, true) : [];
        $additionalDuration = 0;
        if (!empty($additionalServices)) {
            $additionalServicesData = Service::whereIn('id', $additionalServices)->get();
            if (count($additionalServicesData) !== count($additionalServices)) {
                throw new \Exception('Một hoặc nhiều dịch vụ bổ sung không hợp lệ.');
            }
            $additionalDuration = $additionalServicesData->sum('duration');
        }

        $totalDuration = $mainDuration + $additionalDuration;

        return [
            'service' => $service,
            'total_duration' => $totalDuration,
            'additional_services' => $additionalServices
        ];
    }

    public function getAvailableBarbersByDate(Request $request, $branch_id = 'null', $date = 'null', $time = 'null', $service_id = 'null')
    {
        try {
            // Kiểm tra chi nhánh
            if ($branch_id !== 'null' && (!is_numeric($branch_id) || !Branch::find($branch_id))) {
                return response()->json(['error' => 'Invalid branch ID'], 400);
            }

            // Kiểm tra và xử lý ngày
            $parsedDate = null;
            if ($date !== 'null') {
                try {
                    $parsedDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid date format'], 400);
                }
            }

            // Kiểm tra và xử lý giờ
            $parsedTime = null;
            $datetime = null;
            if ($time !== 'null') {
                try {
                    $decodedTime = urldecode($time); // Giải mã 08%3A00 thành 08:00
                    $parsedTime = Carbon::createFromFormat('H:i', $decodedTime)->format('H:i');
                    $datetime = $parsedDate ? Carbon::parse("{$parsedDate} {$parsedTime}:00") : null;
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid time format'], 400);
                }
            }

            // Kiểm tra dịch vụ chính
            $mainDuration = 0;
            if ($service_id !== 'null') {
                $srv = Service::find($service_id);
                if (!$srv) {
                    return response()->json(['error' => 'Invalid service ID'], 400);
                }
                $mainDuration = $srv->duration ?? 0;
            }

            // Kiểm tra dịch vụ bổ sung
            $additionalIds = $request->query('additional_services', []);
            if (is_string($additionalIds)) {
                try {
                    $additionalIds = json_decode($additionalIds, true) ?? [];
                    if (!is_array($additionalIds)) {
                        return response()->json(['error' => 'Invalid additional services format'], 400);
                    }
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Invalid additional services format'], 400);
                }
            }

            $additionalDuration = 0;
            if (!empty($additionalIds)) {
                $additionalServices = Service::whereIn('id', $additionalIds)->get();
                if (count($additionalServices) !== count($additionalIds)) {
                    return response()->json(['error' => 'One or more additional service IDs are invalid'], 400);
                }
                $additionalDuration = $additionalServices->sum('duration');
            }
            $totalDuration = $mainDuration + $additionalDuration;

            // Xây dựng query cơ bản
            $query = Barber::query()
                ->select('barbers.id', 'barbers.name', 'barbers.avatar', 'barbers.rating_avg', 'barbers.skill_level')
                ->where('status', 'idle');

            // Lọc theo chi nhánh nếu có
            if ($branch_id !== 'null') {
                $query->where('branch_id', $branch_id);
            }

            // Kiểm tra lịch nghỉ và lịch làm việc của thợ
            if ($parsedDate) {
                $query->whereDoesntHave('schedules', function ($q) use ($parsedDate, $parsedTime, $datetime, $totalDuration) {
                    $q->where('schedule_date', $parsedDate)
                        ->where(function ($scheduleQuery) use ($parsedTime, $datetime, $totalDuration) {
                            // Lịch nghỉ toàn hệ thống (holiday)
                            $scheduleQuery->where('status', 'holiday')
                                // Lịch nghỉ cá nhân (off)
                                ->orWhere('status', 'off')
                                // Lịch làm việc tùy chỉnh (custom) - kiểm tra thời gian
                                ->orWhere(function ($customQuery) use ($parsedTime, $datetime, $totalDuration) {
                                    $customQuery->where('status', 'custom')
                                        ->where(function ($timeQuery) use ($parsedTime, $datetime, $totalDuration) {
                                            if ($parsedTime && $totalDuration) {
                                                $appointmentEnd = $datetime->copy()->addMinutes($totalDuration);
                                                $appointmentEndTime = $appointmentEnd->format('H:i:s');

                                                // Kiểm tra xung đột thời gian - thợ không có lịch làm việc trong khoảng thời gian này
                                                $timeQuery->where(function ($tq) use ($parsedTime, $appointmentEndTime) {
                                                    // Thợ bắt đầu làm việc sau khi lịch hẹn kết thúc
                                                    $tq->where('start_time', '>', $appointmentEndTime)
                                                        // Thợ kết thúc làm việc trước khi lịch hẹn bắt đầu
                                                        ->orWhere('end_time', '<', $parsedTime);
                                                });
                                            }
                                        });
                                });
                        });
                });
            }

            // Kiểm tra xung đột lịch hẹn
            if ($datetime && $totalDuration) {
                $appointmentEnd = $datetime->copy()->addMinutes($totalDuration);
                $query->whereDoesntHave('appointments', function ($q) use ($datetime, $appointmentEnd, $parsedDate) {
                    $q->whereIn('status', ['pending', 'confirmed', 'progress', 'completed', 'checked-in', 'unconfirmed'])
                        ->whereDate('appointment_time', $parsedDate)
                        ->where(function ($q2) use ($datetime, $appointmentEnd) {
                            // Kiểm tra mọi trường hợp chồng lấn
                            $q2->whereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL COALESCE(duration, 0) MINUTE)', [$datetime])
                                ->orWhereRaw('? BETWEEN appointment_time AND DATE_ADD(appointment_time, INTERVAL COALESCE(duration, 0) MINUTE)', [$appointmentEnd])
                                ->orWhereRaw('appointment_time BETWEEN ? AND ?', [$datetime, $appointmentEnd]);
                        });
                });
            }

            $barbers = $query->get();
            return response()->json($barbers);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Server error: Unable to fetch barbers'], 500);
        }
    }

    public function getAvailableBarbers($date, $time)
    {
        $datetime = Carbon::parse($date . ' ' . $time);
        $parsedDate = $datetime->format('Y-m-d');
        $parsedTime = $datetime->format('H:i:s');

        // Lấy danh sách barber KHÔNG có lịch hẹn vào thời điểm này
        $availableBarbers = Barber::select('id', 'name', 'avatar', 'rating_avg', 'skill_level')->where('status', 'idle')
            ->whereDoesntHave('appointments', function ($query) use ($datetime) {
                $query->where('appointment_time', $datetime)
                    ->whereIn('status', ['pending', 'confirmed']); // chỉ tính lịch chưa bị hủy
            })
            ->whereDoesntHave('schedules', function ($query) use ($parsedDate, $parsedTime) {
                $query->where('schedule_date', $parsedDate)
                    ->where(function ($scheduleQuery) use ($parsedTime) {
                        // Lịch nghỉ toàn hệ thống (holiday)
                        $scheduleQuery->where('status', 'holiday')
                            // Lịch nghỉ cá nhân (off)
                            ->orWhere('status', 'off')
                            // Lịch làm việc tùy chỉnh (custom) - kiểm tra thời gian
                            ->orWhere(function ($customQuery) use ($parsedTime) {
                                $customQuery->where('status', 'custom')
                                    ->where(function ($timeQuery) use ($parsedTime) {
                                        // Thợ bắt đầu làm việc sau thời gian lịch hẹn
                                        $timeQuery->where('start_time', '>', $parsedTime)
                                            // Thợ kết thúc làm việc trước thời gian lịch hẹn
                                            ->orWhere('end_time', '<', $parsedTime);
                                    });
                            });
                    });
            })
            ->get();
        return $availableBarbers;
    }

    public function edit(Appointment $appointment)
    {
        // Kiểm tra quyền truy cập và chi nhánh
        if (Auth::user()->role === 'admin_branch' && Auth::user()->branch_id !== $appointment->branch_id) {
            return redirect()->route('appointments.index')->with('error', 'Bạn không có quyền truy cập.');
        }

        $appointments = Appointment::all();
        $services = Service::all();
        $barbers = Barber::all();
        $branches = Branch::all();

        // Lấy danh sách mã giảm giá khả dụng của người dùng
        $vouchers = Auth::check() ? UserRedeemedVoucher::where('user_id', Auth::id())
            ->where('is_used', false)
            ->with('promotion')
            ->get()
            ->filter(function ($voucher) {
                $promotion = $voucher->promotion;
                return $promotion &&
                    $promotion->is_active &&
                    $promotion->quantity > 0 &&
                    now()->gte($promotion->start_date) &&
                    now()->lte($promotion->end_date);
            }) : collect();

        // Lấy voucher công khai
        $publicPromotions = Promotion::where(function ($q) {
            $q->whereNull('required_points')
                ->orWhere('required_points', 0);
        })
            ->where('is_active', true)
            ->where('quantity', '>', 0)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get()
            ->filter(function ($promotion) {
                if (Auth::check() && $promotion->usage_limit !== null) {
                    $usage_count = Appointment::where('user_id', Auth::id())
                        ->where('promotion_id', $promotion->id)
                        ->whereIn('status', ['pending', 'unconfirmed', 'confirmed', 'completed', 'checked-in', 'progress', 'completed'])
                        ->count();
                    return $usage_count < $promotion->usage_limit;
                }
                return true;
            });

        return view('admin.appointments.edit', compact('appointment', 'services', 'barbers', 'branches', 'appointments', 'vouchers', 'publicPromotions'));
    }

    public function update(BookingAdminRequest $request, Appointment $appointment)
    {
        try {
            $newStatus = $request->status;
            $newPaymentStatus = $request->payment_status;

            $serviceId = $request->input('service_id');
            $additionalServices = json_decode($request->input('additional_services', '[]'), true) ?? [];

            $datetime = Carbon::parse($request->appointment_time);

            // Tính thời lượng và kiểm tra dịch vụ
            $durationData = $this->calculateAppointmentDuration($request, $request->service_id);
            // $service = $durationData['service'];
            $totalDuration = $durationData['total_duration'];
            // $additionalServicesInput = $durationData['additional_services'];

            // Kiểm tra trùng lặp lịch hẹn
            $appointments = Appointment::with('service')
                ->where('barber_id', $request->barber_id)
                ->where('branch_id', $request->branch_id)
                ->whereIn('status', ['pending', 'confirmed', 'pending_cancellation'])
                ->whereDate('appointment_time', $datetime->format('Y-m-d'))
                ->get();


            $start = $datetime;
            $end = $datetime->copy()->addMinutes($totalDuration);

            $conflict = $appointments->first(function ($appointment) use ($start, $end) {
                $appointmentStart = Carbon::parse($appointment->appointment_time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration ?? 0);

                return $start->lt($appointmentEnd) && $end->gt($appointmentStart);
            });

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thợ này đã có lịch hẹn trong khoảng thời gian này.'
                ], 422);
            }

            // Kiểm tra trùng lặp lịch hẹn
            $existingAppointment = Appointment::where('branch_id', $request->branch_id)
                ->where('barber_id', $request->barber_id)
                ->where('appointment_time', $datetime)
                ->whereIn('status', ['unconfirmed', 'pending'])
                ->first();

            if ($existingAppointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khung giờ này đã có lịch hẹn đang chờ xác nhận hoặc đã được đặt. Vui lòng chọn khung giờ khác.',
                ], 422);
            }

            // Kiểm tra xem có lịch hẹn nào khác trùng thời gian không
            $conflictingAppointment = Appointment::where('id', '!=', $appointment->id)
                ->where('barber_id', $appointment->barber_id)
                ->where('branch_id', $appointment->branch_id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request) {
                    $appointmentTime = \Carbon\Carbon::parse($request->appointment_time);
                    $endTime = $appointmentTime->copy()->addMinutes(30);

                    $query->where(function ($q) use ($appointmentTime, $endTime) {
                        $q->where('appointment_time', '>=', $appointmentTime)
                            ->where('appointment_time', '<', $endTime);
                    })->orWhere(function ($q) use ($appointmentTime, $endTime) {
                        $q->where('appointment_time', '<=', $appointmentTime)
                            ->where(DB::raw('DATE_ADD(appointment_time, INTERVAL 30 MINUTE)'), '>', $appointmentTime);
                    });
                })
                ->first();

            if ($conflictingAppointment) {
                return redirect()->back()
                    ->with('error', 'Thời gian này đã có lịch hẹn khác với barber và chi nhánh này. Vui lòng chọn thời gian khác.')
                    ->withInput();
            }

            $appointment->update([
                'service_id' => $serviceId,
                'additional_services' => json_encode($additionalServices),
                'appointment_time' => $request->appointment_time,
                'status' => $newStatus,
                'payment_status' => $newPaymentStatus,
            ]);

            $mainService = Service::find($serviceId);
            $additionalServiceTotal = Service::whereIn('id', $additionalServices)->sum('price');
            $totalAmount = ($mainService->price ?? 0) + $additionalServiceTotal - ($appointment->discount_amount ?? 0);
            $appointment->update(['total_amount' => $totalAmount]);

            $currentPage = $request->input('page', 1);

            if ($newStatus === 'confirmed') {
                $qrCode = rand(100000, 999999);
                Checkin::create([
                    'appointment_id' => $appointment->id,
                    'qr_code_value' => $qrCode,
                    'is_checked_in' => false,
                    'checkin_time' => null,
                ]);

                $additionalServicesNames = !empty($additionalServices)
                    ? Service::whereIn('id', $additionalServices)->pluck('name')->toArray()
                    : [];

                event(new AppointmentStatusUpdated($appointment));

                $checkin = Checkin::where('appointment_id', $appointment->id)->first();
                Mail::to($appointment->email)->queue(new CheckinCodeMail($checkin->qr_code_value, $appointment, $additionalServicesNames));
            }

            if ($newStatus === 'cancelled') {
                $appointmentData = $appointment->toArray();
                $appointmentData['appointment_time'] = $appointment->appointment_time
                    ? Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s')
                    : null;

                CancelledAppointment::create(array_merge($appointmentData, [
                    'status' => 'cancelled',
                    'payment_status' => $appointment->payment_status,
                    'cancellation_type' => 'admin_cancel', // Thay vì 'no-show' để phân biệt với no-show
                    'status_before_cancellation' => $appointment->status,
                    'additional_services' => $appointment->additional_services,
                    'payment_method' => $appointment->payment_method,
                    'note' => $appointment->note,
                    'cancellation_reason' => $request->input('cancellation_reason', 'Không có lý do cụ thể'), // Sửa từ no_show_reason
                ]));

                DB::table('checkins')->where('appointment_id', $appointment->id)->delete();
                DB::table('refund_requests')->where('appointment_id', $appointment->id)->delete();

                Mail::to($appointmentData['email'])->queue(new AdminCancelBookingMail((object) $appointmentData));

                $appointment->delete();

                event(new AppointmentStatusUpdated($appointment));
            }

            // Nếu trạng thái là 'completed', gửi email thông báo
            if ($appointment->status === 'completed') {
                $appointment->payment_status = 'paid';
                $appointment->save();
                // Mail::to($appointment->email)->send(new CompleteBookingMail($appointment));
            }

            // Lấy tab hiện tại từ request
            $currentTab = $request->input('current_tab', $appointment->status);

            // trả về JSON thành công
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lịch hẹn đã được cập nhật thành công!',
                    'appointment_id' => $appointment->id,
                    'redirect_url' => route('appointments.index', ['status' => $currentTab])
                ]);
            }

            // trả về trang đặt lịch tab nếu sửa sang trạng thái nào thì sẽ vào tab đó và có thông báo thành công
            return redirect()->route('appointments.index', ['status' => $newStatus, 'page' => $currentPage])
                ->with('success', 'Lịch hẹn ' . $appointment->appointment_code . ' đã được cập nhật.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Lỗi khi cập nhật lịch hẹn: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        try {
            if ($appointment->status === 'cancelled') {
                return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được hủy trước đó.');
            }

            if (!$request->input('cancellation_reason')) {
                return redirect()->route('appointments.index')
                    ->with('error', 'Vui lòng cung cấp lý do hủy.');
            }

            $appointmentData = $appointment->toArray();
            $appointmentData['appointment_time'] = $appointment->appointment_time
                ? Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s')
                : null;

            // Tạo bản ghi CancelledAppointment
            $cancelledAppointment = CancelledAppointment::create(array_merge($appointmentData, [
                'status' => 'cancelled',
                'payment_status' => $appointment->payment_status,
                'cancellation_type' => 'admin_cancel',
                'status_before_cancellation' => $appointment->status,
                'additional_services' => $appointment->additional_services,
                'payment_method' => $appointment->payment_method,
                'note' => $appointment->note,
                'cancellation_reason' => $request->input('cancellation_reason', 'Không có lý do cụ thể'),
            ]));

            // Xóa các bản ghi liên quan
            DB::table('checkins')->where('appointment_id', $appointment->id)->delete();
            DB::table('refund_requests')->where('appointment_id', $appointment->id)->delete();

            // Gửi email với dữ liệu từ CancelledAppointment
            Mail::to($cancelledAppointment->email)->queue(new AdminCancelBookingMail($cancelledAppointment));

            // Gắn trạng thái để payload broadcast hiển thị đúng
            $appointment->status = 'cancelled';
            $appointment->delete();

            event(new AppointmentStatusUpdated($appointment));

            // Lấy tab hiện tại từ request
            $currentTab = $request->input('current_tab', 'pending');

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được huỷ.',
                'redirect_url' => route('appointments.index', ['status' => $currentTab])
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return redirect()->route('appointments.index')
                ->with('error', 'Lỗi khi hủy lịch hẹn: ' . $e->getMessage());
        }
    }
}
