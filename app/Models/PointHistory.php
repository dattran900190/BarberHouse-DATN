<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory;

    protected $table = 'point_histories';
    protected $fillable = [
        'user_id',
        'points',
        'type',
        'promotion_id',
        'appointment_id'
    ];
    protected $casts = [
        'type' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
