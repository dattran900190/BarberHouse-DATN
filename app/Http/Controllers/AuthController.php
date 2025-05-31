<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            return $user->role === 'admin'
                ? redirect()->route('dashboard')
                : redirect()->route('home');
        }

        return redirect()->back()->with([
            'messageError' => 'Email hoặc mật khẩu không chính xác'
        ]);
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
