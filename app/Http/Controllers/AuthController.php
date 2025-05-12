<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function postLogin(Request $req)
    {
        $req->validate(
            [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:6'
            ],
            [
                'email.required' => 'Không được để trống email',
                'email.email' => 'Email không đúng định dạng',
                'email.exists' => 'Email chưa được đăng ký',
                'password.required' => 'Mật khẩu không được để trống',
                'password.string' => 'Mật khẩu không phải là chuỗi',
                'password.min' => 'Mật khẩu có độ dài quá ngắn'
            ]
        );

        if (Auth::attempt(['email' => $req->email, 'password' => $req->password], $req->filled('remember'))) {
            $req->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('dashboard');
            }
            return redirect()->route('home');
        }

        return redirect()->back()->with([
            'messageError' => 'Email hoặc mật khẩu không chính xác'
        ]);
    }

    public function register()
    {
        return view('register');
    }

    public function postRegister(Request $req)
    {
        $req->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:15|unique:users',
                'gender' => 'required|in:male,female,other',
                'address' => 'required|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ],
            [
                'name.required' => 'Tên không được để trống',
                'email.required' => 'Không được để trống email',
                'email.email' => 'Email không đúng định dạng',
                'email.unique' => 'Email đã được đăng ký',
                'phone.required' => 'Số điện thoại không được để trống',
                'phone.unique' => 'Số điện thoại đã được đăng ký',
                'gender.required' => 'Vui lòng chọn giới tính',
                'gender.in' => 'Giới tính không hợp lệ',
                'address.required' => 'Địa chỉ không được để trống',
                'address.max' => 'Địa chỉ quá dài',
                'password.required' => 'Mật khẩu không được để trống',
                'password.string' => 'Mật khẩu không hợp lệ',
                'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
                'password.confirmed' => 'Mật khẩu nhập lại không khớp',
            ]
        );

        $user = new User();
        $user->name = $req->name;
        $user->email = $req->email;
        $user->phone = $req->phone;
        $user->gender = $req->gender;
        $user->address = $req->address;
        $user->role = 'user'; // Thêm role mặc định là 'user'
        $user->password = Hash::make($req->password);
        $user->save();

        Auth::login($user);

        // Chuyển hướng đến home thay vì login
        return redirect()->route('home')->with('success', 'Đăng ký thành công!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('messageError', 'Đăng xuất thành công');
    }
}
