<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'link_url'   => 'nullable|url',
            'is_active'  => 'boolean',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Tiêu đề không được để trống',
            'link_url.url'     => 'Link không hợp lệ',
            'image.image'      => 'Tệp phải là hình ảnh',
            'image.max'        => 'Kích thước ảnh không vượt quá 2MB',
        ];
    }
}


