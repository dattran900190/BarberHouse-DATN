<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description']; // Các cột có thể gán giá trị hàng loạt
    protected $table = 'product_categories'; // Tên bảng trong database

    // Quan hệ với sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id'); // Một danh mục có nhiều sản phẩm
    }
}