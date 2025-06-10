<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barber_id' => 'required|exists:barbers,id',
            'branch_id' => 'required|exists:branches,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => [
                'required',
                'regex:/^([01]\d|2[0-3]):([0-5]\d)$/', // định dạng HH:MM 24h
                function ($attribute, $value, $fail) {
                    // Giới hạn giờ trong khoảng 08:00 - 20:00
                    if ($value < '08:00' || $value > '20:00') {
                        $fail('Giờ đặt lịch phải từ 08:00 đến 20:00.');
                    }

                    // Không cho đặt lịch quá khứ nếu ngày là hôm nay
                    $date = $this->input('appointment_date');
                    if ($date === now()->toDateString() && $value < now()->format('H:i')) {
                        $fail('Không thể đặt lịch ở thời điểm đã qua.');
                    }
                }
            ],
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
        ];
    }
}
