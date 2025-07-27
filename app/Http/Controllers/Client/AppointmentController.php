<?php

namespace App\Http\Controllers\Client;

use Pusher\Pusher;
use App\Models\Barber;
use App\Models\Branch;
use App\Models\Review;
use App\Models\Checkin;
use App\Models\Service;
use App\Models\Promotion;
use App\Models\Appointment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use App\Events\NewAppointment;
use Illuminate\Support\Carbon;
use App\Mail\ConfirmBookingMail;
use App\Mail\PendingBookingMail;
use App\Events\AppointmentCreated;
use Illuminate\Support\Facades\DB;
use App\Models\UserRedeemedVoucher;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\CancelledAppointment;
use App\Services\AppointmentService;
use Illuminate\Pagination\Paginator;
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
            ->get()
            ->filter(function ($promotion) {
                // Kiểm tra số lần sử dụng của voucher công khai cho người dùng hiện tại
                if (Auth::check() && $promotion->usage_limit !== null) {
                    $usage_count = Appointment::where('user_id', Auth::id())
                        ->where('promotion_id', $promotion->id)
                        ->whereIn('status', ['pending', 'confirmed', 'completed'])
                        ->count();
                    return $usage_count < $promotion->usage_limit;
                }
                return true; // Hiển thị nếu không có giới hạn hoặc người dùng chưa đăng nhập
            });

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

        // Hàm xây dựng truy vấn chung - loại trừ lịch hẹn chưa xác nhận
        $buildQuery = function ($query, $search, $status) {
            $query->with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name'])
                ->where('status', '!=', 'unconfirmed') // Loại trừ lịch hẹn chưa xác nhận
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
                    } elseif ($status !== 'unconfirmed') { // Không cho phép filter theo unconfirmed
                        $q->where('status', $status);
                    }
                });
        };

        // Nếu có tìm kiếm hoặc lọc trạng thái, lấy tất cả lịch hẹn từ cả hai bảng
        if ($search || $status) {
            // Lấy từ bảng appointments - loại trừ unconfirmed
            $appointmentQuery = Appointment::where('user_id', $userId)
                ->where('status', '!=', 'unconfirmed'); // Loại trừ lịch hẹn chưa xác nhận
            $buildQuery($appointmentQuery, $search, $status);
            $appointmentsResult = $appointmentQuery->get();

            // Đảm bảo không có lịch hẹn unconfirmed trong kết quả
            $appointmentsResult = $appointmentsResult->filter(function ($appointment) {
                return $appointment->status !== 'unconfirmed';
            });

            // Lấy từ bảng cancelled_appointments
            $canceledResult = collect();
            if (!$status || in_array($status, ['canceled', 'cancelled'])) {
                $canceledResult = $this->getCancelledAppointments($userId, $search);
            }

            // Nếu status là unconfirmed, không lấy kết quả nào
            if ($status === 'unconfirmed') {
                $appointmentsResult = collect();
                $canceledResult = collect();
            }


            // Kết hợp kết quả
            $allAppointments = $appointmentsResult->merge($canceledResult)
                ->sortByDesc('updated_at')
                ->take(5);
        } else {
            // Lấy lịch hẹn từ cả hai bảng mà không lọc - loại trừ unconfirmed
            $appointmentQuery = Appointment::where('user_id', $userId)
                ->where('status', '!=', 'unconfirmed'); // Loại trừ lịch hẹn chưa xác nhận
            $buildQuery($appointmentQuery, null, null);
            $appointmentsResult = $appointmentQuery->get();

            $canceledQuery = CancelledAppointment::where('user_id', $userId);
            $buildQuery($canceledQuery, null, null);
            $canceledResult = $canceledQuery->get();

            // Đảm bảo không có lịch hẹn unconfirmed trong kết quả
            $appointmentsResult = $appointmentsResult->filter(function ($appointment) {
                return $appointment->status !== 'unconfirmed';
            });

            $allAppointments = $appointmentsResult->merge($canceledResult)
                ->sortByDesc('updated_at')
                ->values();
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

    protected function getCancelledAppointments($userId, $search = null)
    {
        $query = CancelledAppointment::with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name'])
            ->where('user_id', $userId);

        // Tìm kiếm theo mã lịch, tên dịch vụ hoặc tên thợ
        if ($search) {
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

        // Lấy kết quả và gán status ảo để hiển thị đồng nhất
        return $query->get()->each(function ($item) {
            $item->status = 'cancelled'; // Gắn status giả để hiển thị lọc/trạng thái
        });
    }

    public function showCancelledAppointment($id)
    {
        $appointment = CancelledAppointment::with([
            'user:id,name,email,phone',
            'barber:id,name',
            'service:id,name,price',
            'branch:id,name,address'
        ])->where('user_id', Auth::id())
            ->findOrFail($id);


        $additionalServicesIds = json_decode($appointment->additional_services, true) ?? [];
        $additionalServices = Service::whereIn('id', $additionalServicesIds)
            ->withTrashed() // cho phép lấy cả dịch vụ đã xóa mềm
            ->get();

        return view('client.detailAppointmentHistory', compact('appointment', 'additionalServices'));
    }

    public function detailAppointmentHistory($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())
            ->where('status', '!=', 'unconfirmed') // Không cho phép xem chi tiết lịch hẹn chưa xác nhận
            ->with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name', 'review'])
            ->findOrFail($id);


        $additionalServicesIds = json_decode($appointment->additional_services, true) ?? [];
        $additionalServices = Service::whereIn('id', $additionalServicesIds)
            ->withTrashed() // cho phép lấy cả dịch vụ đã xóa mềm
            ->get();

        return view('client.detailAppointmentHistory', compact('appointment', 'additionalServices'));
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        try {
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

            // Kiểm tra xem lịch hẹn đã được hủy trước đó chưa
            if (CancelledAppointment::where('appointment_code', $appointment->appointment_code)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này đã được hủy trước đó.'
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
            $appointmentData = array_merge($appointment->toArray(), [
                'status' => 'cancelled',
                'payment_status' => $appointment->payment_status,
                'cancellation_type' => 'canceled',
                'status_before_cancellation' => $appointment->status,
                'additional_services' => $appointment->additional_services,
                'payment_method' => $appointment->payment_method,
                'note' => $appointment->note ?? null,
                'cancellation_reason' => $request->input('cancellation_reason'),
                'appointment_time' => $appointment->appointment_time ? $appointment->appointment_time->format('Y-m-d H:i:s') : null,
            ]);

            Log::info('appointmentData', $appointmentData);

            // Lưu bản ghi vào cancelled_appointments
            CancelledAppointment::create($appointmentData);

            // Xóa bản ghi liên quan trong bảng checkins
            $checkinsDeleted = DB::table('checkins')->where('appointment_id', $appointment->id)->delete();
            Log::info('Checkins deleted', ['appointment_id' => $appointment->id, 'rows_affected' => $checkinsDeleted]);

            // Xóa bản ghi khỏi bảng appointments
            $appointmentDeleted = $appointment->delete();
            Log::info('Appointment deleted', ['appointment_id' => $appointment->id, 'success' => $appointmentDeleted]);

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hủy.'
            ]);
        } catch (\Exception $e) {
            Log::error('Cancellation error: ' . $e->getMessage(), ['appointment' => $appointment->toArray()]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi máy chủ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmBooking($token)
    {
        try {
            $appointment = Appointment::where('confirmation_token', $token)->first();

            if (!$appointment || $appointment->status !== 'unconfirmed' || !$appointment->confirmation_token_expires_at || $appointment->confirmation_token_expires_at < now()) {
                $errorMessage = !$appointment ? 'Liên kết xác nhận không hợp lệ.' : ($appointment->status !== 'unconfirmed' ? 'Lịch hẹn đã được xác nhận hoặc bị hủy.' :
                    'Liên kết xác nhận đã hết hạn.');
                return redirect()->route('home')->with('error', $errorMessage);
            }

            // Cập nhật trạng thái và xóa token
            $appointment->status = 'pending';
            $appointment->confirmation_token = null;
            $appointment->confirmation_token_expires_at = null;
            $appointment->save();

            // Load quan hệ trước khi gửi email và Pusher
            $appointment->load(['barber', 'service']);

            // Chuyển hướng theo phương thức thanh toán
            if ($appointment->payment_method === 'vnpay') {
                return redirect()->route('client.payment.vnpay', ['appointment_id' => $appointment->id]);
            }

            // Gửi email thông báo pending
            Mail::to($appointment->email)->queue(new PendingBookingMail($appointment));

            // Gửi sự kiện NewAppointment
            event(new NewAppointment($appointment));

            // Kích hoạt Pusher
            $this->triggerPusher($appointment);

            return redirect()->route('dat-lich')->with('success', 'Lịch hẹn đã được xác nhận thành công!');
        } catch (\Exception $e) {
            Log::error('Confirmation error for token: ' . $token, [
                'error' => $e->getMessage(),
                'appointment_id' => $appointment ? $appointment->id : null,
            ]);
            return redirect()->route('home')->with('error', 'Có lỗi xảy ra khi xác nhận lịch hẹn.');
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
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã sử dụng voucher này quá số lần cho phép.'
                    ], 422);
                }

                // Kiểm tra min_order_value
                if ($promotion->min_order_value !== null && $totalAmount < $promotion->min_order_value) {
                    session()->flash('error', "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher.");
                    return response()->json([
                        'success' => false,
                        'message' => "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher."
                    ], 422);
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
                        ->whereIn('status', ['pending', 'confirmed', 'completed'])
                        ->count();

                    if ($promotion->usage_limit !== null && $usage_count >= $promotion->usage_limit) {
                        session()->flash('error', 'Bạn đã sử dụng voucher công khai này quá số lần cho phép.');
                        return response()->json([
                            'success' => false,
                            'message' => 'Bạn đã sử dụng voucher công khai này quá số lần cho phép.'
                        ], 422);
                    }

                    // Kiểm tra min_order_value
                    if ($promotion->min_order_value !== null && $totalAmount < $promotion->min_order_value) {
                        session()->flash('error', "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher.");
                        return response()->json([
                            'success' => false,
                            'message' => "Giá trị đơn hàng phải ít nhất " . number_format($promotion->min_order_value) . " VNĐ để áp dụng voucher."
                        ], 422);
                    }

                    if ($promotion->discount_type === 'fixed') {
                        $discountAmount = $promotion->discount_value;
                    } else {
                        $discountAmount = $totalAmount * $promotion->discount_value / 100;
                    }
                    $totalAmount -= $discountAmount;
                } else {
                    session()->flash('error', 'Mã voucher không tồn tại hoặc đã hết hạn.');
                    return response()->json([
                        'success' => false,
                        'message' => 'Mã voucher không tồn tại hoặc đã hết hạn.'
                    ], 422);
                }
            }
        }

        return [$totalAmount, $discountAmount, $promotion, $redeemedVoucher, $additionalServices];
    }

    protected function triggerPusher(Appointment $appointment)
    {
        $additionalServiceIds = json_decode($appointment->additional_services ?? '[]', true);
        $additionalServicesNames = Service::whereIn('id', $additionalServiceIds)->pluck('name')->toArray();

        $pusherData = [
            'id' => $appointment->id,
            'appointment_code' => $appointment->appointment_code,
            'user_name' => $appointment->name ?? 'N/A',
            'phone' => $appointment->phone ?? 'N/A',
            'barber_name' => $appointment->barber->name ?? 'N/A',
            'service_name' => $appointment->service->name ?? 'N/A',
            'status' => $appointment->status,
            'payment_status' => $appointment->payment_status,
            'created_at' => $appointment->created_at->format('d/m/Y H:i'),
            'additional_services' => $additionalServicesNames ?? [],
            'payment_method' => $appointment->payment_method,
            'appointment_time' => $appointment->appointment_time->format('d/m/Y H:i'),
        ];

        $pusher = new Pusher(
            config('broadcasting.connections.pusher.key'),
            config('broadcasting.connections.pusher.secret'),
            config('broadcasting.connections.pusher.app_id'),
            ['cluster' => config('broadcasting.connections.pusher.options.cluster'), 'useTLS' => true]
        );

        $pusher->trigger('appointments', 'App\\Events\\AppointmentCreated', $pusherData);
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
            $name = $request->other_person ? $request->name : Auth::user()->name;
            $phone = $request->other_person ? $request->phone : Auth::user()->phone;
            $email = $request->other_person ? $request->email : Auth::user()->email;

            // Tính tổng giá trị lịch hẹn và xử lý voucher
            [$totalAmount, $discountAmount, $promotion, $redeemedVoucher, $additionalServices] = $this->handleVoucher($request, $service);

            // Tạo lịch hẹn
            $appointment = Appointment::create([
                'appointment_code' => 'APP' . date('YmdHis') . strtoupper(Str::random(3)),
                'user_id' => Auth::id(),
                'barber_id' => $request->barber_id,
                'branch_id' => $request->branch_id,
                'service_id' => $request->service_id,
                'appointment_time' => $datetime,
                'duration' => $totalDuration,
                'status' => 'unconfirmed',
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
                'additional_services' => json_encode($additionalServices),
                'confirmation_token' => Str::random(60),
                'confirmation_token_expires_at' => now()->addMinutes(10),
            ]);

            // Giới hạn số lượng lịch hẹn chưa xác nhận
            $unconfirmedCount = Appointment::where('user_id', Auth::id())
                ->where('status', 'unconfirmed')
                ->count();
            if ($unconfirmedCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn có quá nhiều lịch hẹn chưa xác nhận. Vui lòng xác nhận hoặc hủy trước khi đặt thêm.'
                ], 422);
            }

            // Load quan hệ và xử lý dịch vụ bổ sung
            $appointment->load(['barber', 'service']);
            $AdditionalServiceIds = json_decode($appointment->additional_services, true) ?? [];
            $AdditionalServices = !empty($AdditionalServiceIds)
                ? Service::whereIn('id', $AdditionalServiceIds)->pluck('name')->toArray()
                : [];

            // Gửi email xác nhận với danh sách dịch vụ bổ sung
            Mail::to($appointment->email)->send(new ConfirmBookingMail($appointment, $AdditionalServices));

            // Phản hồi thành công
            $message = $request->payment_method === 'vnpay'
                ? 'Lịch hẹn đã được tạo. Vui lòng kiểm tra email để xác nhận và tiến hành thanh toán (trong vòng 10 phút kể từ khi đặt).'
                : 'Lịch hẹn đã được tạo. Vui lòng kiểm tra email để xác nhận (trong vòng 10 phút kể từ khi đặt).';

            // Xử lý voucher
            if ($promotion && $redeemedVoucher) {
                $this->appointmentService->applyPromotion($appointment, $redeemedVoucher);
            } elseif ($promotion) {
                $this->appointmentService->applyPromotion($appointment, null, $promotion);
            }


            // Phản hồi thành công
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }
            return redirect()->route('appointments.index')->with('success', 'Đặt lịch thành công!');
        } catch (\Exception $e) {
            Log::error('Booking error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Lỗi khi đặt lịch: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi đặt lịch: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getAvailableBarbersByDate(Request $request, $branch_id = 'null', $date = 'null', $time = 'null', $service_id = 'null')
    {
        try {
            // Log toàn bộ tham số để debug
            Log::info('getAvailableBarbersByDate called', [
                'branch_id' => $branch_id,
                'date' => $date,
                'time' => $time,
                'service_id' => $service_id,
                'additional_services' => $request->query('additional_services', [])
            ]);

            // Kiểm tra chi nhánh
            if ($branch_id !== 'null' && (!is_numeric($branch_id) || !Branch::find($branch_id))) {
                Log::warning('Invalid branch ID', ['branch_id' => $branch_id]);
                return response()->json(['error' => 'Invalid branch ID'], 400);
            }

            // Kiểm tra và xử lý ngày
            $parsedDate = null;
            if ($date !== 'null') {
                try {
                    $parsedDate = Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
                } catch (\Exception $e) {
                    Log::warning('Invalid date format', ['date' => $date, 'error' => $e->getMessage()]);
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
                    Log::warning('Invalid time format', ['time' => $time, 'decoded_time' => $decodedTime, 'error' => $e->getMessage()]);
                    return response()->json(['error' => 'Invalid time format'], 400);
                }
            }

            // Kiểm tra dịch vụ chính
            $mainDuration = 0;
            if ($service_id !== 'null') {
                $srv = Service::find($service_id);
                if (!$srv) {
                    Log::warning('Invalid service ID', ['service_id' => $service_id]);
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
                        Log::warning('Invalid additional services format', ['additional_services' => $additionalIds]);
                        return response()->json(['error' => 'Invalid additional services format'], 400);
                    }
                } catch (\Exception $e) {
                    Log::warning('Invalid additional services format', ['additional_services' => $additionalIds, 'error' => $e->getMessage()]);
                    return response()->json(['error' => 'Invalid additional services format'], 400);
                }
            }

            $additionalDuration = 0;
            if (!empty($additionalIds)) {
                $additionalServices = Service::whereIn('id', $additionalIds)->get();
                if (count($additionalServices) !== count($additionalIds)) {
                    Log::warning('Invalid additional service IDs', ['additional_ids' => $additionalIds]);
                    return response()->json(['error' => 'One or more additional service IDs are invalid'], 400);
                }
                $additionalDuration = $additionalServices->sum('duration');
            }
            $totalDuration = $mainDuration + $additionalDuration;

            // Log thời lượng để debug
            Log::info('Calculated duration', [
                'main_duration' => $mainDuration,
                'additional_duration' => $additionalDuration,
                'total_duration' => $totalDuration
            ]);

            // Xây dựng query cơ bản
            $query = Barber::query()
                ->select('barbers.id', 'barbers.name')
                ->where('status', 'idle');

            // Lọc theo chi nhánh nếu có
            if ($branch_id !== 'null') {
                $query->where('branch_id', $branch_id);
            }

            // Kiểm tra xung đột lịch hẹn
            if ($datetime && $totalDuration) {
                $appointmentEnd = $datetime->copy()->addMinutes($totalDuration);
                $query->whereDoesntHave('appointments', function ($q) use ($datetime, $appointmentEnd, $parsedDate) {
                    $q->whereIn('status', ['pending', 'confirmed'])
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
            Log::info('Barbers fetched successfully', ['barbers_count' => $barbers->count(), 'barbers' => $barbers->toArray()]);
            return response()->json($barbers);
        } catch (\Exception $e) {
            Log::error('Error in getAvailableBarbersByDate: ' . $e->getMessage(), [
                'branch_id' => $branch_id,
                'date' => $date,
                'time' => $time,
                'service_id' => $service_id,
                'additional_services' => $request->query('additional_services', []),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Server error: Unable to fetch barbers'], 500);
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

    // public function completeAppointment($appointmentId)
    // {
    //     try {
    //         $appointment = Appointment::findOrFail($appointmentId);
    //         $appointment->status = 'completed';
    //         $appointment->save();

    //         return response()->json(['message' => 'Lịch hẹn đã hoàn thành']);
    //     } catch (\Exception $e) {
    //         Log::error('Error in completeAppointment: ' . $e->getMessage());
    //         return response()->json(['error' => 'Server error'], 500);
    //     }
    // }
}
