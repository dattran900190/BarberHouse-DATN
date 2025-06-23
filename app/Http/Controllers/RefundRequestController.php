<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProcessRefundRequest;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RefundRequestController extends Controller
{
    public function index()
    {
        $refunds = RefundRequest::with(['user', 'order'])
            ->where('refund_status', 'pending')
            ->paginate(10);

        return view('admin.refunds.index', compact('refunds'));
    }

    public function show(RefundRequest $refund)
    {
        $refund->load(['user', 'order']);
        return view('admin.refunds.show', compact('refund'));
    }

    public function update(ProcessRefundRequest $request, RefundRequest $refund)
    {
        DB::beginTransaction();
        try {
            // Kiểm tra trạng thái đơn hàng và thanh toán
            $order = Order::findOrFail($refund->order_id);
            $payment = Payment::where('order_id', $refund->order_id)->firstOrFail();

            if ($payment->status !== 'paid') {
                return back()->withErrors(['error' => 'Không thể hoàn tiền cho đơn hàng chưa thanh toán.']);
            }

            // Cập nhật trạng thái đơn hàng thành 'cancelled'
            $order->update(['status' => 'cancelled']);

            // Cập nhật yêu cầu hoàn tiền
            $refund->update([
                'refund_status' => 'refunded',
                'refunded_at' => now(),
            ]);

            // TODO: Gửi thông báo cho người dùng (email hoặc notification)
            // \Illuminate\Support\Facades\Notification::send($refund->user, new RefundProcessed($refund));

            DB::commit();
            return redirect()->route('admin.refunds.index')->with('success', 'Hoàn tiền thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}