<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessRefundRequest;
use App\Models\RefundRequest;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status'); // status có thể null (hiện tất cả)
        $search = $request->query('search');

        $refunds = RefundRequest::with(['user', 'order'])
            ->when($status, function ($query, $status) {
                $query->where('refund_status', $status);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                        ->orWhereHas('order', function ($q3) use ($search) {
                            $q3->where('order_code', 'like', "%{$search}%"); // thay `code` nếu bạn tìm theo mã đơn hàng
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends([
                'status' => $status,
                'search' => $search,
            ]);

        return view('admin.refunds.index', compact('refunds', 'status', 'search'));
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
            $newStatus = $request->input('refund_status');

            // Trạng thái hiện tại
            $currentStatus = $refund->refund_status;

            // Không cho phép cập nhật nếu đã hoàn tiền hoặc từ chối
            if (in_array($currentStatus, ['refunded', 'rejected'])) {
                return back()->withErrors(['error' => 'Không thể cập nhật yêu cầu đã ' . ($currentStatus === 'refunded' ? 'hoàn tiền' : 'từ chối') . '.']);
            }

            // Cấm quay lại trạng thái cũ
            $statusOrder = ['pending' => 1, 'processing' => 2, 'refunded' => 3, 'rejected' => 3];
            if ($statusOrder[$newStatus] < $statusOrder[$currentStatus]) {
                return back()->withErrors(['error' => 'Không thể quay lại trạng thái trước đó.']);
            }

            // Nếu là hoàn tiền, kiểm tra đơn hàng & thanh toán
            if ($newStatus === 'refunded') {
                $order = Order::findOrFail($refund->order_id);
                $payment = Payment::where('order_id', $refund->order_id)->firstOrFail();

                if ($payment->status !== 'paid') {
                    return back()->withErrors(['error' => 'Không thể hoàn tiền cho đơn hàng chưa thanh toán.']);
                }

                $order->update(['status' => 'cancelled']);

                $refund->update([
                    'refund_status' => 'refunded',
                    'refunded_at' => now(),
                ]);
            } else {
                $refund->update([
                    'refund_status' => $newStatus,
                ]);
            }

            DB::commit();
            return redirect()->route('refunds.index')->with('success', 'Cập nhật trạng thái hoàn tiền thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi xảy ra: ' . $e->getMessage()]);
        }
    }
}
