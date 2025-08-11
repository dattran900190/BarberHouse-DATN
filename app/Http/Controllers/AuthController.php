<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\AuthRequest;

class AuthController extends Controller
{
    public function login()
    {
        return view('client.login');
    }

    public function postLogin(AuthRequest $req)
    {
        if (Auth::attempt(['email' => $req->email, 'password' => $req->password], $req->filled('remember'))) {
            $req->session()->regenerate();
            $user = Auth::user();

            // Chuyển giỏ hàng từ guest sang user và cập nhật cart_count
            $this->mergeGuestCartToUser($user);

            // Nếu có mua ngay, redirect checkout mua ngay
            if ($req->session()->has('buy_now_product')) {
                return redirect()->route('cart.buyNow.checkout');
            }

            // Bình thường
            if (in_array($user->role, ['admin', 'admin_branch'])) {
                return redirect()->route('dashboard');
            }

            return match ($user->role) {
                'admin' => redirect()->route('dashboard'),
                'admin_branch' => redirect()->route('branches.index'),
                default => redirect()->route('home'),
            };
        }

        return redirect()->back()->with([
            'messageError' => 'Email hoặc mật khẩu không chính xác'
        ]);
    }

    /**
     * Chuyển giỏ hàng từ guest sang user và cập nhật cart_count
     */
    private function mergeGuestCartToUser($user)
    {
        $guestCartId = Session::get('cart_id');
        
        // Tìm hoặc tạo giỏ hàng của user
        $userCart = Cart::where('user_id', $user->id)->first();
        if (!$userCart) {
            $userCart = Cart::create(['user_id' => $user->id]);
        }

        // Nếu có guest cart, merge vào user cart
        if ($guestCartId) {
            $guestCart = Cart::where('id', $guestCartId)
                            ->whereNull('user_id')
                            ->with('items.productVariant')
                            ->first();

            if ($guestCart) {
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
            }
        }
        
        // Luôn cập nhật số lượng trong session (dù có guest cart hay không)
        $cartCount = $userCart->items()->sum('quantity');
        Session::put('cart_count', $cartCount);
    }

    public function register()
    {
        return view('client.register');
    }

    public function postRegister(AuthRequest $req)
    {
        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->gender = $req->gender;
        $user->address = $req->address;
        $user->role = 'user';
        $user->status = 'active';
        $user->password = Hash::make($req->password);
        $user->save();

        Auth::login($user);

        // Chuyển giỏ hàng từ guest sang user (tương tự như login)
        $this->mergeGuestCartToUser($user);

        if (session()->has('buy_now_product')) {
            return redirect()->route('cart.buyNow.checkout');
        }
        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('messageError', 'Đăng xuất thành công');
    }
}
