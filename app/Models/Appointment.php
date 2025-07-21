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
        'service_id',
        'additional_services',
        'branch_id',
        'appointment_time',
        'duration',
        'status',
        'payment_method',
        'payment_status',
        'note',
        'cancellation_reason',
        'status_before_cancellation',
        'promotion_id',
        'discount_amount',
        'total_amount',
        'confirmation_token',
        'confirmation_token_expires_at',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
        'confirmation_token_expires_at' => 'datetime',
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
    public function review()
    {
        return $this->hasOne(Review::class, 'appointment_id');
    }
    public function refundRequests()
    {
        return $this->hasMany(RefundRequest::class, 'appointment_id');
    }

    public function getAdditionalServiceObjectsAttribute()
    {
        $ids = json_decode($this->additional_services, true) ?? [];
        return Service::withTrashed()->whereIn('id', $ids)->get();
    }
}
