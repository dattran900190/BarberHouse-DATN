<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefundRequest extends Model
{
    /** @use HasFactory<\Database\Factories\RefundRequestFactory> */
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'reason',
        'order_id',
        'appointment_id',
        'refund_amount',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'refund_status',
        'reject_reason',
        'refunded_at',
        'proof_image',
    ];

    protected $casts = [
        'refund_status' => 'string',
        'refunded_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
