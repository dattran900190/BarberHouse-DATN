<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Dựa vào route name hoặc URL để xác định đang gọi login hay register
        if ($this->is('login') || $this->routeIs('postLogin')) {
            return [
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:8',
            ];
        }

        // Mặc định là đăng ký
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => [
                'required',
                'string',
                'regex:/^(0|\+84)[0-9]{9}$/',
                'max:15',
                'unique:users,phone'
            ],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            // Login messages
            'email.required' => 'Không được để trống email',
            'email.email' => 'Email không đúng định dạng',
            'email.exists' => 'Email chưa được đăng ký',
            'email.unique' => 'Email đã được đăng ký',
            'password.required' => 'Mật khẩu không được để trống',
            'password.string' => 'Mật khẩu không hợp lệ',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp',

            // Register messages
            'name.required' => 'Tên không được để trống',
            'phone.required' => 'Số điện thoại không được để trống',
            'phone.unique' => 'Số điện thoại đã được đăng ký',
            'phone.regex' => 'Số điện thoại không hợp lệ',
            'phone.max' => 'Số điện thoại không được quá 15 ký tự',

            // 'gender.required' => 'Vui lòng chọn giới tính',
            // 'gender.in' => 'Giới tính không hợp lệ',
            // 'address.required' => 'Địa chỉ không được để trống',
            // 'address.max' => 'Địa chỉ quá dài',
        ];
    }
}
