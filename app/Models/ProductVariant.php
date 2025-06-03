<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'volume_id', 'name', 'description', 'price', 'stock', 'image']; // Các cột có thể gán giá trị hàng loạt
    protected $table = 'product_variants'; // Tên bảng trong database

    // Quan hệ với sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
        // return $this->belongsTo(Product::class); // Mỗi biến thể thuộc về một sản phẩm
    }

    // Quan hệ với dung tích
    public function volume()
    {
        return $this->belongsTo(volume::class); // Mỗi biến thể có một dung tích
    }
}
