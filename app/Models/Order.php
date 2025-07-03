<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'user_id',
        'name',
        'phone',
        'address',
        'total_money',
        'status',
        'payment_method',
        'note',
    ];

    public $timestamps = true;

    // CHỈ GIỮ LẠI HÀM NÀY
    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'order_id', 'id');
    }

    public function refundRequests()
    {
        return $this->hasMany(RefundRequest::class);
    }
}