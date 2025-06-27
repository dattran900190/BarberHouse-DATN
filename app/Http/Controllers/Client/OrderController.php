<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('client.orderHistory', compact('orders'));
    }


    public function show(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }

        $order->load('items.productVariant.product');

        // Trả về view với thông tin chi tiết đơn hàng
        return view('client.detailOrderHistory', [
            'order' => $order,
            'items' => $order->items, // Truyền order items vào view
        ]);
    }
    public function cancel(Order $order)
    {
        // Kiểm tra quyền truy cập
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }

        // Cập nhật trạng thái đơn hàng thành 'cancelled'
        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('client.orderHistory')->with('success', 'Đơn hàng đã được hủy.');
    }
}
