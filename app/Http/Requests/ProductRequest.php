<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
             'product_category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            // 'stock' => 'required|integer|min:0',
            // 'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'additional_images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'variants.*.volume_id' => 'required|exists:volumes,id',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
    public function messages()
{
    return [
        'product_category_id.required' => 'Vui lòng chọn danh mục.',
        'product_category_id.exists' => 'Danh mục sản phẩm không tồn tại.',

        'name.required' => 'Tên sản phẩm là bắt buộc.',
        'name.string' => 'Tên sản phẩm phải là chuỗi ký tự.',
        'name.max' => 'Tên sản phẩm không được vượt quá 100 ký tự.',

        'description.string' => 'Mô tả sản phẩm phải là chuỗi ký tự.',

        'price.required' => 'Giá sản phẩm là bắt buộc.',
        'price.numeric' => 'Giá sản phẩm phải là số.',
        'price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',

        // 'stock.required' => 'Số lượng tồn kho là bắt buộc.',
        // 'stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
        // 'stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.',

        'image.image' => 'Ảnh đại diện phải là file ảnh.',
        'image.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg.',
        'image.max' => 'Ảnh đại diện dung lượng tối đa 2MB.',

        'additional_images.array' => 'Ảnh bổ sung phải là một mảng.',
        'additional_images.*.image' => 'Mỗi ảnh bổ sung phải là file ảnh.',
        'additional_images.*.mimes' => 'Ảnh bổ sung phải có định dạng jpeg, png, jpg.',
        'additional_images.*.max' => 'Ảnh bổ sung dung lượng tối đa 2MB.',

        'variants.array' => 'Biến thể phải là một mảng.',
        'variants.*.volume_id.required' => 'Volume của biến thể là bắt buộc.',
        'variants.*.volume_id.exists' => 'Volume của biến thể không tồn tại.',

        'variants.*.price.required' => 'Giá biến thể là bắt buộc.',
        'variants.*.price.numeric' => 'Giá biến thể phải là số.',
        'variants.*.price.min' => 'Giá biến thể phải lớn hơn hoặc bằng 0.',

        'variants.*.stock.required' => 'Số lượng tồn kho biến thể là bắt buộc.',
        'variants.*.stock.integer' => 'Số lượng tồn kho biến thể phải là số nguyên.',
        'variants.*.stock.min' => 'Số lượng tồn kho biến thể phải lớn hơn hoặc bằng 0.',

        'variants.*.image.image' => 'Ảnh biến thể phải là file ảnh.',
        'variants.*.image.mimes' => 'Ảnh biến thể phải có định dạng jpeg, png, jpg.',
        'variants.*.image.max' => 'Ảnh biến thể dung lượng tối đa 2MB.',
    ];
}

}
