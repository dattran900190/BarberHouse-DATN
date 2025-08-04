<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'refund_status' => 'required|in:pending,processing,refunded,rejected',
        ];

        if ($this->input('refund_status') === 'rejected') {
            $rules['reject_reason'] = 'required|string|min:10|max:500';
        }

        if ($this->input('refund_status') === 'refunded') {
            $rules['proof_image'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048'; // Giới hạn file ảnh
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'refund_status.required' => 'Vui lòng chọn trạng thái hoàn tiền.',
            'refund_status.in' => 'Trạng thái hoàn tiền không hợp lệ.',
            'reject_reason.required' => 'Vui lòng nhập lý do từ chối.',
            'reject_reason.string' => 'Lý do từ chối phải là chuỗi ký tự.',
            'reject_reason.min' => 'Lý do từ chối phải có ít nhất 10 ký tự.',
            'reject_reason.max' => 'Lý do từ chối không được vượt quá 500 ký tự.',
            'proof_image.image' => 'Minh chứng phải là file ảnh.',
            'proof_image.mimes' => 'Minh chứng phải có định dạng jpeg, png hoặc jpg.',
            'proof_image.max' => 'Minh chứng không được vượt quá 2MB.',
        ];
    }
}