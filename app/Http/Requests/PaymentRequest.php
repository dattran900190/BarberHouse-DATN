<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class paymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   public function rules(): array
    {
        return [
            'status' => 'required|in:unpaid,paid,refunded,failed',
            'method' => 'required|in:momo,cash',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Trạng thái thanh toán không được để trống.',
            'status.in' => 'Trạng thái thanh toán không hợp lệ.',
            'method.required' => 'Phương thức thanh toán không được để trống.',
            'method.in' => 'Phương thức thanh toán phải là momo hoặc cash.',
        ];
    }
}
