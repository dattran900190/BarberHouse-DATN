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
        return [
            'refund_status' => [
                'required',
                Rule::in(['pending', 'processing', 'refunded', 'rejected']),
            ],
        ];
    }
}
