<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   public function rules(): array
    {
         // Lấy ID chi nhánh nếu đang ở chế độ chỉnh sửa
        $serviceId = $this->route('service')?->id;

        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'duration'    => 'required|integer|min:1',
            'is_combo'    => 'required|boolean',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // 2MB
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Tên dịch vụ không được để trống',
            'name.string'          => 'Tên dịch vụ phải là chuỗi ký tự',
            'name.max'             => 'Tên dịch vụ không được vượt quá 255 ký tự',

            'description.string'   => 'Mô tả phải là chuỗi ký tự',

            'price.required'       => 'Giá dịch vụ không được để trống',
            'price.numeric'        => 'Giá dịch vụ phải là số',
            'price.min'            => 'Giá dịch vụ phải lớn hơn hoặc bằng 0',

            'duration.required'    => 'Thời lượng không được để trống',
            'duration.integer'     => 'Thời lượng phải là số nguyên',
            'duration.min'         => 'Thời lượng phải ít nhất 1 phút',

            'is_combo.required'    => 'Vui lòng chọn loại dịch vụ',
            'is_combo.boolean'     => 'Giá trị không hợp lệ cho combo',

            'image.image'          => 'Tệp tải lên phải là hình ảnh',
            'image.mimes'          => 'Ảnh chỉ chấp nhận định dạng: jpg, jpeg, png, webp',
            'image.max'            => 'Ảnh không được vượt quá 2MB',
        ];
    }
}
