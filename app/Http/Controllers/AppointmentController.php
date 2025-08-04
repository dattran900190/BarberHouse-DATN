<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Checkin;
use App\Models\Service;
use App\Models\Appointment;
use App\Mail\CustomerNoShow;
use Illuminate\Http\Request;
use App\Mail\CheckinCodeMail;
use Illuminate\Support\Carbon;
use App\Mail\AdminCancelBookingMail; // Thêm dòng này
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\CancelledAppointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Events\AppointmentStatusUpdated;
use App\Http\Requests\AppointmentRequest;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeTab = $request->input('status', 'pending');
        $allAppointments = collect();
        $statuses = ['pending', 'confirmed', 'checked-in', 'progress', 'completed'];
        $appointments = [];
        $user = Auth::user();

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
                ->orderBy('created_at', 'DESC');
        };

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
                ->orderBy('created_at', 'DESC');
        };

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

        foreach ($statuses as $status) {
            $query = Appointment::where('status', $status);
            $buildAppointmentQuery($query, $search);
            $appointments[$status . 'Appointments'] = $query->paginate(10, ['*'], $status . '_page');
        }

        $cancelledQuery = CancelledAppointment::query();
        $buildCancelledQuery($cancelledQuery, $search);
        $appointments['cancelledAppointments'] = $cancelledQuery->paginate(10, ['*'], 'cancelled_page');

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

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu lịch hẹn ' . $appointment->appointment_code . ' là no-show.'
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
            $appointment->status = 'completed';
            $appointment->payment_status = 'paid';
            $appointment->save();

            event(new AppointmentStatusUpdated($appointment));

            return response()->json([
                'success' => true,
                'message' => 'Lịch hẹn ' . $appointment->appointment_code . ' đã được hoàn thành.'
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

            $appointment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã hủy lịch ' . $cancelledAppointment->appointment_code . '.'
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

    public function edit(Appointment $appointment)
    {
        $appointments = Appointment::all();
        $services = Service::all();
        return view('admin.appointments.edit', compact('appointment', 'services'));
    }

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

            $appointment->delete();

            return redirect()->route('appointments.index')->with('success', 'Lịch hẹn ' . $cancelledAppointment->appointment_code . ' đã được hủy.');
        } catch (\Exception $e) {
            Log::error('Error cancelling appointment: ' . $e->getMessage());
            return redirect()->route('appointments.index')
                ->with('error', 'Lỗi khi hủy lịch hẹn: ' . $e->getMessage());
        }
    }
}
