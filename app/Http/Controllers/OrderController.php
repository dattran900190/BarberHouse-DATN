<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng với bộ lọc tìm kiếm và phân trang
    public function index(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        })
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders', 'search'));
    }


    // Hiển thị chi tiết đơn hàng, load quan hệ orderItems và productVariant
    public function show(Order $order)
    {
        $order->load('orderItems.productVariant');

        return view('admin.orders.show', compact('order'))->with('title', 'Chi tiết đơn hàng');
    }

    // Cập nhật trạng thái đơn hàng với validate riêng, kiểm tra logic trạng thái và cập nhật tồn kho khi hủy đơn
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed,cancelled',
        ]);

        $newStatus = $request->status;
        $currentStatus = $order->status;

        // Nếu đơn hàng đã bị hủy
        if ($currentStatus === 'cancelled') {
            return redirect()->route('orders.show', $order->id)->with('error', 'Đơn hàng đã bị hủy, không thể thay đổi trạng thái!');
        }

        // Nếu đơn hàng đã hoàn thành
        if ($currentStatus === 'completed') {
            if ($newStatus === 'cancelled') {
                return redirect()->route('orders.show', $order->id)->with('error', 'Đơn hàng đã hoàn thành, không thể hủy đơn!');
            }
            if ($newStatus !== 'completed') {
                return redirect()->route('orders.show', $order->id)->with('error', 'Đơn hàng đã hoàn thành, không thể thay đổi trạng thái!');
            }
        }

        $validStatuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled'];
        $statusOrder = array_flip($validStatuses);

        // Không cho quay lại trạng thái trước đó (trừ khi là hủy)
        if ($newStatus !== 'cancelled' && $statusOrder[$newStatus] <= $statusOrder[$currentStatus]) {
            return redirect()->route('orders.show', $order->id)->with('error', 'Không thể quay lại trạng thái trước đó!');
        }

        // Nếu chuyển sang hủy đơn, cộng lại tồn kho
        if ($currentStatus !== 'cancelled' && $newStatus === 'cancelled') {
            foreach ($order->orderItems as $item) {
                $variant = ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    $variant->stock += $item->quantity;
                    $variant->save();
                }
            }
        }

        $order->status = $newStatus;
        $order->save();

        return redirect()->route('orders.show', $order->id)->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }


    public function destroy(Order $order)
    {
        // Cập nhật trạng thái thành cancelled
        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Đơn hàng đã được hủy.');
    }
}
