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
        return redirect()->route('cart.show')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
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
    public function checkout()
    {
        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);

        $items = $cart->items()->with('productVariant.product')->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $userInfo = [
            'name'    => $user?->name ?? '',
            'email'   => $user?->email ?? '',
            'phone'   => $user?->phone ?? '',
            'address' => $user?->address ?? '',
        ];

        // Chuyển thành mảng dùng cho view
        $mappedItems = $items->map(function ($item) {
            $subtotal = $item->price * $item->quantity;  // Tính thành tiền
            return [
                'product_variant_id' => $item->productVariant->id,
                'id' => $item->productVariant->product->id,
                'name' => $item->productVariant->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $item->productVariant->image ? Storage::url($item->productVariant->image) : asset('images/no-image.png'),
                'cart_item_id' => $item->id,
                'subtotal' => $subtotal,  // Thêm subtotal
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
        $order->save();

        // Lưu các mục giỏ hàng vào đơn hàng
        foreach ($request->items as $item) {
            $order->items()->create([
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'price_at_time' => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
            ]);
        }

        $cart = $this->getOrCreateCart($request->user());
        $cart->items()->delete();
        Session::forget('cart_id');

        return redirect()->route('order.success')->with('success', 'Đặt hàng thành công!');
    }
}
