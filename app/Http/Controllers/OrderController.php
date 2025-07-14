<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng với bộ lọc tìm kiếm và phân trang
    public function index(Request $request)
    {
        $search = $request->input('search');
        $activeTab = $request->input('tab', 'pending');
        
        $user = Auth::user();
        
        // Hàm xây dựng truy vấn cơ bản
        $buildQuery = function ($query, $search) use ($user) {
            $query->when($search, function ($q) use ($search) {
                return $q->where(function ($subQuery) use ($search) {
                    $subQuery->where('order_code', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $search . '%');
                });
            })
            ->when($user->role === 'admin_branch', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->orderBy('created_at', 'DESC');
        };

        // Lấy đơn hàng theo từng trạng thái với phân trang
        $pendingOrders = Order::query();
        $buildQuery($pendingOrders, $search);
        $pendingOrders = $pendingOrders->where('status', 'pending')->paginate(10, ['*'], 'pending_page');

        $processingOrders = Order::query();
        $buildQuery($processingOrders, $search);
        $processingOrders = $processingOrders->where('status', 'processing')->paginate(10, ['*'], 'processing_page');

        $shippingOrders = Order::query();
        $buildQuery($shippingOrders, $search);
        $shippingOrders = $shippingOrders->where('status', 'shipping')->paginate(10, ['*'], 'shipping_page');

        $completedOrders = Order::query();
        $buildQuery($completedOrders, $search);
        $completedOrders = $completedOrders->where('status', 'completed')->paginate(10, ['*'], 'completed_page');

        $cancelledOrders = Order::query();
        $buildQuery($cancelledOrders, $search);
        $cancelledOrders = $cancelledOrders->where('status', 'cancelled')->paginate(10, ['*'], 'cancelled_page');

        // Đếm số lượng đơn hàng cho mỗi tab
        $pendingCount = Order::where('status', 'pending')->count();
        $processingCount = Order::where('status', 'processing')->count();
        $shippingCount = Order::where('status', 'shipping')->count();
        $completedCount = Order::where('status', 'completed')->count();
        $cancelledCount = Order::where('status', 'cancelled')->count();

        return view('admin.orders.index', compact(
            'pendingOrders', 
            'processingOrders', 
            'shippingOrders', 
            'completedOrders', 
            'cancelledOrders',
            'activeTab',
            'search',
            'pendingCount',
            'processingCount',
            'shippingCount',
            'completedCount',
            'cancelledCount'
        ));
    }
    public function confirm(Order $order)
    {

        if ($order->status === 'pending') {
            $order->status = 'processing';
            $order->save();

            return back()->with('success', 'Đã xác nhận đơn hàng.');
        }

        return back()->with('error', 'Đơn hàng không thể xác nhận.');
    }


    // Hiển thị chi tiết đơn hàng, load quan hệ items và productVariant
    public function show(Order $order)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.orders.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $order->load('items.productVariant.product');

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
        $paymentStatus = $order->payment_status;

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

        // Lấy số trang từ request
        $currentPage = $request->input('page', 1);

        return redirect()->route('admin.orders.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }


    public function destroy(Order $order)
    {
          if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.orders.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $order->status = 'cancelled';
        $order->save();

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng đã được hủy.');
    }
}
