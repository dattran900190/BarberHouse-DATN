<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Kiểm tra giá trị "tab" để xác định người dùng đang cập nhật gì
        if ($this->input('tab') === 'account-change-password') {
            return [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ];
        }

        // Mặc định là cập nhật thông tin cá nhân
        return [
            'name'     => 'required|string|max:100',
            'phone' => [
                'required',
                'regex:/^(\+84|0)(\d{9})$/',
                'max:15',
                Rule::unique('users', 'phone')->ignore($this->user()->id),
            ],
            'address'  => 'required|string',
            'gender'   => 'required|in:male,female,other',
            'avatar'   => 'nullable|image|mimes:jpg,gif,png|max:2048',
            'tab'      => 'nullable|string',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'Tên không được để trống.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'phone.unique' => 'Số điện thoại đã được sử dụng bởi người dùng khác.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 ký tự.',
            'address.required' => 'Địa chỉ không được để trống.',
            'gender.required' => 'Giới tính không được để trống.',
            'avatar.image' => 'Ảnh đại diện phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Ảnh đại diện phải có định dạng jpg, gif hoặc png.',
            'avatar.max' => 'Ảnh đại diện không được vượt quá 2MB.',
            'current_password.required' => 'Mật khẩu hiện tại không được để trống.',
            'new_password.required' => 'Mật khẩu mới không được để trống.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Mật khẩu mới và xác nhận mật khẩu không khớp.',
            'tab.string' => 'Tab không hợp lệ.',
        ];
    }
}
