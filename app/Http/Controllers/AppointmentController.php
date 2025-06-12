<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Checkin;
use Illuminate\Support\Facades\Mail;
use App\Mail\CheckinCodeMail;
class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $appointments = Appointment::with(['user:id,name', 'barber:id,name', 'service:id,name', 'branch:id,name'])
            ->when($search, function ($query, $search) {
                // tìm theo tên user và tên barber
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('barber', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('barber', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'DESC')
            ->paginate(5);

        // $appointmentsDebug = Appointment::with(['service','branch'])->take(1)->get();
        // dd($appointments->toArray());

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */


public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'service_id' => 'required|exists:services,id',
        'appointment_time' => 'required|date',
    ]);

    // Tạo lịch hẹn
    $appointment = Appointment::create([
        'user_id' => $request->user_id,
        'service_id' => $request->service_id,
        'appointment_time' => $request->appointment_time,
    ]);

    // Kiểm tra người dùng có email không
    if (!$appointment->user || !$appointment->user->email) {
        return redirect()->route('appointments.index')->withErrors(['email' => 'Không tìm thấy email khách hàng!']);
    }

    // Tạo mã checkin
    $code = rand(100000, 999999);

    Checkin::create([
        'appointment_id' => $appointment->id,
        'qr_code_value' => $code,
        'is_checked_in' => false,
        'checkin_time' => null,
    ]);

    // Gửi email mã check-in
    Mail::to($appointment->user->email)->send(new CheckinCodeMail($appointment, $code));

    return redirect()->route('appointments.index')->with('success', 'Đặt lịch thành công! Mã check-in đã được gửi qua email.');
}


    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['user', 'barber', 'service', 'branch', 'promotion']);

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

        return view('admin.appointments.show', compact(
            'appointment',
            'otherBarberAppointments',
            'otherUserAppointments'
        ));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $appointments = Appointment::all();
        return view('admin.appointments.edit', compact('appointment',));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
{
    $currentStatus       = $appointment->status;
    $newStatus           = $request->status;
    $currentPaymentStatus = $appointment->payment_status;
    $newPaymentStatus    = $request->payment_status;

    // 1. Nếu đã cancelled rồi thì không được phép chuyển sang bất cứ trạng thái nào khác
    if ($currentStatus === 'cancelled' && $newStatus !== 'cancelled') {
        return back()->withErrors([
            'status' => 'Lịch hẹn đã bị huỷ, không thể chuyển sang trạng thái khác.'
        ]);
    }

    // 2. Nếu đã completed rồi thì cũng không cho chỉnh sang pending/confirmed/cancelled
    if ($currentStatus === 'completed' && $newStatus !== 'completed') {
        return back()->withErrors([
            'status' => 'Lịch hẹn đã hoàn thành, không thể chuyển sang trạng thái khác.'
        ]);
    }

    // 3. Không cho chuyển từ confirmed về pending
    if ($currentStatus === 'confirmed' && $newStatus === 'pending') {
        return back()->withErrors([
            'status' => 'Không thể chuyển từ "Đã xác nhận" về "Chờ xác nhận".'
        ]);
    }

    // 4. Tất cả trường hợp quay về pending hoặc confirmed nếu đã ở completed/cancelled đều bị chặn
    if (in_array($currentStatus, ['completed', 'cancelled']) && in_array($newStatus, ['pending', 'confirmed'])) {
        return back()->withErrors([
            'status' => 'Không thể chuyển về trạng thái trước đó sau khi đã hoàn thành hoặc huỷ.'
        ]);
    }

    // 5. Nếu đã thanh toán hoàn toàn rồi (paid) và trạng thái lịch hẹn cũng đã completed, KHÔNG cho huỷ
    if ($currentPaymentStatus === 'paid' && $currentStatus === 'completed' && $newStatus === 'cancelled') {
        return back()->withErrors([
            'status' => 'Không thể huỷ lịch đã hoàn thành và đã thanh toán.'
        ]);
    }

    // 6. Nếu đang là payment_status = refunded, KHÔNG cho chỉnh status lẫn payment_status nữa
    if ($currentPaymentStatus === 'refunded') {
        // Nếu user vẫn cố tình submit payment_status khác (dù form đã disable), hoặc cố chuyển status
        if ($newPaymentStatus !== 'refunded') {
            return back()->withErrors([
                'payment_status' => 'Thanh toán đã hoàn trả, không thể thay đổi trạng thái thanh toán.'
            ]);
        }
        if ($newStatus !== $currentStatus) {
            return back()->withErrors([
                'status' => 'Lịch hẹn liên quan đã hoàn trả, không được phép thay đổi trạng thái lịch hẹn.'
            ]);
        }
    }

    // 7. Nếu payment_status hiện tại = 'paid'
    //    - Cho phép chuyển sang 'refunded' hoặc 'failed'
    //    - KHÔNG cho chuyển về 'unpaid'
    if ($currentPaymentStatus === 'paid' && $newPaymentStatus === 'unpaid') {
        return back()->withErrors([
            'payment_status' => 'Không thể chuyển từ "Thanh toán thành công" về "Chưa thanh toán".'
        ]);
    }

    // 8. Nếu payment_status hiện tại = 'failed'
    //    - Cho phép vào 'paid' hoặc 'refunded'
    //    - KHÔNG cho vào 'unpaid' (như trên)
    if ($currentPaymentStatus === 'failed' && $newPaymentStatus === 'unpaid') {
        return back()->withErrors([
            'payment_status' => 'Không thể chuyển từ "Thanh toán thất bại" về "Chưa thanh toán".'
        ]);
    }

    // 9. Nếu payment_status hiện tại = 'paid' hoặc 'failed'
    //    - Nếu cố tình chuyển xuống 'refunded' thì OK
    //    - Nếu cố tình chuyển status thành 'cancelled' rồi newPaymentStatus khác (ví dụ vẫn 'paid'), có thể gây conflict
    if ($newStatus === 'cancelled' && in_array($currentPaymentStatus, ['paid', 'refunded'])) {
        // Nếu chưa đồng bộ payment_status = 'failed' khi hủy, ta ép payment_status về 'failed'
        $newPaymentStatus = 'failed';
    }

    // 10. Không cho sửa payment_status = 'unpaid' nếu hiện tại đã khác 'unpaid'
    if ($newPaymentStatus === 'unpaid' && $currentPaymentStatus !== 'unpaid') {
        return back()->withErrors([
            'payment_status' => 'Không thể chuyển về trạng thái chưa thanh toán.'
        ]);
    }

    // --- Nếu qua hết các kiểm tra trên, mới cho phép update ---
    $appointment->update($request->only([
        'appointment_time',
        'status',
        'payment_status',
        'note'
    ]));

    // Giữ nguyên trang
    $currentPage = $request->input('page', 1);
    return redirect()->route('appointments.index', ['page' => $currentPage])
                     ->with('success', 'Cập nhật lịch hẹn thành công.');
}



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        // Nếu đã bị huỷ từ trước
        if ($appointment->status === 'cancelled') {
            return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được huỷ trước đó.');
        }

        // Thực hiện huỷ lịch hẹn
        $appointment->update([
            'status' => 'cancelled',
            'payment_status' => 'failed'
        ]);

        return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được huỷ.');
    }
}
