<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    /** @use HasFactory<\Database\Factories\RefundRequestFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'reason',
        'order_id',
        'refund_amount',
        'bank_account_name',
        'bank_account_number',
        'bank_name',
        'refund_status',
        'refunded_at',
    ];

    protected $casts = [
        'refund_status' => 'string',
        'refunded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
