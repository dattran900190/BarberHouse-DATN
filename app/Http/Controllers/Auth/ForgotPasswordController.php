<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('client.forgotPassword');
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email không tồn tại trong hệ thống.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Xoá OTP cũ
        Otp::where('email', $request->email)->delete();

        $otp = rand(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(2);

        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        // Gửi mail
        Mail::raw("Mã OTP của bạn là: $otp. Có hiệu lực trong 2 phút.", function ($message) use ($request) {
            $message->to($request->email)->subject('Mã OTP khôi phục mật khẩu');
        });

        return redirect()->route('password.verifyForm')->with('email', $request->email);
    }

    public function showVerifyForm(Request $request)
    {
        // Ưu tiên lấy từ session, nếu không có thì fallback về input cũ
        $email = session('email') ?? old('email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Vui lòng nhập email trước.']);
        }

        return view('client.verify-otp', compact('email'));
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'otp.required' => 'Vui lòng nhập mã OTP.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return redirect()->route('password.verifyForm')->withInput()->withErrors([
                'otp' => 'Mã OTP không hợp lệ hoặc đã hết hạn.',
            ])->with('email', $request->email);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        $otpRecord->delete();

        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công.');
    }
}
