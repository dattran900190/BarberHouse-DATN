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

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
