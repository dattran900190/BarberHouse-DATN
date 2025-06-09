<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'appointment_code',
        'user_id',
        'barber_id',
        'service_id',
        'branch_id',
        'appointment_time',
        'status',
        'payment_status',
        'note',
        'is_free',
        'promotion_id',
        'discount_amount',
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
    public function pointHistories()
    {
        return $this->hasMany(PointHistory::class);
    }
}
