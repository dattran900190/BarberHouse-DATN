<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Appointment;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $appointmentId = $this->route('appointment') ? $this->route('appointment')->id : null;
        
        return [
            'appointment_time' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($appointmentId) {
                    $barberId = $this->input('barber_id');
                    $branchId = $this->input('branch_id');
                    $appointmentTime = $value;
                    
                    if (!$barberId || !$branchId) {
                        return; // Bỏ qua validation nếu không có barber_id hoặc branch_id
                    }
                    
                    // Kiểm tra xem có lịch hẹn nào khác trùng thời gian với barber và branch không
                    $conflictingAppointment = Appointment::where('id', '!=', $appointmentId)
                        ->where('barber_id', $barberId)
                        ->where('branch_id', $branchId)
                        ->where('status', '!=', 'cancelled')
                        ->where(function ($query) use ($appointmentTime) {
                            // Kiểm tra trùng thời gian (cho phép overlap 30 phút)
                            $startTime = \Carbon\Carbon::parse($appointmentTime);
                            $endTime = $startTime->copy()->addMinutes(30); // Giả sử mỗi lịch hẹn kéo dài 30 phút
                            
                            $query->where(function ($q) use ($startTime, $endTime) {
                                $q->where('appointment_time', '>=', $startTime)
                                  ->where('appointment_time', '<', $endTime);
                            })->orWhere(function ($q) use ($startTime, $endTime) {
                                $q->where('appointment_time', '<=', $startTime)
                                  ->where(DB::raw('DATE_ADD(appointment_time, INTERVAL 30 MINUTE)'), '>', $startTime);
                            });
                        })
                        ->first();
                    
                    if ($conflictingAppointment) {
                        $fail('Thời gian này đã có lịch hẹn khác với barber và chi nhánh này. Vui lòng chọn thời gian khác.');
                    }
                }
            ],
            'barber_id' => 'exists:barbers,id',
            'branch_id' => 'exists:branches,id',
            'service_id' => 'exists:services,id',
            // 'status' => 'required|in:pending,confirmed,completed,cancelled',
            'status' => 'in:pending,confirmed,checked-in,progress,completed,cancelled',
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
            'barber_id.exists' => 'Barber không tồn tại.',
            'branch_id.exists' => 'Chi nhánh không tồn tại.',
            'service_id.exists' => 'Dịch vụ không tồn tại.',
            // 'status.required' => 'Vui lòng chọn trạng thái lịch hẹn.',
            'status.in' => 'Trạng thái lịch hẹn không hợp lệ.',

            'payment_status.required' => 'Vui lòng chọn trạng thái thanh toán.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',

            'note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
        ];
    }
}
