<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items.productVariant.product')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at');

        // Lọc theo từ khóa tìm kiếm
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                    ->orWhere('shipping_method', 'like', "%$search%")
                    ->orWhereHas('items', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%$search%");
                    });
            });
        }

        // ✅ Lọc theo trạng thái đơn hàng nếu có
        if ($request->filled('status')) {
            $status = $request->input('status');
            $query->where('status', $status);
        }

        $orders = $query->paginate(5)->appends($request->all());

        return view('client.orderHistory', compact('orders'));
    }



    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }

        $order->load('items.productVariant.product');

        return view('client.detailOrderHistory', [
            'order' => $order,
            'items' => $order->items,
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy đơn hàng khi đang chờ xử lý.'
            ], 400);
        }

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:500',
        ]);

        $order->status = 'cancelled';
        $order->cancellation_reason = $validated['cancellation_reason'];
        $order->cancellation_type = 'user'; // Hoặc gì đó bạn dùng
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Đơn hàng đã được hủy thành công.'
        ]);
    }
}
