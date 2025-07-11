<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;



class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ], [
            'product_variant_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_variant_id.exists' => 'Sản phẩm không tồn tại.',
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
        ]);

        $user = Auth::user();
        $productVariant = ProductVariant::find($request->product_variant_id);

        if (!$productVariant) {
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại.');
        }

        $cart = $this->getOrCreateCart($user);

        if (!$user) {
            Session::put('cart_id', $cart->id);
        }

        $cartItem = $cart->items()->where('product_variant_id', $request->product_variant_id)->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $cartItem->quantity + $request->quantity,
                'price' => $productVariant->price,
            ]);
        } else {
            $cart->items()->create([
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity,
                'price' => $productVariant->price,
            ]);
        }

        $cart_count = $cart->items()->sum('quantity');
        Session::put('cart_count', $cart_count);
        if ($request->ajax()) {
            return response()->json(['success' => true, 'cart_count' => $cart_count]);
        }
       return redirect()->back()->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
    }


    public function removeFromCart(CartItem $cartItem)
    {
        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);

        if ($cartItem->cart_id !== $cart->id) {
            return redirect()->route('cart.show')->with('error', 'Bạn không có quyền xóa mục này.');
        }

        $cartItem->delete();
        // Cập nhật lại số lượng trong session
        $cart_count = $cart->items()->sum('quantity');
        Session::put('cart_count', $cart_count);
        return redirect()->route('cart.show')->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng.');
    }



    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng.',
            'quantity.integer' => 'Số lượng phải là một số nguyên.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
        ]);

        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền cập nhật mục này.'], 403);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json([
            'success' => true,
            'unit_price' => round($cartItem->price),
            'subtotal' => round($cartItem->price * $cartItem->quantity)
        ]);
    }


    public function show()
    {
        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);

        // Lấy lại item từ DB mới nhất
        $items = CartItem::with('productVariant.product')
            ->where('cart_id', $cart->id)
            ->get();

        // Gán lại items để view dùng
        $cart->setRelation('items', $items);

        return view('client.cart', compact('cart', 'items'));
    }

    private function getOrCreateCart($user)
    {
        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();
            if (!$cart) {
                $cart = Cart::create(['user_id' => $user->id]);
            }
        } else {
            $cartId = Session::get('cart_id');
            $cart = $cartId ? Cart::find($cartId) : null;
            if (!$cart) {
                $cart = Cart::create(['user_id' => null]);
                Session::put('cart_id', $cart->id);
            }
        }
        return $cart;
    }
    public function updateVariant(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
        ]);

        $newVariant = ProductVariant::findOrFail($request->product_variant_id);
        $cartItem->update([
            'product_variant_id' => $newVariant->id,
            'price' => $newVariant->price, // cập nhật lại giá
        ]);

        return redirect()->route('cart.show');
    }
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);

        // Nếu không truyền gì lên thì lấy toàn bộ, còn nếu có thì lọc lại
        $selectedIds = [];
        if ($request->has('checkout_items')) {
            $selectedItems = json_decode($request->checkout_items, true) ?? [];
            $selectedIds = array_column($selectedItems, 'id');
        }

        if (empty($selectedIds)) {
            return redirect()->route('cart.show')->with('error', 'Bạn chưa chọn sản phẩm để thanh toán!');
        }

        // Lấy các item được chọn
        $items = $cart->items()->with('productVariant.product')
            ->whereIn('id', $selectedIds)
            ->get();

        $quantityMap = collect($selectedItems)->keyBy('id')->map(fn($item) => $item['quantity']);

        foreach ($items as $item) {
            if (isset($quantityMap[$item->id])) {
                $item->quantity = $quantityMap[$item->id];
            }
        }

        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Giỏ hàng của bạn đang trống hoặc không có sản phẩm nào được chọn.');
        }

        $userInfo = [
            'name'    => $user?->name ?? '',
            'email'   => $user?->email ?? '',
            'phone'   => $user?->phone ?? '',
            'address' => $user?->address ?? '',
        ];

        $mappedItems = $items->map(function ($item) {
            $subtotal = $item->price * $item->quantity;
            return [
                'product_variant_id' => $item->productVariant->id,
                'id' => $item->productVariant->product->id,
                'name' => $item->productVariant->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $item->productVariant->image ? Storage::url($item->productVariant->image) : asset('images/no-image.png'),
                'cart_item_id' => $item->id,
                'subtotal' => $subtotal,
            ];
        })->toArray();

        $shippingFee = 25000;

        return view('client.checkout', compact('userInfo', 'mappedItems', 'shippingFee'))->with(['items' => $mappedItems]);
    }


    private function getShippingFee($method)
    {
        return match ($method) {
            'standard' => 25000,
            'express' => 100000,
            default => throw new \Exception('Phương thức vận chuyển không hợp lệ'),
        };
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'delivery_method' => 'required|in:standard,express',
            'phuong_thuc_thanh_toan_id' => 'required|in:1,2',
            'items' => 'required|array|min:1',
            'tong_tien' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'delivery_method.required' => 'Vui lòng chọn phương thức giao hàng.',
            'phuong_thuc_thanh_toan_id.required' => 'Vui lòng chọn phương thức thanh toán.',
            'items.required' => 'Giỏ hàng không được để trống.',
            'tong_tien.required' => 'Tổng tiền không được để trống.',
        ]);

        // Tính lại tổng tiền sản phẩm
        $productTotal = collect($request->items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Tính phí vận chuyển
        $shippingFee = $request->shipping_fee;

        // Tính tổng tiền
        $expectedTotal = $productTotal + $shippingFee;

        // So sánh tổng tiền
        if (round($request->tong_tien, 2) != round($expectedTotal, 2)) {
            return back()->with('error', 'Tổng tiền không khớp. Vui lòng thử lại.');
        }

        // Chọn phương thức thanh toán
        $paymentMethod = match ((int)$request->phuong_thuc_thanh_toan_id) {
            1 => 'cash',
            2 => 'vnpay',
            default => 'cash',
        };

        // Tạo mã đơn hàng
        $orderCode = 'ORD' . now()->format('Ymd') . strtoupper(Str::random(4));

        try {
            $order = null;
            DB::transaction(function () use ($request, $paymentMethod, $shippingFee, $expectedTotal, $orderCode, &$order) {
                // Tạo đơn hàng
                $order = new \App\Models\Order();
                $order->order_code = $orderCode;
                $order->user_id = $request->user()->id;
                $order->name = $request->name;
                $order->email = $request->email ?? $request->user()->email;
                $order->phone = $request->phone ?? $request->user()->phone;
                $order->address = $request->address;
                $order->note = $request->note;
                $order->payment_method = $paymentMethod;
                $order->shipping_method = $request->delivery_method;
                $order->shipping_fee = $shippingFee;
                $order->total_money = $expectedTotal;
                $order->status = 'pending';
                $order->payment_status = 'unpaid';
                $order->save();

                // Lưu chi tiết + trừ kho từng variant
                foreach ($request->items as $item) {
                    $variant = \App\Models\ProductVariant::find($item['product_variant_id']);
                    if (!$variant) {
                        throw new \Exception('Sản phẩm không tồn tại!');
                    }
                    if ($variant->stock < $item['quantity']) {
                        throw new \Exception('Sản phẩm "' . ($variant->name ?? '') . '" không đủ hàng trong kho!');
                    }

                    // Trừ tồn kho
                    $variant->decrement('stock', $item['quantity']);
                    $product = $variant->product;
                    if ($product) {
                        $product->updateStockFromVariants();
                    }

                    $order->items()->create([
                        'product_variant_id' => $item['product_variant_id'],
                        'quantity' => $item['quantity'],
                        'price_at_time' => $item['price'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);
                }
                $cartItemIds = collect($request->items)->pluck('cart_item_id')->toArray();

                $cart = $this->getOrCreateCart($request->user());
                $cart->items()->whereIn('id', $cartItemIds)->delete();
                if ($cart->items()->count() == 0) {
                    Session::forget('cart_id');
                }
            });

            // Nếu chọn VNPay, chuyển hướng đến thanh toán
            if ($paymentMethod === 'vnpay' && $order) {
                return redirect()->route('client.payment.vnpay.order', ['order_id' => $order->id]);
            }

            return redirect()->route('order.success')->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function buyNow(Request $request)
    {
        // Nếu chưa đăng nhập, lưu thông tin vào session rồi redirect login
        if (!Auth::check()) {
            Session::put('buy_now_product', [
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity ?? 1,
            ]);
            return redirect()->route('login')->with('alert', 'Bạn cần đăng nhập để sử dụng tính năng mua ngay.');
        }

        // Nếu đã đăng nhập, chuyển thẳng đến trang checkout với dữ liệu buy_now
        return $this->showBuyNowCheckout($request);
    }

    // Trang checkout cho "mua ngay"
    public function showBuyNowCheckout(Request $request)
    {
        $user = Auth::user();

        // Nếu từ login quay lại, lấy từ session ra
        $buyNow = $request->has('product_variant_id')
            ? [
                'product_variant_id' => $request->product_variant_id,
                'quantity' => $request->quantity ?? 1,
            ]
            : Session::pull('buy_now_product');

        if (!$buyNow || empty($buyNow['product_variant_id'])) {
            return redirect('/')->with('error', 'Không tìm thấy sản phẩm để mua ngay.');
        }

        // Lấy thông tin variant
        $variant = ProductVariant::with('product')->find($buyNow['product_variant_id']);
        if (!$variant) {
            return redirect('/')->with('error', 'Sản phẩm không tồn tại.');
        }

        $item = [
            'product_variant_id' => $variant->id,
            'id' => $variant->product->id,
            'name' => $variant->product->name,
            'price' => $variant->price,
            'quantity' => $buyNow['quantity'],
            'image' => $variant->image ? Storage::url($variant->image) : asset('images/no-image.png'),
            'cart_item_id' => null, // Không qua giỏ hàng
            'subtotal' => $variant->price * $buyNow['quantity'],
        ];

        $userInfo = [
            'name'    => $user->name ?? '',
            'email'   => $user->email ?? '',
            'phone'   => $user->phone ?? '',
            'address' => $user->address ?? '',
        ];

        $shippingFee = 25000;

        // Lưu ý: truyền $items là mảng 1 phần tử
        return view('client.checkout', [
            'userInfo' => $userInfo,
            'mappedItems' => [$item],
            'shippingFee' => $shippingFee,
            'items' => [$item],
            'buyNow' => true, // Để view biết là “mua ngay”
        ]);
    }
}
