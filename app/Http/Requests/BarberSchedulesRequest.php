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
        $rules = [
            'status' => 'required|in:off,custom,holiday',
        ];

        if ($this->input('status') === 'holiday') {
            $rules['holiday_start_date'] = 'required|date|after_or_equal:today';
            $rules['holiday_end_date'] = 'required|date|after_or_equal:holiday_start_date';
            $rules['note'] = 'required|string|max:255';
        } else {
            $rules['barber_id'] = 'required|exists:barbers,id';
            $rules['schedule_date'] = 'required|date|after_or_equal:today';

            if ($this->input('status') === 'custom') {
                $rules['start_time'] = 'required|date_format:H:i';
                $rules['end_time'] = 'required|date_format:H:i|after:start_time';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            // Chung
            'status.required' => 'Vui lòng chọn loại lịch.',
            'status.in' => 'Loại lịch không hợp lệ.',

            // Với lịch thợ thường
            'barber_id.required' => 'Vui lòng chọn thợ cắt tóc.',
            'barber_id.exists' => 'Thợ cắt tóc không tồn tại.',
            'schedule_date.required' => 'Vui lòng chọn ngày.',
            'schedule_date.date' => 'Ngày làm việc không hợp lệ.',
            'schedule_date.after_or_equal' => 'Ngày làm việc phải là hôm nay hoặc sau hôm nay.',
            'start_time.required' => 'Vui lòng nhập giờ bắt đầu.',
            'start_time.date_format' => 'Giờ bắt đầu không đúng định dạng (H:i).',
            'end_time.required' => 'Vui lòng nhập giờ kết thúc.',
            'end_time.date_format' => 'Giờ kết thúc không đúng định dạng (H:i).',
            'end_time.after' => 'Giờ kết thúc phải sau giờ bắt đầu.',

            // Với nghỉ lễ
            'holiday_start_date.required' => 'Vui lòng chọn ngày bắt đầu nghỉ lễ.',
            'holiday_start_date.date' => 'Ngày bắt đầu nghỉ lễ không hợp lệ.',
            'holiday_start_date.after_or_equal' => 'Ngày bắt đầu nghỉ phải từ hôm nay trở đi.',
            'holiday_end_date.required' => 'Vui lòng chọn ngày kết thúc nghỉ lễ.',
            'holiday_end_date.date' => 'Ngày kết thúc nghỉ lễ không hợp lệ.',
            'holiday_end_date.after_or_equal' => 'Ngày kết thúc nghỉ phải bằng hoặc sau ngày bắt đầu.',
            'note.required' => 'Vui lòng nhập tên kỳ nghỉ lễ.',
            'note.string' => 'Ghi chú không hợp lệ.',
            'note.max' => 'Ghi chú không được vượt quá 255 ký tự.',
        ];
    }
}
