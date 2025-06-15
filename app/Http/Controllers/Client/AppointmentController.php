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
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BookingRequest;
use App\Models\UserRedeemedVoucher;
use App\Services\AppointmentService;
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
        $services = Service::all();
        $branches = Branch::all();

        // Lấy danh sách mã giảm giá khả dụng của người dùng
        $vouchers = Auth::check() ? UserRedeemedVoucher::where('user_id', Auth::id())
            ->where('is_used', false)
            ->with('promotion')
            ->get() : collect();

        // Mặc định: hiển thị tất cả barber nếu chưa chọn thời gian
        if ($request->filled('appointment_date') && $request->filled('appointment_time')) {
            $barbers = $this->getAvailableBarbers($request->appointment_date, $request->appointment_time);
        } else {
            $barbers = Barber::all();
        }

        return view('client.booking', compact('barbers', 'services', 'branches', 'vouchers'));
    }

    public function getBarbersByBranch($branch_id)
    {
        $barbers = Barber::where('branch_id', $branch_id)->get();
        return response()->json($barbers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('dat-lich')->with('mustLogin', true);
        }

        $validator = Validator::make($request->all(), [
            'barber_id' => 'required|exists:barbers,id',
            'branch_id' => 'required|exists:branches,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'voucher_id' => 'nullable|exists:user_redeemed_vouchers,id,user_id,' . Auth::id(),
            'appointment_time' => [
                'required',
                'regex:/^([01]\d|2[0-3]):([0-5]\d)$/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value < '08:00' || $value > '20:00') {
                        $fail('Giờ đặt lịch phải từ 08:00 đến 20:00.');
                    }

                    $date = $request->input('appointment_date');
                    if ($date === now()->toDateString() && $value < now()->format('H:i')) {
                        $fail('Không thể đặt lịch ở thời điểm đã qua.');
                    }
                }
            ],
            'name' => 'nullable|string|max:100|required_if:other_person,1',
            'phone' => [
                'nullable',
                'required_if:other_person,1',
                'regex:/^0[0-9]{9}$/'
            ],
        ], [
            'barber_id.required' => 'Vui lòng chọn thợ cắt.',
            'barber_id.exists' => 'Thợ cắt không tồn tại.',

            'branch_id.required' => 'Vui lòng chọn chi nhánh đặt lịch.',
            'branch_id.exists' => 'Chi nhánh không tồn tại.',

            'service_id.required' => 'Vui lòng chọn dịch vụ.',
            'service_id.exists' => 'Dịch vụ không tồn tại.',

            'appointment_date.required' => 'Vui lòng chọn ngày đặt lịch.',
            'appointment_date.date' => 'Ngày đặt lịch không hợp lệ.',
            'appointment_date.after_or_equal' => 'Ngày đặt lịch phải là hôm nay hoặc sau.',

            'appointment_time.required' => 'Vui lòng chọn thời gian đặt lịch.',
            'appointment_time.regex' => 'Thời gian phải có định dạng HH:MM (ví dụ: 14:30).',

            'name.string' => 'Tên không hợp lệ.',
            'name.max' => 'Tên quá dài (tối đa 100 ký tự).',

            'required_if' => 'Vui lòng nhập :attribute khi đặt cho người khác.',
            'phone.regex' => 'Số điện thoại không hợp lệ. Phải có 10 chữ số và bắt đầu bằng 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Lấy tên và số điện thoại
        $name = $request->filled('other_person') ? $request->name : Auth::user()->name;
        $phone = $request->filled('other_person') ? $request->phone : Auth::user()->phone;

        $appointment = new Appointment();
        $appointment->appointment_code = strtoupper(Str::random(8));
        $appointment->user_id = Auth::id();
        $appointment->barber_id = $request->barber_id;
        $appointment->branch_id = $request->branch_id;
        $appointment->service_id = $request->service_id;
        $appointment->appointment_time = $request->appointment_date . ' ' . $request->appointment_time;
        $appointment->status = 'pending';
        $appointment->payment_status = 'unpaid';
        $appointment->note = $request->note;
        $appointment->name = $name;
        $appointment->phone = $phone;
        $appointment->save();

        // Áp dụng mã giảm giá nếu có
        if ($request->voucher_id) {
            $voucher = UserRedeemedVoucher::where('id', $request->voucher_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            $this->appointmentService->applyPromotion($appointment, $voucher);
        }
        
        $code = rand(100000, 999999);
        Checkin::create([
            'appointment_id' => $appointment->id,
            'qr_code_value' => $code,
            'is_checked_in' => false,
            'checkin_time' => null,
        ]);

        Mail::to(Auth::user()->email)->send(new CheckinCodeMail($code, $appointment));

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
