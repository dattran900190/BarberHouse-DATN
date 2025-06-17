<?php

namespace App\Http\Controllers;

use App\Models\UserRedeemedVoucher;
// use App\Models\User;
// use App\Models\Promotion;
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

}