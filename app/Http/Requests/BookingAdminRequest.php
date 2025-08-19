<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Appointment;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class BookingAdminRequest extends FormRequest
{
    public function authorize()
    {
        // Cho phép tất cả yêu cầu, kiểm tra đăng nhập sẽ được xử lý trong controller
        return true;
    }

    public function rules(): array
    {
        return [
            'barber_id' => 'required|exists:barbers,id',
            'branch_id' => 'required|exists:branches,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'voucher_id' => 'nullable|exists:user_redeemed_vouchers,id,user_id,' . Auth::id(),
            'appointment_time' => [
                'required',
                'regex:/^([01]\d|2[0-3]):([0-5]\d)$/',
            ],
            'name' => 'required|string|max:100|required_if:other_person,1',
            // 'phone' => [
            //     'nullable',
            //     'required_if:other_person,1',
            //     'regex:/^0[0-9]{9}$/'
            // ],
            // 'email' => [
            //     'nullable',
            //     'required_if:other_person,1',
            //     'email:rfc,dns'
            // ],
            // 'payment_method' => 'required|in:cash,vnpay',
            'additional_services' => 'nullable|json',
            'additional_services.*' => 'exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'barber_id.required' => 'Vui lòng chọn thợ cắt.',
            'barber_id.exists' => 'Thợ cắt không tồn tại.',
            'branch_id.required' => 'Vui lòng chọn chi nhánh đặt lịch.',
            'branch_id.exists' => 'Chi nhánh không tồn tại.',
            'service_id.required' => 'Vui lòng chọn dịch vụ.',
            'service_id.exists' => 'Dịch vụ không tồn tại.',
            'appointment_date.required' => 'Vui lòng chọn ngày đặt lịch.',
            'appointment_date.date' => 'Ngày đặt lịch không hợp lệ.',
            'appointment_date.after_or_equal' => 'Ngày đặt lịch phải là hôm nay hoặc sau.',
            'appointment_time.required' => 'Vui lòng chọn thời gian đặt lịch.',
            'appointment_time.regex' => 'Thời gian phải có định dạng HH:MM (ví dụ: 14:30).',
            'name.string' => 'Tên không hợp lệ.',
            'name.required' => 'Vui lòng nhập tên khách hàng.',
            'name.max' => 'Tên quá dài (tối đa 100 ký tự).',
            'required_if' => 'Vui lòng nhập :attribute khi đặt cho người khác.',
            // 'phone.regex' => 'Số điện thoại không hợp lệ. Phải có 10 chữ số và bắt đầu bằng 0.',
            // 'email.required_if' => 'Vui lòng nhập email khi đặt cho người khác.',
            // 'email.email' => 'Email không hợp lệ.',
            // 'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
            'additional_services.*.exists' => 'Dịch vụ bổ sung không hợp lệ.',
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $appointmentTime = $this->input('appointment_time');
            $appointmentDate = $this->input('appointment_date');

            if ($appointmentTime) {
                // Kiểm tra giờ đặt lịch từ 08:00 đến 20:00
                if ($appointmentTime < '08:00' || $appointmentTime > '19:30') {
                    $validator->errors()->add('appointment_time', 'Giờ đặt lịch phải từ 08:00 đến 19:30.');
                }

                // Kiểm tra thời gian đã qua nếu ngày là hôm nay
                if ($appointmentDate === now()->toDateString()) {
                    $currentTime = now()->format('H:i');
                    $appointmentDateTime = Carbon::parse($appointmentDate . ' ' . $appointmentTime);
                    $currentDateTime = now();

                    if ($appointmentDateTime->lessThan($currentDateTime)) {
                        $validator->errors()->add('appointment_time', 'Không thể đặt lịch ở thời điểm đã qua.');
                    }
                }
            }
        });
    }
}
