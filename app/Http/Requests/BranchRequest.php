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
        // Kiểm tra xem có route parameter 'branch' không → nếu có là đang update
        $isUpdate = $this->route('branch') !== null;

        // Lấy ID chi nhánh nếu đang ở chế độ chỉnh sửa
        $branchId = $this->route('branch')?->id;

        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/'
            ],
            'google_map_url' => ['required', 'url', 'regex:/^https:\/\/www\.google\.com\/maps\/embed\?pb=.*/'],
            'image' => $isUpdate
                ? 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'  // Khi sửa → ảnh không bắt buộc
                : 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Khi thêm mới → ảnh bắt buộc
            'content' => 'required|string',
            'content' => 'required|nullable|string',
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
            'content.required' => 'Nội dung không được để trống',
            'phone.max' => 'Số điện thoại không được vượt quá 10 ký tự',
            'phone.regex' => 'Số điện thoại phải bắt đầu bằng số 0 và gồm 10 chữ số.',
            'image.required' => 'Ảnh đại diện không được để trống',
            'image.max' => 'Ảnh đại diện không được vượt quá 2MB',
            'image.image' => 'Ảnh đại diện phải là một tệp hình ảnh',
            'image.mimes' => 'Ảnh đại diện phải có định dạng: jpeg, png, jpg, gif, svg',
            'content.string' => 'Nội dung phải là kiểu chuỗi ký tự',
            'google_map_url.required' => 'URL Google Map không được để trống',
            'google_map_url.url' => 'URL Google Map không hợp lệ',
            'google_map_url.regex' => 'URL Google Map không hợp lệ. Vui lòng sử dụng định dạng https://www.google.com/maps/embed?pb=...',


        ];
    }
}
