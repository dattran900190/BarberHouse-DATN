<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessRefundRequest;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\Appointment;
use App\Mail\RefundStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = RefundRequest::with(['user', 'order', 'appointment']);

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

        // Áp dụng phân trang, mặc định 10 bản ghi mỗi trang
        $refunds = $query->orderBy('created_at', 'desc')->paginate(10);

        // Logic để xác định activeTab dựa trên tìm kiếm
        $activeTab = $status ?? 'pending'; 

        if ($search && $refunds->isNotEmpty()) {
            // Nếu có kết quả tìm kiếm, ưu tiên chuyển đến tab của kết quả đầu tiên
            $activeTab = $refunds->first()->refund_status;
        } elseif ($search && $refunds->isEmpty() && !$status) {
            // Nếu tìm kiếm không có kết quả và không có tab cụ thể nào được chọn,
            // có thể giữ nguyên tab hiện tại hoặc về mặc định (pending)
        }

        // Lấy số lượng yêu cầu chờ duyệt cho sidebar
        $pendingRefundCount = RefundRequest::where('refund_status', 'pending')->count();

        return view('admin.refunds.index', [
            'refunds' => $refunds,
            'search' => $search,
            'activeTab' => $activeTab,
            'status' => $status,
            'pendingRefundCount' => $pendingRefundCount,
        ]);
    }

    public function show(RefundRequest $refund)
    {
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

            Log::info('Cập nhật trạng thái:', [
                'refund_id' => $refund->id,
                'order_id' => $refund->order_id,
                'appointment_id' => $refund->appointment_id,
                'current' => $currentStatus,
                'new' => $newStatus,
            ]);

            if (in_array($currentStatus, ['refunded', 'rejected'])) {
                return back()->withErrors(['error' => 'Không thể cập nhật yêu cầu đã ' . ($currentStatus === 'refunded' ? 'hoàn tiền' : 'từ chối') . '.']);
            }

            $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];
            Log::info('So sánh cấp độ:', [
                'current_order' => $statusOrder[$currentStatus],
                'new_order' => $statusOrder[$newStatus],
            ]);

            if ($statusOrder[$newStatus] < $statusOrder[$currentStatus]) {
                return back()->withErrors(['error' => 'Không thể quay lại trạng thái trước đó.']);
            }

            if ($newStatus === 'refunded') {
                if ($refund->order_id) {
                    $order = Order::findOrFail($refund->order_id);
                    Log::info('Đã tìm thấy đơn hàng', ['order' => $order->toArray()]);

                    if ($order->payment_status !== 'paid') {
                        Log::warning('Thanh toán không hợp lệ', ['payment_status' => $order->payment_status]);
                        return back()->withErrors(['error' => 'Không thể hoàn tiền. Trạng thái thanh toán hiện tại: ' . $order->payment_status]);
                    }

                    $order->update(['status' => 'cancelled']);
                } elseif ($refund->appointment_id) {
                    $appointment = Appointment::findOrFail($refund->appointment_id);
                    Log::info('Đã tìm thấy đặt lịch', ['appointment' => $appointment->toArray()]);

                    if ($appointment->payment_status !== 'paid') {
                        Log::warning('Thanh toán không hợp lệ', ['payment_status' => $appointment->payment_status]);
                        return back()->withErrors(['error' => 'Không thể hoàn tiền. Trạng thái thanh toán hiện tại: ' . $appointment->payment_status]);
                    }

                    $appointment->update(['status' => 'cancelled', 'payment_status' => 'refunded']);
                }

                $refund->update([
                    'refund_status' => 'refunded',
                    'refunded_at' => now(),
                ]);

                Mail::to($refund->user->email)->send(new RefundStatusMail($refund, 'refunded'));
            } else {
                $refund->update([
                    'refund_status' => $newStatus,
                ]);

                if ($newStatus === 'rejected') {
                    Mail::to($refund->user->email)->send(new RefundStatusMail($refund, 'rejected'));
                }
            }

            DB::commit();
            Log::info('Cập nhật trạng thái hoàn tiền thành công', ['refund_id' => $refund->id]);
            return redirect()->route('refunds.index')->with('success', 'Cập nhật trạng thái hoàn tiền thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật trạng thái hoàn tiền', [
                'refund_id' => $refund->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->withErrors(['error' => 'Lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}