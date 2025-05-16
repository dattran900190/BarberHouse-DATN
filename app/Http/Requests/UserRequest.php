<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Xác định xem người dùng có quyền thực hiện yêu cầu này không.
     */
    public function authorize()
    {
        return true; // Cho phép tất cả người dùng, bạn có thể điều chỉnh quyền tại đây
    }

    /**
     * Quy tắc validation cho các trường trong form.
     */
    public function rules()
    {
        $userId = $this->user ? $this->user->id : null;

        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:users,email,' . $userId,
            'password' => $this->isMethod('post') ? 'required|string|min:8|max:255' : 'nullable|string|min:8|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $userId,
            'gender' => 'nullable|in:male,female,other',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Ảnh tối đa 2MB
            'address' => 'nullable|string',
            'role' => 'required|in:user,admin,staff,editor',
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
            'gender.in' => 'Giới tính phải là male, female hoặc other.',
            'avatar.image' => 'Avatar phải là một tệp hình ảnh.',
            'avatar.mimes' => 'Avatar chỉ hỗ trợ định dạng jpeg, png, jpg, gif.',
            'avatar.max' => 'Avatar không được vượt quá 2MB.',
            'role.required' => 'Vai trò là bắt buộc.',
            'role.in' => 'Vai trò phải là user, admin, staff hoặc editor.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là active, inactive hoặc banned.',
            'points_balance.integer' => 'Số điểm phải là số nguyên.',
            'points_balance.min' => 'Số điểm không được nhỏ hơn 0.',
        ];
    }
}