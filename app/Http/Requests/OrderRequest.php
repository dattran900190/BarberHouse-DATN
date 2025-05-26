<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $orderId = $this->route('order')?->id;

        return [
            'order_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('orders')->ignore($orderId),
            ],
            'user_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'total_money' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,processing,shipping,completed,cancelled',
            'payment_method' => 'nullable|in:cash,momo,vnpay,card',
            'note' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'order_code.required' => 'Mã đơn hàng không được để trống',
            'order_code.unique' => 'Mã đơn hàng đã tồn tại',
            'order_code.string' => 'Mã đơn hàng phải là chuỗi ký tự',
            'order_code.max' => 'Mã đơn hàng không được vượt quá 255 ký tự',

            'name.required' => 'Tên người nhận không được để trống',
            'name.string' => 'Tên người nhận phải là chuỗi ký tự',
            'name.max' => 'Tên người nhận không được vượt quá 255 ký tự',

            'phone.required' => 'Số điện thoại không được để trống',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự',

            'address.required' => 'Địa chỉ không được để trống',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự',

            'total_money.required' => 'Tổng tiền không được để trống',
            'total_money.numeric' => 'Tổng tiền phải là số',
            'total_money.min' => 'Tổng tiền phải lớn hơn hoặc bằng 0',

            'status.in' => 'Trạng thái không hợp lệ',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',

            'note.string' => 'Ghi chú phải là chuỗi ký tự',
        ];
    }
}
