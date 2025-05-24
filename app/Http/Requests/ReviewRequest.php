<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'       => 'required|exists:users,id',
            'barber_id'     => 'required|exists:barbers,id',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:1000',
            'appointment_id'=> 'nullable|exists:appointments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required'       => 'Người dùng không được để trống',
            'user_id.exists'         => 'Người dùng không hợp lệ',

            'barber_id.required'     => 'Barber không được để trống',
            'barber_id.exists'       => 'Barber không hợp lệ',

            'rating.required'        => 'Vui lòng chọn đánh giá sao',
            'rating.integer'         => 'Số sao phải là số nguyên',
            'rating.min'             => 'Số sao tối thiểu là 1',
            'rating.max'             => 'Số sao tối đa là 5',

            'comment.string'         => 'Bình luận phải là chuỗi ký tự',
            'comment.max'            => 'Bình luận không được quá 1000 ký tự',

            'appointment_id.exists'  => 'Lịch hẹn không hợp lệ',
        ];
    }
}
