<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Promotion extends Model
{    use HasFactory, Notifiable, SoftDeletes;
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

     protected $dates = ['deleted_at'];
    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
