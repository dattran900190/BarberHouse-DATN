<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;

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
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('barber', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('service', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('branch', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%');
                    });
                });
            })

            ->orderBy('id', 'DESC')
            ->paginate(5);

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
        //

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
        return view('admin.appointments.edit', compact('appointment', ));
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(AppointmentRequest $request, Appointment $appointment)
    {
        $currentStatus = $appointment->status;
        $newStatus = $request->status;
        $currentPaymentStatus = $appointment->payment_status;
        $newPaymentStatus = $request->payment_status;

        // 1. Nếu đã cancelled thì không được phép chuyển sang trạng thái khác
        if ($currentStatus === 'cancelled' && $newStatus !== 'cancelled') {
            return back()->withErrors([
                'status' => 'Lịch hẹn đã bị huỷ, không thể chuyển sang trạng thái khác.'
            ]);
        }

        // 2. Nếu đã completed thì không cho chỉnh sang trạng thái khác
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

        // 4. Chặn chuyển về pending hoặc confirmed nếu đã completed/cancelled
        if (in_array($currentStatus, ['completed', 'cancelled']) && in_array($newStatus, ['pending', 'confirmed'])) {
            return back()->withErrors([
                'status' => 'Không thể chuyển về trạng thái trước đó sau khi đã hoàn thành hoặc huỷ.'
            ]);
        }

        // 5. Không cho huỷ lịch đã hoàn thành và đã thanh toán
        if ($currentPaymentStatus === 'paid' && $currentStatus === 'completed' && $newStatus === 'cancelled') {
            return back()->withErrors([
                'status' => 'Không thể huỷ lịch đã hoàn thành và đã thanh toán.'
            ]);
        }

        // 6. Nếu payment_status là refunded, không cho thay đổi status hoặc payment_status
        if ($currentPaymentStatus === 'refunded') {
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

        // 7. Không cho chuyển từ paid về unpaid
        if ($currentPaymentStatus === 'paid' && $newPaymentStatus === 'unpaid') {
            return back()->withErrors([
                'payment_status' => 'Không thể chuyển từ "Thanh toán thành công" về "Chưa thanh toán".'
            ]);
        }

        // 8. Không cho chuyển từ failed về unpaid
        if ($currentPaymentStatus === 'failed' && $newPaymentStatus === 'unpaid') {
            return back()->withErrors([
                'payment_status' => 'Không thể chuyển từ "Thanh toán thất bại" về "Chưa thanh toán".'
            ]);
        }

        // 9. Nếu chuyển status sang cancelled và payment_status là paid, đặt thành refunded
        $updateData = $request->only(['appointment_time', 'status', 'payment_status']);
        if ($newStatus === 'cancelled' && $currentPaymentStatus === 'paid') {
            $updateData['payment_status'] = 'refunded'; // Có thể thay bằng 'failed' nếu cần
        }

        // 10. Không cho sửa payment_status về unpaid nếu hiện tại không phải unpaid
        if ($newPaymentStatus === 'unpaid' && $currentPaymentStatus !== 'unpaid') {
            return back()->withErrors([
                'payment_status' => 'Không thể chuyển về trạng thái chưa thanh toán.'
            ]);
        }

        // Thực hiện cập nhật nếu qua hết các kiểm tra
        $appointment->update($updateData);

        // Chuyển hướng về trang hiện tại
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
