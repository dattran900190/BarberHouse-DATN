<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRefundRequest;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\RefundRequest;
use App\Events\RefundRequestCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    // Hiển thị danh sách yêu cầu hoàn tiền
    public function index(Request $request)
    {
        $query = RefundRequest::where('user_id', Auth::id())
            ->with(['order', 'appointment']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('order', function ($q2) use ($request) {
                    $q2->where('order_code', 'like', '%' . $request->search . '%');
                })->orWhereHas('appointment', function ($q3) use ($request) {
                    $q3->where('appointment_code', 'like', '%' . $request->search . '%');
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('refund_status', $request->status);
        }

        $refunds = $query->latest()->paginate(5)->withQueryString();

        return view('client.detailWallet', compact('refunds'));
    }

    // Hiển thị form tạo yêu cầu hoàn tiền
    public function create(Request $request)
    {
        $orders = Order::where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('payment_status', 'paid')
            ->where(function ($query) {
                $query->whereDoesntHave('refundRequests', function ($q) {
                    $q->whereIn('refund_status', ['pending', 'processing']);
                });
            })
            ->get();

        $appointments = Appointment::where('user_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->where('payment_status', 'paid')
            ->where(function ($query) {
                $query->whereDoesntHave('refundRequests', function ($q) {
                    $q->whereIn('refund_status', ['pending', 'processing']);
                });
            })
            ->get();

        if ($request->has('refundable_type') && $request->has('refundable_id')) {
            $refundableType = $request->input('refundable_type');
            $refundableId = $request->input('refundable_id');

            if ($refundableType === 'order') {
                $orders = $orders->where('id', $refundableId);
                if ($orders->isEmpty()) {
                    return redirect()->route('client.wallet')->withErrors(['error' => 'Đơn hàng không hợp lệ hoặc không đủ điều kiện hoàn tiền.']);
                }
            } elseif ($refundableType === 'appointment') {
                $appointments = $appointments->where('id', $refundableId);
                if ($appointments->isEmpty()) {
                    return redirect()->route('client.wallet')->withErrors(['error' => 'Đặt lịch không hợp lệ hoặc không đủ điều kiện hoàn tiền.']);
                }
            }
        }

        return view('client.wallet', compact('orders', 'appointments'));
    }

    // Lưu yêu cầu hoàn tiền
    public function store(StoreRefundRequest $request)
    {
        DB::beginTransaction();
        try {
            $refundableType = $request->input('refundable_type');
            $refundableId = $request->input('refundable_id');
            $userId = Auth::id();

            $existingRequest = RefundRequest::where($refundableType . '_id', $refundableId)
                ->where('user_id', $userId)
                ->whereIn('refund_status', ['pending', 'processing'])
                ->exists();

            if ($existingRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đã có một yêu cầu hoàn tiền đang chờ xử lý cho mục này.'
                ], 422);
            }

            if ($refundableType === 'order') {
                $refundable = Order::where('id', $refundableId)
                    ->where('user_id', $userId)
                    ->where('status', '!=', 'cancelled')
                    ->where('payment_status', 'paid')
                    ->where(function ($query) {
                        $query->whereDoesntHave('refundRequests', function ($q) {
                            $q->whereIn('refund_status', ['pending', 'processing']);
                        });
                    })
                    ->firstOrFail();
                $refundAmount = $refundable->total_money;
            } elseif ($refundableType === 'appointment') {
                $refundable = Appointment::where('id', $refundableId)
                    ->where('user_id', $userId)
                    ->where('status', '!=', 'cancelled')
                    ->where('status', '!=', 'completed')
                    ->where('payment_status', 'paid')
                    ->where(function ($query) {
                        $query->whereDoesntHave('refundRequests', function ($q) {
                            $q->whereIn('refund_status', ['pending', 'processing']);
                        });
                    })
                    ->firstOrFail();
                $refundAmount = $refundable->total_amount;
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Loại yêu cầu không hợp lệ.'
                ], 422);
            }

            $refundRequest = RefundRequest::create([
                'user_id' => $userId,
                'order_id' => $refundableType === 'order' ? $refundableId : null,
                'appointment_id' => $refundableType === 'appointment' ? $refundableId : null,
                'refund_amount' => $refundAmount,
                'reason' => $request->reason,
                'bank_account_name' => $request->bank_account_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_name' => $request->bank_name,
                'refund_status' => 'pending',
            ]);

            // Load relationships để có đủ data cho event
            $refundRequest->load(['user', 'order', 'appointment']);

            // Trigger event để broadcast đến admin
            event(new RefundRequestCreated($refundRequest));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Yêu cầu hoàn tiền đã được gửi.'
            ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi gửi yêu cầu hoàn tiền', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xảy ra: ' . $e->getMessage()
            ], 500, ['Content-Type' => 'application/json; charset=utf-8']);
        }
    }
}