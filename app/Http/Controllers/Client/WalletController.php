<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRefundRequest;
use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $refunds = RefundRequest::where('user_id', Auth::id())
            ->with('order')
            ->paginate(10);

        return view('client.refunds.index', compact('refunds'));
    }

    public function create()
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->whereHas('payment', fn($query) => $query->where('status', 'paid'))
            ->get();

        return view('client.refunds.create', compact('orders'));
    }

    public function store(StoreRefundRequest $request)
    {
        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== Auth::id()) {
            return back()->withErrors(['error' => 'Unauthorized action.']);
        }

        RefundRequest::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'refund_amount' => $order->total_money,
            'reason' => $request->reason,
            'bank_account_name' => $request->bank_account_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'refund_status' => 'pending',
        ]);

        return redirect()->route('client.refunds.index')->with('success', 'Refund request submitted.');
    }
}