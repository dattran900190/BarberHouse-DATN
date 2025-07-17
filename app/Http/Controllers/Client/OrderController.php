<?php

namespace App\Http\Controllers\Client;

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

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Không có quyền truy cập đơn hàng này.');
        }

        if ($order->status === 'cancelled') {
            return redirect()->route('client.orderHistory')->with('error', 'Đơn hàng đã được hủy trước đó!');
        }

        DB::transaction(function () use ($order) {
            $order->status = 'cancelled';
            $order->save();

            foreach ($order->items as $item) {
                $variant = \App\Models\ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    $variant->increment('stock', $item->quantity);

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
