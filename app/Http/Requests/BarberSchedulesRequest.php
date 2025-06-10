<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarberSchedulesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cho phép dùng request này, nếu bạn muốn kiểm tra quyền có thể sửa lại
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'barber_id' => 'required|exists:barbers,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }
    public function messages(): array
    {
        return [
            'barber_id.required' => 'Vui lòng chọn thợ cắt tóc.',
            'barber_id.exists' => 'Thợ cắt tóc không tồn tại.',

            'schedule_date.required' => 'Vui lòng chọn ngày.',
            'schedule_date.date' => 'Ngày không hợp lệ.',

            'start_time.required' => 'Vui lòng chọn giờ bắt đầu.',
            'start_time.date_format' => 'Giờ bắt đầu không đúng định dạng (H:i).',

            'end_time.required' => 'Vui lòng chọn giờ kết thúc.',
            'end_time.date_format' => 'Giờ kết thúc không đúng định dạng (H:i).',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',
        ];
    }
}
