<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Checkin;
use App\Models\Service;
use App\Models\Appointment;
use App\Mail\CustomerNoShow;
use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use App\Mail\CancelBookingMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\AppointmentConfirmed;
use App\Models\CancelledAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\AppointmentStatusUpdated;
use App\Http\Requests\AppointmentRequest;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeTab = $request->input('status', 'pending');
        $allAppointments = collect();
        $statuses = ['pending', 'confirmed', 'checked-in', 'progress', 'completed'];
        $appointments = [];
        $user = Auth::user();
        // Hàm xây dựng truy vấn cho bảng appointments
        $buildAppointmentQuery = function ($query, $search) use ($user) {
            $query->with(['user:id,name', 'barber:id,name', 'service:id,name'])
                ->when($user->role === 'admin_branch', function ($q) use ($user) {
                    $q->where('branch_id', $user->branch_id);
                })
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('appointment_code', 'like', '%' . $search . '%')
                            ->orWhereHas('user', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('barber', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('service', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderBy('updated_at', 'DESC');
        };
        $user = Auth::user();
        // Hàm xây dựng truy vấn cho bảng cancelled_appointments
        $buildCancelledQuery = function ($query, $search) use ($user) {
            $query->with(['user:id,name', 'barber:id,name', 'service:id,name'])
                ->when($user->role === 'admin_branch', fn($q) => $q->where('branch_id', $user->branch_id))
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('appointment_code', 'like', '%' . $search . '%')
                            ->orWhereHas('user', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('barber', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('service', function ($q2) use ($search) {
                                $q2->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderBy('updated_at', 'DESC');
        };

        // Nếu có tìm kiếm và không có tham số status rõ ràng, điều chỉnh $activeTab
        if ($search && !$request->has('status')) {
            $appointmentQuery = Appointment::query();
            $buildAppointmentQuery($appointmentQuery, $search);
            $appointmentsResult = $appointmentQuery->get();

            $cancelledQuery = CancelledAppointment::query();
            $buildCancelledQuery($cancelledQuery, $search);
            $cancelledResult = $cancelledQuery->get();

            $allAppointments = $appointmentsResult->merge($cancelledResult);

            if ($allAppointments->count() > 0) {
                $activeTab = $allAppointments->first()->status;
            }
        }

        // Lấy danh sách lịch hẹn theo trạng thái từ bảng appointments
        foreach ($statuses as $status) {
            $query = Appointment::where('status', $status);
            $buildAppointmentQuery($query, $search);
            $appointments[$status . 'Appointments'] = $query->paginate(5, ['*'], $status . '_page');
        }

        // Lấy danh sách lịch hẹn từ bảng cancelled_appointments
        $cancelledQuery = CancelledAppointment::query();
        $buildCancelledQuery($cancelledQuery, $search);
        $appointments['cancelledAppointments'] = $cancelledQuery->paginate(5, ['*'], 'cancelled_page');

        return view('admin.appointments.index', array_merge(
            compact('activeTab', 'allAppointments', 'search'),
            $appointments
        ));
    }

    public function markNoShow(Request $request, Appointment $appointment)
    {
        try {
            // Kiểm tra trạng thái lịch hẹn
            if (!in_array($appointment->status, ['pending', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể đánh dấu no-show cho lịch hẹn ở trạng thái chờ xác nhận hoặc đã xác nhận.'
                ], 400);
            }

            // Xóa bản ghi liên quan trong bảng checkins
            Checkin::where('appointment_id', $appointment->id)->delete();


            // Lưu bản ghi vào bảng cancelled_appointments
            CancelledAppointment::create(array_merge($appointment->toArray(), [
                'status' => 'cancelled',
                'payment_status' => $appointment->payment_status,
                'payment_method' => $appointment->payment_method,
                'cancellation_type' => 'no-show',
                'status_before_cancellation' => $appointment->status,
                'additional_services' => $appointment->additional_services,
                'cancellation_reason' => $request->input('no_show_reason', 'Khách hàng không đến'),
            ]));

            // Xóa bản ghi liên quan trong bảng checkins (nếu có)
            DB::table('checkins')->where('appointment_id', $appointment->id)->delete();

            // Xóa bản ghi khỏi bảng appointments
            $appointment->delete();

            // Gửi email thông báo
            Mail::to($appointment->email)->send(new CustomerNoShow($appointment));

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu lịch hẹn ' . $appointment->appointment_code . ' là no-show.'
            ]);
        } catch (\Exception $e) {
            // Log::error('Lỗi khi đánh dấu no-show: ' . $e->getMessage(), [
            //     'appointment_id' => $appointment->id,
            //     'request_data' => $request->all(),
            // ]);

            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra khi đánh dấu no-show: ' . $e->getMessage(),
            ], 500);
        }
    }

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
            $qrCode = rand(100000, 999999); // Mã QR duy nhất;
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

            // Gửi email mã QR
            $checkin = Checkin::where('appointment_id', $appointment->id)->first();
            Mail::to($appointment->email)->send(new CheckinCodeMail($checkin->qr_code_value, $appointment, $additionalServices));

            // Trigger event
            // event(new AppointmentConfirmed($appointment));

            event(new AppointmentStatusUpdated($appointment));

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được xác nhận.'
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

            // Cập nhật trạng thái lịch hẹn và thanh toán
            $appointment->status = 'completed';
            $appointment->payment_status = 'paid'; // hoặc kiểm tra đã thanh toán trước
            $appointment->save();

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hoàn thành.'
            ]);
        } catch (\Exception $e) {
            // Log::error('Complete: Failed', [
            //     'appointment_id' => $appointment->id,
            //     'error' => $e->getMessage()
            // ]);
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
                return redirect()->route('appointments.index')
                    ->with('error', 'Lịch hẹn này không thể hủy.');
            }

            // Lưu bản ghi vào bảng cancelled_appointments
            CancelledAppointment::create(array_merge($appointment->toArray(), [
                'status' => 'cancelled',
                'payment_status' => $appointment,
                'cancellation_type' => 'no-show',
                'status_before_cancellation' => $appointment->status,
                'additional_services' => $appointment->additional_services,
                'payment_method' => $appointment->payment_method,
                'note' => $appointment->note,
                'cancellation_reason' => $request->input('no_show_reason', 'Khách hàng không đến'),
            ]));

            // Xóa bản ghi liên quan trong bảng checkins (nếu có)
            DB::table('checkins')->where('appointment_id', $appointment->id)->delete();

            // Xóa bản ghi khỏi bảng appointments
            $appointment->delete();

            Mail::to($appointment->email)->send(new CancelBookingMail($appointment));

            return response()->json([
                'success' => true,
                'message' => 'Đã huỷ lịch ' . $appointment->appointment_code . '.'
            ]);
        } catch (\Exception $e) {
            // Log::error('Cancel: Failed', [
            //     'appointment_id' => $appointment->id,
            //     'error' => $e->getMessage()
            // ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi huỷ lịch hẹn: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show(Appointment $appointment)
    {
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
            ->withTrashed() // cho phép lấy cả dịch vụ đã xóa mềm
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
            ->withTrashed() // cho phép lấy cả dịch vụ đã xóa mềm
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


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Appointment $appointment)
    {
        $appointments = Appointment::all();
        $services = Service::all();
        return view('admin.appointments.edit', compact('appointment', 'services'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AppointmentRequest $request, Appointment $appointment)
    {
        try {
            $newStatus = $request->status;
            $newPaymentStatus = $request->payment_status;

            $serviceId = $request->input('service_id');
            $additionalServices = json_decode($request->input('additional_services', '[]'), true) ?? [];

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

            // Giữ nguyên trang
            $currentPage = $request->input('page', 1);

            // Nếu trạng thái là 'pending', gửi email thông báo
            if ($appointment->status === 'confirmed') {
                // Tạo mã QR check-in
                $qrCode = rand(100000, 999999); // Mã QR duy nhất;
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

                // Gửi email mã QR
                $checkin = Checkin::where('appointment_id', $appointment->id)->first();
                Mail::to($appointment->email)->send(new CheckinCodeMail($checkin->qr_code_value, $appointment, $additionalServices));
            }

            // Nếu trạng thái là 'cancelled', lưu vào bảng cancelled_appointments
            if ($appointment->status === 'cancelled') {
                // Lưu bản ghi vào bảng cancelled_appointments
                CancelledAppointment::create(array_merge($appointment->toArray(), [
                    'status' => 'cancelled',
                    'payment_status' => $appointment->payment_status,
                    'payment_method' => $appointment->payment_method,
                    'cancellation_type' => 'no-show',
                    'status_before_cancellation' => $appointment->status,
                    'additional_services' => $appointment->additional_services,
                    'note' => $appointment->note,
                    'cancellation_reason' => $request->input('no_show_reason', 'Khách hàng không đến'),
                ]));

                // Xóa bản ghi liên quan trong bảng checkins (nếu có)
                DB::table('checkins')->where('appointment_id', $appointment->id)->delete();

                // Xóa bản ghi khỏi bảng appointments
                $appointment->delete();

                Mail::to($appointment->email)->send(new CancelBookingMail($appointment));
            }

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được cập nhật.',
                'page' => $currentPage
            ]);
        } catch (\Exception $e) {
            // Log::error('Confirm: Failed', [
            //     'appointment_id' => $appointment->id,
            //     'error' => $e->getMessage()
            // ]);
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
