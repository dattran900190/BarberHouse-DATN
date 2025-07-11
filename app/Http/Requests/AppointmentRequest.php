<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_time' => 'required|date',
            // 'status' => 'required|in:pending,confirmed,completed,cancelled',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid,refunded,failed',
            'note' => 'nullable|string|max:500',
            'voucher_id' => 'nullable|exists:user_redeemed_vouchers,id,user_id,' . Auth::id(),
            'additional_services' => 'nullable|json',
            'additional_services.*' => 'exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_time.required' => 'Vui lòng chọn thời gian hẹn.',
            'appointment_time.date' => 'Thời gian hẹn không hợp lệ.',

            // 'status.required' => 'Vui lòng chọn trạng thái lịch hẹn.',
            'status.in' => 'Trạng thái lịch hẹn không hợp lệ.',

            'payment_status.required' => 'Vui lòng chọn trạng thái thanh toán.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',

            'note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
        ];
    }
}
