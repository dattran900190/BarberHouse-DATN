<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        return view('admin.profile', compact('user'));
    }

    public function update(AccountUpdateRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Không cho sửa các thông tin hệ thống
        unset($validated['role'], $validated['status'], $validated['points_balance'], $validated['password']);

        // Cập nhật avatar nếu có
        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('admin.profile', [
            'tab' => $request->input('tab', 'account-info')
        ])->with('success-info', 'Cập nhật thông tin thành công.');
    }

    public function updatePassword(AccountUpdateRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
             return redirect()->route('admin.profile', [
            'tab' => $request->input('tab', 'account-change-password')
        ])->with('current_password', 'Mật khẩu cũ không chính xac.');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('admin.profile', [
            'tab' => $request->input('tab', 'account-change-password')
        ])->with('success-password', 'Đổi mật khẩu thành công.');
    }
    
}
