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

        return redirect()->route('cart.show')->with('success', 'Sản phẩm đã được xóa khỏi giỏ hàng.');
    }



    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);

        if ($cartItem->cart_id !== $cart->id) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền cập nhật mục này.'], 403);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json(['success' => true, 'message' => 'Số lượng sản phẩm đã được cập nhật.']);
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
            return [
                'product_variant_id' => $item->productVariant->id,
                'id' => $item->productVariant->product->id,
                'name' => $item->productVariant->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'image' => $item->productVariant->image ? Storage::url($item->productVariant->image) : asset('images/no-image.png'),
                'cart_item_id' => $item->id,
            ];
        })->toArray();



        $shippingFee = 25000;

        return view('client.checkout', compact('userInfo', 'mappedItems', 'shippingFee'))->with(['items' => $mappedItems]);
    }



    private function getShippingFee($method)
    {
        return match ($method) {
            'standard' => 25000,
            'express' => 45000,
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
        ]);

        $user = Auth::user();
        $cart = $this->getOrCreateCart($user);
        $shippingFee = $this->getShippingFee($request->delivery_method);

        // Tính lại tổng tiền sản phẩm
        $productTotal = collect($request->items)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
        $expectedTotal = $productTotal + $shippingFee;

        // So sánh chính xác với float
        if (round((float)$request->tong_tien, 2) != round($expectedTotal, 2)) {
            return back()->with('error', 'Tổng tiền không khớp. Vui lòng thử lại.');
        }

        $paymentMethod = match ((int)$request->phuong_thuc_thanh_toan_id) {
            1 => 'cash',
            2 => 'vnpay',
            default => 'cash',
        };

        $orderCode = 'ORD' . now()->format('Ymd') . strtoupper(Str::random(6));

        $order = new \App\Models\Order();
        $order->order_code = $orderCode;
        $order->user_id = $user?->id;
        $order->name = $request->name;
        $order->email = $request->email ?? $user?->email;
        $order->phone = $request->phone ?? $user?->phone;
        $order->address = $request->address;
        $order->note = $request->note;
        $order->payment_method = $paymentMethod;
        $order->shipping_fee = $shippingFee;
        $order->total_money = $expectedTotal;
        $order->status = 'pending';
        $order->save();

        foreach ($request->items as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'image' => $item['image'] ?? null,
            ]);
        }

        $cart->items()->delete();
        Session::forget('cart_id');

        return redirect()->route('order.success')->with('success', 'Đặt hàng thành công!');
    }
}
