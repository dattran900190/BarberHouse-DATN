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
        'holiday_start_date',
        'holiday_end_date', // Thêm trường này nếu bạn cần lưu ngày nghỉ
        'status',         // Thêm để lưu loại lịch
        'is_available',   // Nếu bạn dùng field này
        'note',          // Ghi chú cho lịch
    ];

    /**
     * Quan hệ: Lịch thuộc về một thợ cắt tóc
     */
    public function barber()
    {
        return $this->belongsTo(Barber::class);
    }

    /**
     * Scope: Lọc theo ngày
     */
    public function scopeOnDate($query, $date)
    {
        return $query->where('schedule_date', $date);
    }

    /**
     * Kiểm tra xem lịch này có phải nghỉ cả ngày không
     */
    public function isDayOff()
    {
        return $this->status === 'off';
    }
}
