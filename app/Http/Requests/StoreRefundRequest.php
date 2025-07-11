<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'refundable_type' => 'required|in:order,appointment',
            'refundable_id' => 'required|integer',
            'reason' => 'required|string|max:1000',
            'bank_account_name' => 'required|string|max:100',
            'bank_account_number' => [
                'required',
                'regex:/^[0-9]{6,20}$/'
            ],
            'bank_name' => [
                'required',
                'string',
                Rule::in(config('banks')),
            ],
        ];
    }

    public function messages()
    {
        return [
            'refundable_type.required' => 'Vui lòng chọn loại yêu cầu (đơn hàng hoặc đặt lịch).',
            'refundable_type.in' => 'Loại yêu cầu không hợp lệ.',
            'refundable_id.required' => 'Vui lòng chọn đơn hàng hoặc đặt lịch.',
            'refundable_id.integer' => 'ID không hợp lệ.',
            'reason.required' => 'Vui lòng nhập lý do hoàn tiền.',
            'reason.max' => 'Lý do hoàn tiền không được vượt quá 1000 ký tự.',
            'bank_account_name.required' => 'Vui lòng nhập tên chủ tài khoản.',
            'bank_account_name.max' => 'Tên chủ tài khoản không được vượt quá 100 ký tự.',
            'bank_account_number.required' => 'Vui lòng nhập số tài khoản.',
            'bank_account_number.regex' => 'Số tài khoản chỉ được chứa số và phải từ 6 đến 20 chữ số.',
            'bank_name.required' => 'Vui lòng chọn tên ngân hàng.',
            'bank_name.in' => 'Ngân hàng không hợp lệ.',
        ];
    }
}
