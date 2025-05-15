<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    use HasFactory;

    protected $fillable = ['name']; // Các cột có thể gán giá trị hàng loạt
    protected $table = 'volumes'; // Tên bảng trong database

    // Quan hệ với biến thể sản phẩm
    public function variants()
    {
        return $this->hasMany(ProductVariant::class); // Một dung tích có nhiều biến thể
    }
}