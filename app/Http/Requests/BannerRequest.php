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
        'link_url'   => ['nullable', 'url', 'max:500', 'regex:/^https?:\/\/.*/i'],
        'status'     => 'required|boolean',
        'image'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ];
}

   public function messages(): array
{
    return [
        'title.required'   => 'Tiêu đề không được để trống',
        'link_url.url'     => 'Link không hợp lệ',
        'link_url.regex'   => 'Link phải bắt đầu bằng http:// hoặc https://',
        'link_url.max'     => 'Link không được dài quá 500 ký tự',
        'image.image'      => 'Tệp phải là hình ảnh',
        'image.mimes'      => 'Ảnh phải có định dạng jpg, jpeg, png',
        'image.max'        => 'Kích thước ảnh không vượt quá 2MB',
    ];
}

}


