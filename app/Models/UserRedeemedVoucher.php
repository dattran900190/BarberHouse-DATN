<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRedeemedVoucher extends Model
{
    use HasFactory;
    protected $table = 'user_redeemed_vouchers';

    // Không dùng timestamps mặc định (created_at, updated_at)
    public $timestamps = false;


    // Các cột có thể gán giá trị hàng loạt
    protected $fillable = [
        'user_id',
        'promotion_id',
        'redeemed_at',
        'is_used',
        'used_at',
    ];

    // Ép kiểu dữ liệu
    protected $casts = [
        'redeemed_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    // Quan hệ: 1 voucher đã đổi thuộc về 1 người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: 1 voucher đã đổi thuộc về 1 chương trình khuyến mãi
    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}
