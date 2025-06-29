<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefundRequest;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // Hiển thị danh sách yêu cầu hoàn tiền
    public function index(Request $request)
    {
        $query = RefundRequest::where('user_id', Auth::id())
            ->with('order');

        // Lọc theo mã đơn hàng
        if ($request->filled('search')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_code', 'like', '%' . $request->search . '%');
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('refund_status', $request->status);
        }

        // Phân trang và sắp xếp mới nhất
        $refunds = $query->latest()->paginate(5)->appends([
            'search' => $request->search,
            'status' => $request->status,
        ]);

        return view('client.detailWallet', compact('refunds'));
    }

    // Hiển thị form tạo yêu cầu hoàn tiền
    public function create()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->get();


        return view('client.wallet', compact('orders'));
    }

    // Lưu yêu cầu hoàn tiền
    public function store(StoreRefundRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        // Đảm bảo đơn hàng thuộc về người dùng
        if ($order->user_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Bạn không có quyền thực hiện hành động này.']);
        }

        // Kiểm tra xem đã có yêu cầu hoàn tiền cho đơn hàng này chưa
        $existingRefund = RefundRequest::where('order_id', $order->id)->first();

        if ($existingRefund) {
            return back()->withErrors(['error' => 'Đơn hàng này đã được gửi yêu cầu hoàn tiền trước đó.']);
        }

        // Tạo yêu cầu hoàn tiền mới
        RefundRequest::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'refund_amount' => $order->total_money,
            'reason' => $request->reason,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'refund_status' => 'pending',
        ]);

        return redirect()->route('client.detailWallet')->with('success', 'Yêu cầu hoàn tiền đã được gửi.');
    }
}
