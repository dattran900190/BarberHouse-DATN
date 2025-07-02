<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Lấy ID thợ cắt tóc nếu đang chỉnh sửa
        $barberId = $this->route('barber')?->id;

        return [
            'name' => 'required|string|max:100',
            'skill_level' => 'required|string|max:50',
            'rating_avg' => 'nullable|numeric|min:0|max:5',
            'profile' => 'nullable|string',
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
            ],
            'branch_id' => [
                'required',
                'integer',
                'exists:branches,id',
            ],
            'status' => 'required|in:idle,busy,retired',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Họ tên không được để trống',
            'name.string' => 'Họ tên phải là kiểu chuỗi',
            'name.max' => 'Họ tên không được vượt quá 100 ký tự',

            'skill_level.required' => 'Trình độ không được để trống',
            'skill_level.string' => 'Trình độ phải là kiểu chuỗi',
            'skill_level.max' => 'Trình độ không được vượt quá 50 ký tự',

            'rating_avg.numeric' => 'Đánh giá trung bình phải là số',
            'rating_avg.min' => 'Đánh giá trung bình không được nhỏ hơn 0',
            'rating_avg.max' => 'Đánh giá trung bình không được lớn hơn 5',

            'profile.string' => 'Hồ sơ phải là kiểu chuỗi',

            'avatar.image' => 'Ảnh đại diện phải là một file ảnh',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpeg, png, jpg, gif hoặc svg',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB',

            'branch_id.required' => 'Chi nhánh không được để trống',
            'branch_id.integer' => 'Chi nhánh phải là số nguyên',
            'branch_id.exists' => 'Chi nhánh không tồn tại',
        ];
    }
}
