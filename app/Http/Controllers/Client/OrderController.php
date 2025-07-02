<?php

namespace App\Http\Controllers\Client;

use Illuminate\Support\Facades\DB;
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
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }
        if ($order->status === 'cancelled') {
            return redirect()->route('client.orderHistory')->with('error', 'Đơn hàng đã được hủy trước đó!');
        }

        DB::transaction(function () use ($order) {
            // Cập nhật trạng thái đơn hàng thành 'cancelled'
            $order->status = 'cancelled';
            $order->save();

            foreach ($order->items as $item) {
                $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);

                    // Cập nhật lại tồn kho tổng sản phẩm cha
                    $product = $variant->product;
                    if ($product) {
                        $product->updateStockFromVariants();
                    }
                }
            }
        });

        return redirect()->route('client.orderHistory')->with('success', 'Đơn hàng đã được hủy và hàng đã trả về kho.');
    }
}
