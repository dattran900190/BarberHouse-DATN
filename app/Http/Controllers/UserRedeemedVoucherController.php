<?php

namespace App\Http\Controllers;

use App\Models\UserRedeemedVoucher;
use App\Models\User;
// use App\Models\User;
// use App\Models\Promotion;
use Illuminate\Http\Request;

class UserRedeemedVoucherController extends Controller
{
  public function index(Request $request)
{
    // Lấy danh sách người dùng đã đổi voucher (nhóm theo user_id)
    $query = User::whereHas('redeemedVouchers');

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('name', 'like', '%' . $search . '%');
    }

    $users = $query->withCount('redeemedVouchers')
                   ->withCount(['redeemedVouchers as used_vouchers_count' => function($query) {
                       $query->where('is_used', true);
                   }])
                   ->withCount(['redeemedVouchers as unused_vouchers_count' => function($query) {
                       $query->where('is_used', false);
                   }])
                   ->paginate(10);

    return view('admin.user_redeemed_vouchers.index', compact('users'));
}

public function show($userId)
{
    $user = User::with(['redeemedVouchers.promotion'])->findOrFail($userId);
    return view('admin.user_redeemed_vouchers.show', compact('user'));
}
}