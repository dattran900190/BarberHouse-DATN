<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{


    public function vnpayPayment(Request $request)
    {
        // Lấy thông tin lịch hẹn
        $appointmentId = $request->input('appointment_id');
        $appointment = Appointment::findOrFail($appointmentId);

        // Kiểm tra người dùng có quyền thanh toán lịch hẹn này
        if ($appointment->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền thanh toán lịch hẹn này.');
        }

        // Cấu hình VNPay
        $vnp_TmnCode = config('payment.vnpay.tmn_code');
        $vnp_HashSecret = config('payment.vnpay.hash_secret');
        $vnp_Url = config('payment.vnpay.url');
        $vnp_Returnurl = config('payment.vnpay.return_url');

        // Thông tin giao dịch
        $vnp_TxnRef = $appointment->appointment_code; // Mã giao dịch duy nhất
        $vnp_Amount = $appointment->total_amount * 100; // Số tiền (VND, nhân 100 theo yêu cầu VNPay)
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->input('bank_code', 'NCB'); // Mã ngân hàng (mặc định NCB)
        $vnp_IpAddr = $request->ip();
        $vnp_OrderInfo = 'Thanh toan lich hen ' . $vnp_TxnRef;

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

        // Chuyển hướng đến trang thanh toán VNPay
        return redirect($vnp_Url);
    }

    public function vnpayCallback(Request $request)
    {
        // Lấy dữ liệu từ VNPay
        $vnp_HashSecret = config('payment.vnpay.hash_secret');
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);

        // Tạo chữ ký để kiểm tra bảo mật
        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Tìm lịch hẹn
        $appointment = Appointment::where('appointment_code', $inputData['vnp_TxnRef'])->first();

        // Kiểm tra chữ ký và trạng thái giao dịch
        if ($appointment && $secureHash === $vnp_SecureHash && $inputData['vnp_ResponseCode'] == '00') {
            $appointment->payment_status = 'paid';
            $appointment->payment_method = 'vnpay';
            $appointment->save();
            return redirect()->route('dat-lich')->with('success', 'Thanh toán thành công!');
        } else {
            if ($appointment) {
                $appointment->payment_status = 'failed';
                $appointment->save();
            }
            return redirect()->route('dat-lich')->with('error', 'Thanh toán thất bại!');
        }
    }
}
