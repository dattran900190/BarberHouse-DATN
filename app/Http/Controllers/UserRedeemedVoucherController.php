<?php

namespace App\Http\Controllers;

use App\Models\UserRedeemedVoucher;
use App\Models\User;
use App\Models\Promotion;
use Illuminate\Http\Request;

class UserRedeemedVoucherController extends Controller
{
  public function index(Request $request)
{
    $query = UserRedeemedVoucher::with(['user', 'promotion']);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%');
        });
    }

    $items = $query->paginate(10);
    return view('admin.user_redeemed_vouchers.index', compact('items'));
}

    public function create()
    {
        $users = User::all();
        $promotions = Promotion::all();
        return view('admin.user_redeemed_vouchers.create', compact('users', 'promotions'));
    }
public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'promotion_id' => 'required|exists:promotions,id',
        'is_used' => 'nullable|boolean',
    ]);

    // Kiểm tra xem người dùng đã được gán voucher này chưa
    $exists = UserRedeemedVoucher::where('user_id', $request->user_id)
                ->where('promotion_id', $request->promotion_id)
                ->exists();

    if ($exists) {
        return redirect()->back()
            ->withInput()
            ->withErrors(['user_id' => 'Người dùng này đã được gán voucher này rồi.']);
    }

    // Nếu chưa có thì lưu
    UserRedeemedVoucher::create([
        'user_id' => $request->user_id,
        'promotion_id' => $request->promotion_id,
        'is_used' => $request->has('is_used'),
        'used_at' => $request->has('is_used') ? now() : null,
    ]);

    return redirect()->route('user_redeemed_vouchers.index')->with('success', 'Đã gán voucher thành công!');
}


    public function edit(UserRedeemedVoucher $userRedeemedVoucher)
    {
        $users = User::all();
        $promotions = Promotion::all();
        return view('admin.user_redeemed_vouchers.edit', compact('userRedeemedVoucher', 'users', 'promotions'));
    }

    public function update(Request $request, UserRedeemedVoucher $userRedeemedVoucher)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'promotion_id' => 'required|exists:promotions,id',
        'is_used' => 'nullable|boolean',
        'used_at' => 'nullable|date',
    ]);

    $isUsed = $request->has('is_used') && $request->input('is_used');

    $userRedeemedVoucher->update([
        'user_id' => $validated['user_id'],
        'promotion_id' => $validated['promotion_id'],
        'is_used' => $isUsed ? 1 : 0,
        'used_at' => $isUsed ? ($request->input('used_at') ?? now()) : null,
    ]);

    return redirect()->route('user_redeemed_vouchers.index')->with('success', 'Cập nhật thành công!');
}


    public function destroy(UserRedeemedVoucher $userRedeemedVoucher)
    {
        $userRedeemedVoucher->delete();
        return redirect()->route('user_redeemed_vouchers.index')->with('success', 'Xóa thành công!');
    }
}