<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessRefundRequest;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\CancelledAppointment;
use App\Mail\RefundStatusMail;
use App\Mail\AdminCancelBookingMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        // Kiểm tra quyền truy cập
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('appointments.index')->with('error', 'Bạn không có quyền truy cập.');
        }

        $status = $request->query('status');
        $search = $request->query('search');
        $filter = $request->query('filter', 'all');

        $query = match ($filter) {
            'deleted' => RefundRequest::onlyTrashed()->with(['user', 'order', 'appointment']),
            'active' => RefundRequest::query()->with(['user', 'order', 'appointment']),
            default => RefundRequest::withTrashed()->with(['user', 'order', 'appointment']),
        };

        if ($status) {
            $query->where('refund_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('order', function ($q3) use ($search) {
                    $q3->where('order_code', 'like', "%{$search}%");
                })->orWhereHas('appointment', function ($q4) use ($search) {
                    $q4->where('appointment_code', 'like', "%{$search}%");
                });
            });
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(10);

        $activeTab = $status ?? 'pending';

        if ($search && $refunds->isNotEmpty()) {
            $activeTab = $refunds->first()->refund_status;
        }

        $pendingRefundCount = RefundRequest::where('refund_status', 'pending')->count();

        return view('admin.refunds.index', [
            'refunds' => $refunds,
            'search' => $search,
            'activeTab' => $activeTab,
            'status' => $status,
            'filter' => $filter,
            'pendingRefundCount' => $pendingRefundCount,
        ]);
    }

    public function show($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('appointments.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $refund = RefundRequest::withTrashed()->findOrFail($id);
        $refund->load([
            'user',
            'order.items.productVariant.volume',
            'appointment.service',
            'appointment.barber',
            'appointment.branch',
        ]);

        return view('admin.refunds.show', compact('refund'));
    }

    public function update(ProcessRefundRequest $request, RefundRequest $refund)
    {
        DB::beginTransaction();
        try {
            $newStatus = $request->input('refund_status');
            $currentStatus = $refund->refund_status;

            if (in_array($currentStatus, ['refunded', 'rejected'])) {
                return back()->withErrors(['error' => 'Không thể cập nhật yêu cầu đã ' . ($currentStatus === 'refunded' ? 'hoàn tiền' : 'từ chối') . '.']);
            }

            $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];

            if ($statusOrder[$newStatus] < $statusOrder[$currentStatus]) {
                return back()->withErrors(['error' => 'Không thể quay lại trạng thái trước đó.']);
            }

            $updateData = ['refund_status' => $newStatus];

            if ($newStatus === 'rejected') {
                $updateData['reject_reason'] = $request->input('reject_reason');
            }

            if ($newStatus === 'refunded') {
                if ($refund->order_id) {
                    $order = Order::findOrFail($refund->order_id);

                    if ($order->payment_status !== 'paid') {
                        return back()->withErrors(['error' => 'Không thể hoàn tiền. Trạng thái thanh toán hiện tại: ' . $order->payment_status]);
                    }

                    $order->update(['status' => 'cancelled']);
                } elseif ($refund->appointment_id) {
                    $appointment = Appointment::findOrFail($refund->appointment_id);

                    if ($appointment->payment_status !== 'paid') {
                        return back()->withErrors(['error' => 'Không thể hoàn tiền. Trạng thái thanh toán hiện tại: ' . $appointment->payment_status]);
                    }

                    // Di chuyển lịch hẹn sang bảng cancelled_appointments
                    $appointmentData = $appointment->toArray();
                    $appointmentData['appointment_time'] = $appointment->appointment_time
                        ? \Carbon\Carbon::parse($appointment->appointment_time)->format('Y-m-d H:i:s')
                        : null;

                    $cancelledAppointment = CancelledAppointment::create(array_merge($appointmentData, [
                        'status' => 'cancelled',
                        'payment_status' => 'refunded',
                        'cancellation_type' => 'refund_cancel',
                        'status_before_cancellation' => $appointment->status,
                        'additional_services' => $appointment->additional_services,
                        'payment_method' => $appointment->payment_method,
                        'note' => $appointment->note,
                        'cancellation_reason' => $request->input('cancellation_reason', 'Hoàn tiền và hủy lịch'),
                    ]));

                    // Xóa các bản ghi liên quan (GIỮ lại yêu cầu hoàn tiền để lưu lịch sử)
                    DB::table('checkins')->where('appointment_id', $appointment->id)->delete();

                    // KHÔNG xóa lịch hẹn gốc để tránh mất lịch sử yêu cầu hoàn tiền (FK)
                    // Cập nhật trạng thái lịch hẹn về cancelled + refunded và giải phóng slot thời gian
                    $appointment->update([
                        'status' => 'cancelled',
                        'payment_status' => 'refunded',
                        'appointment_time' => null,
                    ]);
                }

                // Xử lý upload hình ảnh minh chứng
                if ($request->hasFile('proof_image')) {
                    $file = $request->file('proof_image');
                    if ($file->isValid()) {
                        // Lưu file vào storage (thư mục public/proof_images)
                        $path = $file->store('proof_images', 'public');
                        $updateData['proof_image'] = $path;
                    } else {
                        return back()->withErrors(['error' => 'Hình ảnh minh chứng không hợp lệ.']);
                    }
                }

                $updateData['refunded_at'] = now();
            }

            $refund->update($updateData);

            if ($newStatus === 'refunded' || $newStatus === 'rejected') {
                // Sử dụng queue thay vì send trực tiếp
                Mail::to($refund->user->email)->queue(new RefundStatusMail($refund, $newStatus));
            }

            DB::commit();
            // Redirect về trang index với trạng thái mới và trang hiện tại
            return redirect()->route('refunds.index', [
                'status' => $newStatus,
                'page' => $request->input('page', 1)
            ])->with('success', 'Yêu cầu hoàn tiền ' . ($refund->order->order_code ?? $refund->appointment->appointment_code ?? 'N/A') . ' đã được cập nhật.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật trạng thái hoàn tiền', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage(),
            ]);
            return back()->withErrors(['error' => 'Lỗi xảy ra: ' . $e->getMessage()])->withInput();
        }
    }

    public function softDelete($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền xoá yêu cầu hoàn tiền.'
            ]);
        }

        $refund = RefundRequest::findOrFail($id);

        if (!in_array($refund->refund_status, ['refunded', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ được xoá mềm các yêu cầu đã hoàn tiền hoặc từ chối.'
            ]);
        }

        $refund->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã xoá mềm yêu cầu hoàn tiền.'
        ]);
    }

    public function restore($id)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền khôi phục yêu cầu hoàn tiền.'
            ]);
        }

        $refund = RefundRequest::withTrashed()->findOrFail($id);

        if (!$refund->trashed()) {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu hoàn tiền chưa bị xoá.'
            ]);
        }

        $refund->restore();

        return response()->json([
            'success' => true,
            'message' => 'Đã khôi phục yêu cầu hoàn tiền.'
        ]);
    }
}
