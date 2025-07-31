<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Events\OrderStatusUpdated;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng với bộ lọc tìm kiếm và phân trang
     */
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

        $pendingOrderCount = Order::where('status', 'pending')->count();

        return view('admin.orders.index', compact(
            'pendingOrders',
            'processingOrders',
            'shippingOrders',
            'completedOrders',
            'cancelledOrders',
            'activeTab',
            'search',
            'pendingOrderCount'
        ));
    }

    /**
     * Xác nhận đơn hàng từ trạng thái Chờ xác nhận sang Đang xử lý
     */
    public function confirm(Order $order)
    {
        if ($order->status === 'pending') {
            $order->status = 'processing';
            $order->save();
            // Gửi email xác nhận đơn hàng cho khách
            try {
                $order->load('items.productVariant.product');
                if ($order->email) {
                    Mail::to($order->email)->send(new OrderStatusMail($order, 'processing'));
                }
            } catch (\Exception $e) {
                Log::error('Lỗi gửi email xác nhận đơn hàng (admin): ' . $e->getMessage());
            }
            
            // Dispatch event để gửi thông báo realtime
            event(new OrderStatusUpdated($order));
            
            return response()->json(['success' => true, 'message' => 'Đã xác nhận đơn hàng.']);
        }
        return response()->json(['success' => false, 'message' => 'Đơn hàng không thể xác nhận.']);
    }

    /**
     * Chuyển trạng thái đơn hàng từ Đang xử lý sang Đang giao hàng
     */
    public function ship(Order $order)
    {
        if ($order->status !== 'processing') {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không ở trạng thái Đang xử lý!']);
        }

        $order->status = 'shipping';
        $order->save();

        // Gửi email thông báo đơn hàng đang giao
        try {
            $order->load('items.productVariant.product');
            if ($order->email) {
                Mail::to($order->email)->send(new OrderStatusMail($order, 'shipping'));
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email đơn hàng đang giao (admin): ' . $e->getMessage());
        }

        // Dispatch event để gửi thông báo realtime
        event(new OrderStatusUpdated($order));

        return response()->json(['success' => true, 'message' => 'Đơn hàng đã được chuyển sang trạng thái Đang giao hàng.']);
    }

    /**
     * Chuyển trạng thái đơn hàng từ Đang giao hàng sang Hoàn thành
     */
    public function complete(Order $order)
    {
        if ($order->status !== 'shipping') {
            return response()->json(['success' => false, 'message' => 'Đơn hàng không ở trạng thái Đang giao hàng!']);
        }

        $order->status = 'completed';
        $order->save();

        // Gửi email thông báo hoàn thành đơn hàng
        try {
            $order->load('items.productVariant.product');
            if ($order->email) {
                Mail::to($order->email)->send(new OrderStatusMail($order, 'completed'));
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email hoàn thành đơn hàng (admin): ' . $e->getMessage());
        }

        // Dispatch event để gửi thông báo realtime
        event(new OrderStatusUpdated($order));

        return response()->json(['success' => true, 'message' => 'Đơn hàng đã được chuyển sang trạng thái Hoàn thành.']);
    }

    /**
     * Hiển thị chi tiết đơn hàng, load quan hệ items và productVariant
     */
    public function show(Order $order)
    {
        if (Auth::user()->role === 'admin_branch') {
            return redirect()->route('admin.orders.index')->with('error', 'Bạn không có quyền truy cập.');
        }
        $order->load([
            'items.productVariant' => function ($query) {
                $query->withTrashed();
            },
            'items.productVariant.product' => function ($query) {
                $query->withTrashed();
            },
        ]);

        return view('admin.orders.show', compact('order'))->with('title', 'Chi tiết đơn hàng');
    }

    /**
     * Cập nhật trạng thái đơn hàng với validate riêng, kiểm tra logic trạng thái và cập nhật tồn kho khi hủy đơn
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipping,completed,cancelled',
        ]);

        $newStatus = $request->status;
        $currentStatus = $order->status;
        $page = $request->input('page', 1);

        // Nếu đơn hàng đã bị hủy
        if ($currentStatus === 'cancelled') {
            return redirect()->route('admin.orders.show', ['order' => $order->id, 'page' => $page])
                ->with('error', 'Đơn hàng đã bị hủy, không thể thay đổi trạng thái!');
        }

        // Nếu đơn hàng đã hoàn thành
        if ($currentStatus === 'completed') {
            if ($newStatus === 'cancelled') {
                return redirect()->route('admin.orders.show', ['order' => $order->id, 'page' => $page])
                    ->with('error', 'Đơn hàng đã hoàn thành, không thể hủy đơn!');
            }
            if ($newStatus !== 'completed') {
                return redirect()->route('admin.orders.show', ['order' => $order->id, 'page' => $page])
                    ->with('error', 'Đơn hàng đã hoàn thành, không thể thay đổi trạng thái!');
            }
        }

        $validStatuses = ['pending', 'processing', 'shipping', 'completed', 'cancelled'];
        $statusOrder = array_flip($validStatuses);

        // Không cho quay lại trạng thái trước đó (trừ khi là hủy)
        if ($newStatus !== 'cancelled' && $statusOrder[$newStatus] <= $statusOrder[$currentStatus]) {
            return redirect()->route('admin.orders.show', ['order' => $order->id, 'page' => $page])
                ->with('error', 'Không thể quay lại trạng thái trước đó!');
        }

        // Nếu chuyển sang hủy đơn, cộng lại tồn kho
        if ($currentStatus !== 'cancelled' && $newStatus === 'cancelled') {
            $order->load('items');
            foreach ($order->items as $item) {
                $variant = ProductVariant::find($item->product_variant_id);
                if ($variant) {
                    $variant->stock += $item->quantity;
                    $variant->save();
                }
            }
        }

        $order->status = $newStatus;
        $order->save();

        // Gửi email thông báo thay đổi trạng thái
        try {
            $order->load('items.productVariant.product');
            if ($order->email) {
                Mail::to($order->email)->send(new OrderStatusMail($order, $newStatus));
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email thay đổi trạng thái đơn hàng (admin): ' . $e->getMessage());
        }

        // Dispatch event để gửi thông báo realtime
        event(new OrderStatusUpdated($order));

        return redirect()->route('admin.orders.index', ['page' => $page])
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    /**
     * Hủy đơn hàng và hoàn lại số lượng tồn kho
     */
    public function destroy(Request $request, Order $order)
    {
        if (Auth::user()->role === 'admin_branch') {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.']);
        }

        // Kiểm tra nếu đơn hàng đã bị hủy hoặc đã hoàn thành
        if ($order->status === 'cancelled') {
            return response()->json(['success' => false, 'message' => 'Đơn hàng đã bị hủy trước đó!']);
        }
        if ($order->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Đơn hàng đã hoàn thành, không thể hủy!']);
        }

        // Cộng lại số lượng tồn kho
        $order->load('items');
        foreach ($order->items as $item) {
            $variant = ProductVariant::find($item->product_variant_id);
            if ($variant) {
                $variant->stock += $item->quantity;
                $variant->save();
            }
        }

        // Cập nhật trạng thái đơn hàng thành 'cancelled'
        $order->status = 'cancelled';
        $order->save();

        // Gửi email thông báo hủy đơn hàng
        try {
            $order->load('items.productVariant.product');
            if ($order->email) {
                Mail::to($order->email)->send(new OrderStatusMail($order, 'cancelled'));
            }
        } catch (\Exception $e) {
            Log::error('Lỗi gửi email hủy đơn hàng (admin): ' . $e->getMessage());
        }

        // Dispatch event để gửi thông báo realtime
        event(new OrderStatusUpdated($order));

        return response()->json(['success' => true, 'message' => 'Đơn hàng đã được hủy.']);
    }
}
