<?php


namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Checkin extends Model
{
    protected $fillable = [
        'appointment_id',
        'qr_code_value',
        'checkin_time',
        'is_checked_in',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
