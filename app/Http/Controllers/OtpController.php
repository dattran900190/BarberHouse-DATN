<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;

class OtpController extends Controller
{
    public function showForm()
    {
        return view('otp-form');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $otp = rand(100000, 999999);
        $phone = $request->phone;

        $basic  = new Basic(env('VONAGE_API_KEY'), env('VONAGE_API_SECRET'));
        $client = new Client($basic);

        try {
            $client->sms()->send(
                new \Vonage\SMS\Message\SMS($phone, env('VONAGE_SMS_FROM'), "Mã xác minh của bạn là: $otp")
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi gửi OTP: ' . $e->getMessage());
        }

        Session::put('otp_code', $otp);
        Session::put('otp_phone', $phone);

        return back()->with('success', 'Đã gửi mã xác minh!');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string'
        ]);

        if ($request->otp == Session::get('otp_code')) {
            return back()->with('success', 'Xác minh thành công!');
        } else {
            return back()->with('error', 'Sai mã xác minh.');
        }
    }
}
