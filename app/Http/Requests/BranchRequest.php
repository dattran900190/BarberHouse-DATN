<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID chi nhánh nếu đang ở chế độ chỉnh sửa
        $branchId = $this->route('branch')?->id;

        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'google_map_url' => 'nullable|url',
            'image' => 'nullable|max:2048',
            'content' => 'nullable|string', // 
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' => 'Tên chi nhánh không được để trống',
            'name.string' => 'Tên chi nhánh phải là kiểu chuỗi ký tự',
            'address.required' => 'Địa chỉ không được để trống',
            'address.string' => 'Địa chỉ phải là kiểu chuỗi ký tự',
            'phone.required' => 'Số điện thoại không được để trống',
            'phone.string' => 'Số điện thoại phải là kiểu chuỗi ký tự',
            'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự',
            'image.max' => 'Ảnh đại diện không được vượt quá 2MB',
            'content.string' => 'Nội dung phải là kiểu chuỗi ký tự',
            'google_map_url' => ['nullable', 'url', 'regex:/^https:\/\/www\.google\.com\/maps\/embed\?pb=.*/'],

        ];
    }
}
