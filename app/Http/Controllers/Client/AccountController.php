<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{

    public function index()
    {
        return view('client.account.profile', [
            'pointHistories' => Auth::user()->pointHistories ?? collect([]), // Truyền dữ liệu cho tab Lịch sử điểm
        ]);
    }
    /**
     * Cập nhật thông tin người dùng
     */
    public function update(AccountUpdateRequest $request)
{
    $user = Auth::user();
    $validated = $request->validated();

    // Nếu có file avatar thì xử lý
    if ($request->hasFile('avatar')) {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        $path = $request->file('avatar')->store('avatars', 'public');
        $validated['avatar'] = $path;
    }

    $user->update($validated);

    return redirect()->route('client.account.profile', [
        'tab' => $request->input('tab', 'account-info')
    ])->with('success', 'Cập nhật thông tin thành công.');
}


    /**
     * Thay đổi mật khẩu người dùng
     */
    public function updatePassword(AccountUpdateRequest $request)
{
    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Mật khẩu cũ không đúng.']);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->route('client.account.profile')
        ->with('success', 'Đổi mật khẩu thành công.');
}

}
