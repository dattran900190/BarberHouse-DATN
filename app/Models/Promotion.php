<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';

    protected $fillable = [
        'code',
        'description',
        'required_points',
        'usage_limit',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_value',
        'quantity',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
