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
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $currentStatus       = $payment->status;
        $newStatus           = $request->status;
        $currentMethod       = $payment->method;
        $newMethod           = $request->method;

        // 1. Nếu đã 'refunded', không được phép chuyển status hay method
        if ($currentStatus === 'refunded') {
            if ($newStatus !== 'refunded') {
                return back()->withErrors([
                    'status' => 'Thanh toán đã được hoàn trả, không thể thay đổi trạng thái.'
                ]);
            }
            if ($newMethod !== $currentMethod) {
                return back()->withErrors([
                    'method' => 'Thanh toán đã được hoàn trả, không thể thay đổi phương thức.'
                ]);
            }
        }

        // 2. Nếu đã 'paid', không cho quay về 'pending'
        if ($currentStatus === 'paid' && $newStatus === 'pending') {
            return back()->withErrors([
                'status' => 'Không thể chuyển từ "Thanh toán thành công" về "Chờ xử lý".'
            ]);
        }

        // 3. Nếu đã 'paid', vẫn cho phép 'paid' → 'refunded' hoặc 'paid' → 'failed'
        //    => không cần kiểm vì mặc định $newStatus có thể là 'refunded' hoặc 'failed' (hợp lệ)

        // 4. Nếu đang 'failed', không cho quay về 'pending'
        if ($currentStatus === 'failed' && $newStatus === 'pending') {
            return back()->withErrors([
                'status' => 'Không thể chuyển từ "Thanh toán thất bại" về "Chờ xử lý".'
            ]);
        }

        // 5. Nếu đang 'pending', không cho 'pending' → 'refunded'
        if ($currentStatus === 'pending' && $newStatus === 'refunded') {
            return back()->withErrors([
                'status' => 'Không thể chuyển từ "Chờ xử lý" trực tiếp sang "Hoàn trả".'
            ]);
        }

        // 6. Nếu đang 'paid' hoặc 'failed', không cho đổi method
        if (in_array($currentStatus, ['paid', 'refunded']) && $newMethod !== $currentMethod) {
            return back()->withErrors([
                'method' => 'Không thể thay đổi phương thức sau khi thanh toán đã hoàn thành/hoàn trả.'
            ]);
        }
        //    Lưu ý: nếu bạn muốn cho 'failed' → 'paid' nhưng vẫn đổi method, 
        //    thì bỏ in_array(..., ['failed']) trong điều kiện trên.

        // 7. Cuối cùng: tất cả hợp lệ, cập nhật
        $payment->update($request->only(['status', 'method']));

        // Nếu status mới = 'paid' mà chưa có paid_at, set paid_at = now()
        if ($newStatus === 'paid' && !$payment->paid_at) {
            $payment->paid_at = now();
            $payment->save();
        }

        // Nếu status không phải 'paid', có thể reset paid_at về null (tuỳ business)
        if ($newStatus !== 'paid') {
            $payment->paid_at = null;
            $payment->save();
        }

        // Quay về index (hoặc bất cứ đâu) kèm flash
        return redirect()->route('payments.index')->with('success', 'Cập nhật thanh toán thành công.');
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
