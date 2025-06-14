<?php

namespace App\Http\Controllers\Client;

use App\Http\Requests\AccountUpdateRequest;
use App\Models\PointHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        // Lấy user đang đăng nhập
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        // Lấy lịch sử điểm của user đó
        $pointHistories = PointHistory::with('promotion') // nếu có liên kết với promotion
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('client.profile', compact('user', 'pointHistories'));
    }
    public function update(AccountUpdateRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('client.profile', [
            'tab' => $request->input('tab', 'account-info')
        ])->with('success-info', 'Cập nhật thông tin thành công.');
    }

    public function updatePassword(AccountUpdateRequest $request)
    {
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu cũ không đúng.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('client.profile', [
            'tab' => $request->input('tab', 'account-change-password')
        ])->with('success-password', 'Đổi mật khẩu thành công.');
    }
}
