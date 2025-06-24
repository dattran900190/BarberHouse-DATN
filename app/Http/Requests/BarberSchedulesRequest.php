<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarberSchedulesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'barber_id'      => 'required|exists:barbers,id',
            'schedule_date'  => 'required|date|after_or_equal:today',
            'status'         => 'required|in:off,custom',

            // Chỉ yêu cầu giờ nếu là custom
            'start_time'     => 'required_if:status,custom|nullable|date_format:H:i',
            'end_time'       => 'required_if:status,custom|nullable|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'barber_id.required'      => 'Vui lòng chọn thợ cắt tóc.',
            'barber_id.exists'        => 'Thợ cắt tóc không tồn tại.',

            'schedule_date.required'  => 'Vui lòng chọn ngày.',
            'schedule_date.date'      => 'Ngày làm việc không hợp lệ.',
            'schedule_date.after_or_equal' => 'Ngày làm việc phải là hôm nay hoặc sau hôm nay.',

            'status.required'         => 'Vui lòng chọn loại lịch.',
            'status.in'               => 'Loại lịch không hợp lệ.',
            'status.off'              => 'Lịch nghỉ cả ngày không thể có giờ làm.',
            'start_time.required_if'  => 'Vui lòng nhập giờ bắt đầu khi chọn thay đổi giờ làm.',
            'start_time.date_format'  => 'Giờ bắt đầu không đúng định dạng (H:i).',

            'end_time.required_if'    => 'Vui lòng nhập giờ kết thúc khi chọn thay đổi giờ làm.',
            'end_time.date_format'    => 'Giờ kết thúc không đúng định dạng (H:i).',
            'end_time.after'          => 'Giờ kết thúc phải sau giờ bắt đầu.',
        ];
    }
}
