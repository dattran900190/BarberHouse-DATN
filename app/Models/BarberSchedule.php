<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id',
        'schedule_date',
        'start_time',
        'end_time',
        'is_available'
    ];

    // Quan hệ với barber
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }
}
