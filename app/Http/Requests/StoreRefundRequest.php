<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRefundRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string|max:1000',
            'bank_account_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
        ];
    }
}