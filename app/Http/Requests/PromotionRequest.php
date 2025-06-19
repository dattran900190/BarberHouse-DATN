<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromotionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Thay bằng logic xác thực nếu cần (ví dụ: Auth::user()->hasRole('admin'))
    }

    public function rules(): array
    {
        $promotionId = $this->route('promotion')?->id;


        return [
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('promotions')->ignore($promotionId),
            ],
            'required_points' => 'nullable|integer|min:0',
            'usage_limit' => 'required|integer|min:1',
            'discount_type' => 'required|in:fixed,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string|max:300',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã giảm giá là bắt buộc.',
            'code.unique' => 'Mã giảm giá đã tồn tại.',
            'code.max' => 'Mã giảm giá không được vượt quá 10 ký tự.',
            'usage_limit.required' => 'Giới hạn sử dụng là bắt buộc.',
            'discount_type.required' => 'Loại giảm giá là bắt buộc.',
            'discount_value.required' => 'Giá trị giảm giá là bắt buộc.',
            'discount_type.in' => 'Loại giảm giá không hợp lệ.',
            'start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'start_date.after_or_equal' => 'Ngày bắt đầu phải là ngày hôm nay hoặc sau đó.',
            'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
            'is_active.boolean' => 'Trạng thái hoạt động phải là có hoặc không.',
            'required_points.integer' => 'Điểm yêu cầu phải là số nguyên.',
            'required_points.min' => 'Điểm yêu cầu phải lớn hơn hoặc bằng 0.',
            'usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên.',
            'usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn hoặc bằng 1.',
            'discount_value.numeric' => 'Giá trị giảm giá phải là số.',
            'discount_value.min' => 'Giá trị giảm giá phải lớn hơn 0.',
            'max_discount_amount.numeric' => 'Số tiền giảm giá tối đa phải là số.',
            'max_discount_amount.min' => 'Số tiền giảm giá tối đa phải lớn hơn hoặc bằng 0.',
            'min_order_value.numeric' => 'Giá trị đơn hàng tối thiểu phải là số.',
            'min_order_value.min' => 'Giá trị đơn hàng tối thiểu phải lớn hơn hoặc bằng 0.',
            'quantity.required' => 'Số lượng mã giảm giá là bắt buộc.',
            'quantity.integer' => 'Số lượng mã giảm giá phải là số nguyên.',
            'quantity.min' => 'Số lượng mã giảm giá phải lớn hơn hoặc bằng 1.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'description.max' => 'Mô tả không được vượt quá 300 ký tự.',
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                $this->discount_type === 'percent' &&
                $this->discount_value > 100
            ) {
                $validator->errors()->add('discount_value', 'Giá trị giảm phần trăm không được vượt quá 100%.');
            }
        });
    }
}
