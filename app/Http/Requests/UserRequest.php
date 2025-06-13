<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    
    public function authorize()
    {
        return true; 
    }

    
    public function rules()
    {
        $userId = $this->user ? $this->user->id : null;

        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'password' => $this->isMethod('post') ? 'required|string|min:8|max:255' : 'nullable|string|min:8|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $userId,
            'gender' => 'required|in:male,female,other',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ảnh tối đa 2MB
            'address' => 'nullable|string',
            'role' => 'required|in:user,admin,super_admin,admin_branch',
            'status' => 'required|in:active,inactive,banned',
            'points_balance' => 'nullable|integer|min:0',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'Tên là bắt buộc.',
            'name.max' => 'Tên không được vượt quá 100 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Mật khẩu là bắt buộc khi tạo mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.max' => 'Mật khẩu không được vượt quá 255 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'gender.required' => 'Bắt buộc phải chọn giới tính.',
            'gender.in' => 'Giới tính phải là male, female hoặc other.',
            'avatar.image' => 'Avatar phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Avatar chỉ hỗ trợ định dạng jpeg, png, jpg, gif.',
            'avatar.max' => 'Avatar không được vượt quá 2MB.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.string' => 'Địa chỉ phải là một chuỗi.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'role.required' => 'Vai trò là bắt buộc.',
            'role.in' => 'Vai trò phải là user, admin, super_admin hoặc admin_branch.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là active, inactive hoặc banned.',
            'points_balance.integer' => 'Số điểm phải là số nguyên.',
            'points_balance.min' => 'Số điểm không được nhỏ hơn 0.',
        ];
    }
}