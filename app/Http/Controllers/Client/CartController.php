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
use App\Events\NewOrderCreated;
use App\Mail\OrderSuccessMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


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
        
        // Kiểm tra quyền truy cập cho admin và admin_branch
        if ($user && in_array($user->role, ['admin', 'admin_branch'])) {
            $message = 'Bạn không có quyền thực hiện hành động này';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 403)
                : redirect()->back()->with('error', $message);
        }
        
        $productVariant = ProductVariant::find($request->product_variant_id);

        if (!$productVariant) {
            $message = 'Sản phẩm không tồn tại.';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $message], 404)
                : redirect()->back()->with('error', $message);
        }

        $cart = $this->getOrCreateCart($user);

        if (!$user) {
            Session::put('cart_id', $cart->id);
        }

        $cartItem = $cart->items()->where('product_variant_id', $request->product_variant_id)->first();

        $existingQty = $cartItem ? $cartItem->quantity : 0;
        $newQty = $existingQty + $request->quantity;
        if ($existingQty >= $productVariant->stock) {
            $message = "Bạn đã thêm tối đa số lượng có sẵn của sản phẩm này ({$productVariant->stock} sản phẩm).";
            return $request->ajax()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                    'available_stock' => $productVariant->stock,
                    'reached_max' => true
                ], 400)
                : redirect()->back()->with('error', $message);
        }
        if ($newQty > $productVariant->stock) {
            $message = "Chỉ còn {$productVariant->stock} sản phẩm trong kho. Bạn đang có {$existingQty} sản phẩm trong giỏ.";
            return $request->ajax()
                ? response()->json([
                    'success' => false,
                    'message' => $message,
                    'available_stock' => $productVariant->stock,
                ], 400)
                : redirect()->back()->with('error', $message);
        }



        if ($cartItem) {
            $cartItem->update([
                'quantity' => $newQty,
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
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng.',
                'cart_count' => $cart_count,
            ]);
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
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền cập nhật mục này.'
            ], 403);
        }

        // Kiểm tra tồn kho
        $variant = $cartItem->productVariant;
        if (!$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại.'
            ], 404);
        }

        if ($request->quantity > $variant->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Số lượng vượt quá tồn kho.',
                'available_stock' => $variant->stock
            ], 422);
        }

        // Cập nhật số lượng
        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        $cart_count = $cart->items()->sum('quantity');
        Session::put('cart_count', $cart_count);

        return response()->json([
            'success' => true,
            'unit_price' => round($cartItem->price),
            'subtotal' => round($cartItem->price * $cartItem->quantity),
            'cart_count' => $cart_count,
        ]);
    }




    public function show()
    {
        $user = Auth::user();

        // Nếu user đã đăng nhập và có session cart_id từ guest, merge cart
        if ($user && Session::has('cart_id')) {
            $this->mergeGuestCartToUser($user);
        }

        $cart = $this->getOrCreateCart($user);

        // Lấy lại item từ DB mới nhất (bao gồm cả sản phẩm đã soft delete)
        $items = CartItem::with(['productVariant' => function ($query) {
            $query->withTrashed(); // Include cả sản phẩm đã soft delete
        }, 'productVariant.product' => function ($query) {
            $query->withTrashed(); // Include cả sản phẩm đã soft delete
        }])
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

    /**
     * Chuyển giỏ hàng từ guest sang user
     */
    private function mergeGuestCartToUser($user)
    {
        $guestCartId = Session::get('cart_id');

        if (!$guestCartId) {
            return; // Không có giỏ hàng guest
        }

        $guestCart = Cart::where('id', $guestCartId)
            ->whereNull('user_id')
            ->with('items.productVariant')
            ->first();

        if (!$guestCart) {
            Session::forget('cart_id');
            return;
        }

        // Tìm hoặc tạo giỏ hàng của user
        $userCart = Cart::where('user_id', $user->id)->first();
        if (!$userCart) {
            $userCart = Cart::create(['user_id' => $user->id]);
        }

        // Chuyển các item từ guest cart sang user cart
        foreach ($guestCart->items as $guestItem) {
            // Kiểm tra xem item này đã có trong user cart chưa
            $existingItem = $userCart->items()
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existingItem) {
                // Nếu đã có, cộng thêm số lượng
                $existingItem->update([
                    'quantity' => $existingItem->quantity + $guestItem->quantity,
                    'price' => $guestItem->price // Cập nhật giá mới nhất
                ]);
            } else {
                // Nếu chưa có, tạo mới
                $userCart->items()->create([
                    'product_variant_id' => $guestItem->product_variant_id,
                    'quantity' => $guestItem->quantity,
                    'price' => $guestItem->price
                ]);
            }
        }

        // Xóa cart_items trước, sau đó xóa cart để tránh lỗi foreign key
        $guestCart->items()->delete();
        $guestCart->delete();
        Session::forget('cart_id');

        // Cập nhật số lượng trong session
        $cartCount = $userCart->items()->sum('quantity');
        Session::put('cart_count', $cartCount);
    }

    public function updateVariant(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
        ], [
            'product_variant_id.required' => 'Vui lòng chọn dung tích sản phẩm.',
            'product_variant_id.exists' => 'Dung tích sản phẩm không tồn tại.',
        ]);

        $newVariant = ProductVariant::findOrFail($request->product_variant_id);
        $cart = $this->getOrCreateCart(Auth::user() ?? null);

        if ($cartItem->cart_id !== $cart->id) {
            return redirect()->route('cart.show')->with('error', 'Bạn không có quyền cập nhật mục này.');
        }

        $existingItem = $cart->items()
            ->where('product_variant_id', $newVariant->id)
            ->where('id', '!=', $cartItem->id)
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $cartItem->quantity;

            if ($newQuantity > $newVariant->stock) {
                return redirect()->route('cart.show')->with('error', "Lưu ý: Chỉ còn {$newVariant->stock} sản phẩm {$newVariant->product->name} dung tích {$newVariant->volume->name}.");
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'price' => round($newVariant->price),
            ]);

            $cartItem->delete();

            $cart_count = $cart->items()->sum('quantity');
            Session::put('cart_count', $cart_count);

            return redirect()->route('cart.show');
        } else {
            if ($cartItem->quantity > $newVariant->stock) {
                return redirect()->route('cart.show')->with('error', "Số lượng vượt quá tồn kho. Chỉ còn {$newVariant->stock} sản phẩm.");
            }

            $cartItem->update([
                'product_variant_id' => $newVariant->id,
                'price' => round($newVariant->price),
            ]);

            $cart_count = $cart->items()->sum('quantity');
            Session::put('cart_count', $cart_count);

            return redirect()->route('cart.show')->with('success', 'Cập nhật dung tích thành công.');
        }
    }
    public function checkout(Request $request)
    {
        $user = Auth::user();
        
        // Kiểm tra quyền truy cập cho admin và admin_branch
        if ($user && in_array($user->role, ['admin', 'admin_branch'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này');
        }
        
        $cart = $this->getOrCreateCart($user);

        // Nếu không truyền gì lên thì lấy toàn bộ, còn nếu có thì lọc lại
        $selectedIds = [];
        if ($request->has('checkout_items')) {
            $selectedItems = json_decode($request->checkout_items, true) ?? [];
            $selectedIds = array_column($selectedItems, 'id');
        }

        if (empty($selectedIds)) {
            return redirect()->route('cart.show');
        }

        // Lấy các item được chọn
        $items = $cart->items()->with('productVariant.product', 'productVariant.volume')
            ->whereIn('id', $selectedIds)
            ->get();

        $quantityMap = collect($selectedItems)->keyBy('id')->map(fn($item) => $item['quantity']);

        foreach ($items as $item) {
            if (isset($quantityMap[$item->id])) {
                $item->quantity = $quantityMap[$item->id];
            }
        }
        foreach ($items as $item) {
            $stock = $item->productVariant->stock ?? 0;
            if ($item->quantity > $stock) {
                return redirect()
                    ->route('cart.show')
                    ->with('error', "Sản phẩm {$item->productVariant->product->name} dung tích ({$item->productVariant->volume->name}) chỉ còn {$stock} sản phẩm trong kho.");
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
                'volume_name' => $item->productVariant->volume->name ?? '',
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
        try {
            // Validation
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10,11}$/',
                'address' => 'required|string|max:255',
                'delivery_method' => 'required|in:standard,express',
                'phuong_thuc_thanh_toan_id' => 'required|in:1,2',
                'items' => 'required|array|min:1',
                'tong_tien' => 'required|numeric|min:0',
            ], [
                'name.required' => 'Vui lòng nhập tên.',
                'phone.required' => 'Vui lòng nhập số điện thoại.',
                'phone.regex' => 'Số điện thoại không hợp lệ. Vui lòng nhập 10 hoặc 11 số.',
                'address.required' => 'Vui lòng nhập địa chỉ.',
                'delivery_method.required' => 'Vui lòng chọn phương thức giao hàng.',
                'phuong_thuc_thanh_toan_id.required' => 'Vui lòng chọn phương thức thanh toán.',
                'items.required' => 'Giỏ hàng không được để trống.',
                'tong_tien.required' => 'Tổng tiền không được để trống.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Kiểm tra đăng nhập
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn cần đăng nhập để đặt hàng.',
                ], 401);
            }

            $user = $request->user();
            
            // Kiểm tra quyền truy cập cho admin và admin_branch
            if (in_array($user->role, ['admin', 'admin_branch'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện hành động này',
                ], 403);
            }

            // Cập nhật lại thông tin user nếu còn thiếu
            $needUpdate = false;
            if (empty($user->phone) && $request->filled('phone')) {
                $user->phone = $request->phone;
                $needUpdate = true;
            }
            if (empty($user->address) && $request->filled('address')) {
                $user->address = $request->address;
                $needUpdate = true;
            }
            if ($needUpdate) {
                $user->save();
            }

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
                return response()->json([
                    'success' => false,
                    'message' => 'Tổng tiền không khớp. Vui lòng thử lại.',
                ], 422);
            }

            // Chọn phương thức thanh toán
            $paymentMethod = match ((int)$request->phuong_thuc_thanh_toan_id) {
                1 => 'cash',
                2 => 'vnpay',
                default => 'cash',
            };

            // Nếu thanh toán VNPAY, lưu thông tin vào session và chuyển hướng đến thanh toán
            if ($paymentMethod === 'vnpay') {
                // Lưu thông tin đơn hàng vào session để sử dụng sau khi thanh toán thành công
                Session::put('pending_order_data', [
                    'name' => $request->name,
                    'email' => $request->email ?? $user->email,
                    'phone' => $request->phone ?? $user->phone,
                    'address' => $request->address,
                    'note' => $request->note,
                    'delivery_method' => $request->delivery_method,
                    'shipping_fee' => $shippingFee,
                    'total_money' => $expectedTotal,
                    'items' => $request->items,
                    'buy_now' => $request->has('buy_now'),
                ]);

                // Tạo URL thanh toán VNPAY
                $vnpayUrl = $this->createVnpayPaymentUrl($expectedTotal, $request);

                return response()->json([
                    'success' => true,
                    'message' => 'Chuyển hướng đến trang thanh toán VNPAY',
                    'payment_method' => 'vnpay',
                    'redirect_url' => $vnpayUrl,
                ], 200);
            }

            // Nếu thanh toán COD, tạo đơn hàng ngay
            $orderCode = 'ORD' . now()->format('Y') . strtoupper(Str::random(4));

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
                    $variant = ProductVariant::find($item['product_variant_id']);
                    if (!$variant) {
                        throw new \Exception('Sản phẩm không tồn tại!');
                    }
                    if ($variant->stock < $item['quantity']) {
                        throw new \Exception('Sản phẩm "' . ($variant->name ?? '') . '" không đủ hàng trong kho!');
                    }

                    // Trừ tồn kho
                    $variant->decrement('stock', $item['quantity']);

                    $product = $variant->product;
                    if ($product && method_exists($product, 'updateStockFromVariants')) {
                        $product->updateStockFromVariants();
                    }

                    $order->items()->create([
                        'product_variant_id' => $item['product_variant_id'],
                        'quantity' => $item['quantity'],
                        'price_at_time' => $item['price'],
                        'total_price' => $item['price'] * $item['quantity'],
                        'volume_name' => $variant->volume->name ?? null,
                    ]);
                }

                // Xóa sản phẩm khỏi giỏ hàng
                $cartItemIds = collect($request->items)->pluck('cart_item_id')->toArray();
                $cart = $this->getOrCreateCart($request->user());
                $cart->items()->whereIn('id', $cartItemIds)->delete();

                // Cập nhật lại số lượng còn lại trong giỏ
                $cart_count = $cart->items()->sum('quantity');
                Session::put('cart_count', $cart_count);
                if ($cart->items()->count() == 0) {
                    Session::forget('cart_id');
                }
            });

            // Phát event realtime khi có đơn hàng mới
            if ($order) {
                event(new NewOrderCreated($order));

                // Gửi email xác nhận cho đơn hàng COD
                try {
                    $order->load('items.productVariant.product');
                    if ($order->email) {
                        Mail::to($order->email)->queue(new OrderSuccessMail($order));
                    }
                } catch (\Exception $e) {
                    Log::error('Lỗi gửi email xác nhận đơn hàng: ' . $e->getMessage());
                }
            }

            // Trả về JSON khi thành công cho COD
            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'order_id' => $order ? $order->id : null,
                'payment_method' => $paymentMethod,
                'redirect_url' => route('home'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tạo URL thanh toán VNPAY
     */
    private function createVnpayPaymentUrl($amount, $request)
    {
        // Cấu hình VNPay
        $vnp_TmnCode = config('payment.vnpay.tmn_code');
        $vnp_HashSecret = config('payment.vnpay.hash_secret');
        $vnp_Url = config('payment.vnpay.url');
        $vnp_Returnurl = route('client.payment.vnpay.checkout.callback');

        // Tạo mã giao dịch tạm thời
        $vnp_TxnRef = 'TEMP_' . now()->format('YmdHis') . '_' . strtoupper(Str::random(6));

        // Lưu mã giao dịch vào session
        Session::put('vnpay_txn_ref', $vnp_TxnRef);

        $vnp_Amount = $amount * 100; // Số tiền (VND, nhân 100 theo yêu cầu VNPay)
        $vnp_Locale = 'vn';
        $vnp_BankCode = 'NCB'; // Mã ngân hàng (mặc định NCB)
        $vnp_IpAddr = $request->ip();
        $vnp_OrderInfo = 'Thanh toan don hang ' . $vnp_TxnRef;

        // Dữ liệu gửi đến VNPay
        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        // Sắp xếp dữ liệu và tạo chữ ký bảo mật
        ksort($inputData);
        $query = http_build_query($inputData);
        $vnp_SecureHash = hash_hmac('sha512', $query, $vnp_HashSecret);
        $vnp_Url .= '?' . $query . '&vnp_SecureHash=' . $vnp_SecureHash;

        return $vnp_Url;
    }

    /**
     * Callback từ VNPAY sau khi thanh toán
     */
    public function vnpayCheckoutCallback(Request $request)
    {
        try {
            // Lấy dữ liệu từ VNPay
            $vnp_HashSecret = config('payment.vnpay.hash_secret');
            $inputData = $request->all();
            $vnp_SecureHash = $inputData['vnp_SecureHash'];
            unset($inputData['vnp_SecureHash']);

            // Tạo chữ ký để kiểm tra bảo mật
            ksort($inputData);
            $hashData = http_build_query($inputData);
            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            // Kiểm tra chữ ký và trạng thái giao dịch
            if ($secureHash === $vnp_SecureHash && $inputData['vnp_ResponseCode'] == '00') {
                // Thanh toán thành công, tạo đơn hàng
                $orderData = Session::get('pending_order_data');
                if (!$orderData) {
                    return redirect()->route('cart.show')->with('error', 'Không tìm thấy thông tin đơn hàng.');
                }

                // Tạo đơn hàng
                $orderCode = 'ORD' . now()->format('Y') . strtoupper(Str::random(4));
                $order = null;

                DB::transaction(function () use ($orderData, $orderCode, &$order) {
                    // Tạo đơn hàng
                    $order = new \App\Models\Order();
                    $order->order_code = $orderCode;
                    $order->user_id = Auth::id();
                    $order->name = $orderData['name'];
                    $order->email = $orderData['email'];
                    $order->phone = $orderData['phone'];
                    $order->address = $orderData['address'];
                    $order->note = $orderData['note'];
                    $order->payment_method = 'vnpay';
                    $order->shipping_method = $orderData['delivery_method'];
                    $order->shipping_fee = $orderData['shipping_fee'];
                    $order->total_money = $orderData['total_money'];
                    $order->status = 'pending';
                    $order->payment_status = 'paid';
                    $order->save();

                    // Lưu chi tiết + trừ kho từng variant
                    foreach ($orderData['items'] as $item) {
                        $variant = ProductVariant::find($item['product_variant_id']);
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
                            'volume_name' => $variant->volume->name ?? null,
                        ]);
                    }

                    // Xóa sản phẩm khỏi giỏ hàng nếu không phải buy now
                    if (!$orderData['buy_now']) {
                        $cartItemIds = collect($orderData['items'])->pluck('cart_item_id')->toArray();
                        $cart = $this->getOrCreateCart(Auth::user());
                        $cart->items()->whereIn('id', $cartItemIds)->delete();

                        // Cập nhật lại số lượng còn lại trong giỏ
                        $cart_count = $cart->items()->sum('quantity');
                        Session::put('cart_count', $cart_count);
                        if ($cart->items()->count() == 0) {
                            Session::forget('cart_id');
                        }
                    }
                });

                // Phát event realtime khi có đơn hàng mới
                if ($order) {
                    event(new NewOrderCreated($order));

                    // Gửi email xác nhận
                    try {
                        $order->load('items.productVariant.product');
                        if ($order->email) {
                            Mail::to($order->email)->queue(new OrderSuccessMail($order));
                        }
                    } catch (\Exception $e) {
                        Log::error('Lỗi gửi email xác nhận đơn hàng VNPAY: ' . $e->getMessage());
                    }
                }

                // Xóa dữ liệu tạm thời
                Session::forget('pending_order_data');
                Session::forget('vnpay_txn_ref');

                return redirect()->route('home')->with('success', 'Thanh toán thành công! Đơn hàng của bạn đã được tạo.');
            } else {
                // Thanh toán thất bại
                Session::forget('pending_order_data');
                Session::forget('vnpay_txn_ref');
                return redirect()->route('cart.show')->with('error', 'Thanh toán thất bại! Vui lòng thử lại.');
            }
        } catch (\Exception $e) {
            Session::forget('pending_order_data');
            Session::forget('vnpay_txn_ref');
            return redirect()->route('cart.show')->with('error', 'Đã có lỗi xảy ra: ' . $e->getMessage());
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

        // Kiểm tra quyền truy cập cho admin và admin_branch
        $user = Auth::user();
        if (in_array($user->role, ['admin', 'admin_branch'])) {
            return redirect()->back()->with('error', 'Bạn không có quyền thực hiện hành động này');
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
            : Session::get('buy_now_product'); // ✅ không xóa


        if (!$buyNow || empty($buyNow['product_variant_id'])) {
            return redirect('/')->with('error', 'Không tìm thấy sản phẩm để mua ngay.');
        }

        // Lấy thông tin variant
        $variant = ProductVariant::with('product', 'volume')->find($buyNow['product_variant_id']);
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
            'cart_item_id' => null,
            'subtotal' => $variant->price * $buyNow['quantity'],
            'volume_name' => $variant->volume->name ?? '',
        ];

        $userInfo = [
            'name'    => $user->name ?? '',
            'email'   => $user->email ?? '',
            'phone'   => $user->phone ?? '',
            'address' => $user->address ?? '',
        ];

        $shippingFee = 25000;

        // Nếu thiếu SĐT hoặc địa chỉ thì cảnh báo


        return view('client.checkout', [
            'userInfo' => $userInfo,
            'mappedItems' => [$item],
            'shippingFee' => $shippingFee,
            'items' => [$item],
            'buyNow' => true,
        ]);
    }
}
