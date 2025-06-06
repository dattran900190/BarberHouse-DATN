<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $payments = Payment::with('appointment') // thêm dòng này
            ->when($search, function ($query, $search) {
                return $query->where('method', 'like', '%' . $search . '%'); // hoặc lọc theo method
            })
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        // Nạp quan hệ liên quan qua appointment
        $payment->load(['appointment.user', 'appointment.promotion', 'appointment.service', 'appointment.barber', 'appointment.branch']);

        return view('admin.payments.show', compact('payment'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $payments = Payment::all();
        $paymentLocked = in_array($payment->status, ['paid', 'refunded', 'failed']);
        return view('admin.payments.edit', compact('payment', 'paymentLocked'));

        // return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $dataToUpdate = [
            'status' => $request->status,
            'method' => $request->method,
        ];

        // Nếu trạng thái mới là 'paid' mà chưa có paid_at thì set thời gian hiện tại
        if ($request->status === 'paid' && !$payment->paid_at) {
            $dataToUpdate['paid_at'] = now();
        }

        // Nếu trạng thái không phải 'paid', bạn có thể muốn reset paid_at thành null
        if ($request->status !== 'paid') {
            $dataToUpdate['paid_at'] = null;
        }

        $payment->update($dataToUpdate);

        // Lấy số trang từ request
        $currentPage = $request->input('page', 1);

        return redirect()->route('payments.index', ['page' => $currentPage])
            ->with('success', 'Cập nhật thông tin thanh toán thành công.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        // Nếu đã bị đánh dấu là thất bại từ trước
        if ($payment->status === 'failed') {
            return redirect()->route('payments.index')->with('success', 'Thanh toán đã được huỷ trước đó.');
        }

        // Thực hiện huỷ thanh toán
        $payment->update([
            'status' => 'failed',
            'paid_at' => null, // Huỷ thì không còn thời điểm thanh toán
        ]);

        return redirect()->route('payments.index')->with('success', 'Thanh toán đã được huỷ.');
    }
}
