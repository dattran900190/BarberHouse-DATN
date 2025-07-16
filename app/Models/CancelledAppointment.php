<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledAppointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'appointment_code',
        'user_id',
        'barber_id',
        'service_id',
        'additional_services',
        'branch_id',
        'appointment_time',
        'status',
        'payment_status',
        'note',
        'cancellation_reason',
        'cancellation_type',
        'status_before_cancellation',
        'total_amount'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withTrashed();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
    
    public function checkin()
    {
        return $this->hasOne(Checkin::class);
    }
}
