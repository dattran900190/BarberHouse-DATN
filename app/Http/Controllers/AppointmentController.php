<?php

namespace App\Http\Controllers;

use App\Models\Checkin;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use Illuminate\Support\Facades\Log;
use App\Events\AppointmentConfirmed;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AppointmentRequest;
use App\Mail\CancelBookingMail;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeTab = 'pending';
        $allAppointments = collect();
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'pending_cancellation'];
        $appointments = [];

        $buildQuery = function ($query, $search) {
            $query->with(['user:id,name', 'barber:id,name', 'service:id,name'])
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->whereHas('user', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%' . $search . '%');
                        })->orWhereHas('barber', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%' . $search . '%');
                        })->orWhereHas('service', function ($q2) use ($search) {
                            $q2->where('name', 'like', '%' . $search . '%');
                        });
                    });
                })
                ->orderBy('updated_at', 'DESC');
        };

        if ($search) {
            $allAppointments = Appointment::query();
            $buildQuery($allAppointments, $search);
            $allAppointments = $allAppointments->get();

            if ($allAppointments->count() > 0) {
                $activeTab = $allAppointments->first()->status;
            }
        }

        foreach ($statuses as $status) {
            $query = Appointment::where('status', $status);
            $buildQuery($query, $search);
            $appointments[$status . 'Appointments'] = $query->paginate(5, ['*'], $status . '_page');
        }

        return view('admin.appointments.index', array_merge(
            compact('activeTab', 'allAppointments', 'search'),
            $appointments
        ));
    }


    // public function confirm($id)
    // {
    //     $appointment = Appointment::findOrFail($id);
    //     $appointment->status = 'confirmed';
    //     $appointment->save();

    //     // // Create check-in QR code
    //     $qrCode = rand(100000, 999999);
    //     Checkin::create([
    //         'appointment_id' => $appointment->id,
    //         'qr_code_value' => $qrCode,
    //         'is_checked_in' => false,
    //         'checkin_time' => null,
    //     ]);
    //     $checkin = Checkin::where('appointment_id', $appointment->id)->first();
    //     Mail::to($appointment->email)->send(new CheckinCodeMail($checkin->qr_code_value, $appointment));

    //     // Gửi sự kiện Pusher khi xác nhận lịch hẹn
    //     try {
    //         Log::info('Kích hoạt sự kiện AppointmentConfirmed', [$appointment->toArray()]);

    //         // Gửi sự kiện Pusher khi đặt lịch hẹn
    //         event(new AppointmentConfirmed($appointment));
    //         Log::info('Sự kiện AppointmentConfirmed đã gửi');
    //     } catch (\Exception $e) {
    //         Log::error('Lỗi khi gửi sự kiện AppointmentConfirmed', ['error' => $e->getMessage()]);
    //     }


    //     // return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được xác nhận.');

    //     try {
    //         // Logic lưu dữ liệu
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Lịch hẹn đã được xác nhận.'
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Lỗi khi xác nhận lịch hẹn: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function confirm(Request $request, Appointment $appointment)
    {
        try {
            // Kiểm tra trạng thái hợp lệ
            if ($appointment->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lịch hẹn này không ở trạng thái chờ xác nhận.'
                ], 400);
            }

            // Cập nhật trạng thái
            $appointment->status = 'confirmed';
            $appointment->save();

            // Tạo mã QR check-in
            $qrCode = rand(100000, 999999);
            Checkin::create([
                'appointment_id' => $appointment->id,
                'qr_code_value' => $qrCode,
                'is_checked_in' => false,
                'checkin_time' => null,
            ]);

            // Gửi email mã QR
            $checkin = Checkin::where('appointment_id', $appointment->id)->first();
            Mail::to($appointment->email)->send(new CheckinCodeMail($checkin->qr_code_value, $appointment));

            // Trigger event
            event(new AppointmentConfirmed($appointment));

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn đã được xác nhận.'
            ]);
        } catch (\Exception $e) {
            Log::error('Confirm: Failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xác nhận lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
    }


    public function completed($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'completed';
        $appointment->payment_status = 'paid';
        $appointment->save();

        return redirect()->route('appointments.index')->with('success', 'Lịch hẹn đã được hoàn thành.');
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return redirect()->route('appointments.index')
                ->with('error', 'Lịch hẹn này không thể hủy.');
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('cancellation_reason', 'Hủy bởi admin'),
        ]);

        return redirect()->route('appointments.index')
            ->with('success', 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hủy.');
    }

    public function approveCancel(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== 'pending_cancellation') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch hẹn này không ở trạng thái chờ hủy.'
            ], 400);
        }

        $appointment->update([
            'status' => 'cancelled',
            'payment_status' => 'unpaid',
            'rejection_reason' => null,
        ]);

        Mail::to($appointment->email)->send(new CancelBookingMail($appointment));

        return response()->json([
            'success' => true,
            'message' => 'Đã chấp nhận hủy lịch hẹn ' . $appointment->appointment_code . '.'
        ]);
    }

    public function rejectCancel(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== 'pending_cancellation') {
            return response()->json([
                'success' => false,
                'message' => 'Lịch hẹn này không ở trạng thái chờ hủy.'
            ], 400);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $appointment->update([
            'status' => $appointment->status_before_cancellation ?? 'confirmed',
            'rejection_reason' => $request->input('rejection_reason'),
            'cancellation_reason' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối yêu cầu hủy lịch hẹn ' . $appointment->appointment_code . '.'
        ]);
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
        try {
            $currentStatus = $appointment->status;
            $newStatus = $request->status;
            $currentPaymentStatus = $appointment->payment_status;
            $newPaymentStatus = $request->payment_status;

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
            ]));

            // Giữ nguyên trang
            $currentPage = $request->input('page', 1);
            // return redirect()->route('appointments.index', ['page' => $currentPage])
            //     ->with('success', 'Cập nhật lịch hẹn thành công.');

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn đã được xác nhận.',
                // 'page' => $currentPage
            ]);
        } catch (\Exception $e) {
            Log::error('Confirm: Failed', [
                'appointment_id' => $appointment->id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xác nhận lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
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

        if (!$appointment->cancellation_reason) {
            return redirect()->route('appointments.index')
                ->with('error', 'Vui lòng cung cấp lý do hủy qua chức năng hủy.');
        }

        // Thực hiện huỷ lịch hẹn
        $appointment->update([
            'status' => 'cancelled',
            'payment_status' => 'failed'
        ]);

        return redirect()->route('appointments.index')->with('success', 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hủy.');
    }
}
