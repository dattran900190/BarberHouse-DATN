<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'method',
        'amount',
        'status',
        'transaction_code',
        'paid_at'
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
