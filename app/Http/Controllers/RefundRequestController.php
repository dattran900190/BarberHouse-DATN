<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessRefundRequest;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Mail\RefundStatusMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;



class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // tab trạng thái
        $search = $request->query('search'); // từ khóa tìm kiếm

        $query = RefundRequest::with(['user', 'order']);

        if ($status) {
            $query->where('refund_status', $status);
        }


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                })->orWhereHas('order', function ($q3) use ($search) {
                    $q3->where('order_code', 'like', "%{$search}%");
                });
            });
        }

        $refunds = $query->orderBy('created_at', 'desc')
            ->get(); // dùng get() thay vì paginate nếu cần xử lý nhiều tab

        $activeTab = $status ?? 'pending';

        return view('admin.refunds.index', [
            'refunds' => $refunds,
            'search' => $search,
            'activeTab' => $activeTab,
            'status' => $status,
        ]);
    }


    public function show(RefundRequest $refund)
    {
        $refund->load(['user', 'order.items.productVariant.volume']);
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
                'current' => $currentStatus,
                'new' => $newStatus,
            ]);

            // Không cho phép cập nhật nếu đã hoàn tiền hoặc từ chối
            if (in_array($currentStatus, ['refunded', 'rejected'])) {
                return back()->withErrors(['error' => 'Không thể cập nhật yêu cầu đã ' . ($currentStatus === 'refunded' ? 'hoàn tiền' : 'từ chối') . '.']);
            }

            // Cấm quay lại trạng thái cũ
            $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];
            Log::info('So sánh cấp độ:', [
                'current_order' => $statusOrder[$currentStatus],
                'new_order' => $statusOrder[$newStatus],
            ]);

            if ($statusOrder[$newStatus] < $statusOrder[$currentStatus]) {
                return back()->withErrors(['error' => 'Không thể quay lại trạng thái trước đó.']);
            }

            // Nếu là hoàn tiền, kiểm tra đơn hàng & trạng thái thanh toán
            if ($newStatus === 'refunded') {
                $order = Order::findOrFail($refund->order_id);
                Log::info('Đã tìm thấy đơn hàng', ['order' => $order->toArray()]);

                if ($order->payment_status !== 'paid') {
                    Log::warning('Thanh toán không hợp lệ', ['payment_status' => $order->payment_status]);
                    return back()->withErrors(['error' => 'Không thể hoàn tiền. Trạng thái thanh toán hiện tại: ' . $order->payment_status]);
                }

                $order->update(['status' => 'cancelled']);
                $refund->update([
                    'refund_status' => 'refunded',
                    'refunded_at' => now(),
                ]);

                // Gửi email thông báo hoàn tiền thành công
                Mail::to($refund->user->email)->send(new RefundStatusMail($refund, 'refunded'));
            } else {
                // Cập nhật trạng thái khác như processing hoặc rejected
                $refund->update([
                    'refund_status' => $newStatus,
                ]);

                // Gửi email thông báo từ chối (nếu trạng thái là rejected)
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