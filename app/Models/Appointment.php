<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
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
        'branch_id',
        'service_id',
        'additional_services',
        'appointment_time',
        'status',
        'payment_method',
        'payment_status',
        'note',
        'cancellation_reason',
        'status_before_cancellation',
        'promotion_id',
        'discount_amount',
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
        return $this->belongsTo(Service::class);
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
    public function review()
    {
        return $this->hasOne(Review::class, 'appointment_id');
    }
    public function refundRequests()
    {
        return $this->hasMany(RefundRequest::class, 'appointment_id');
    }
}
