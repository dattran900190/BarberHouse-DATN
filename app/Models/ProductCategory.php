<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{

    use HasFactory;
    // use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description']; // Các cột có thể gán giá trị hàng loạt
    protected $table = 'product_categories'; // Tên bảng trong database

    // Quan hệ với sản phẩm
    public function products()
    {
        return $this->hasMany(Product::class, 'product_category_id'); // Một danh mục có nhiều sản phẩm
    }
    protected static function booted()
    {
        static::deleting(function ($category) {
            if (! $category->isForceDeleting()) {
                // Xoá mềm toàn bộ product thuộc category này
                $category->products()->each(function ($product) {
                    $product->delete();
                });
            }
        });

        static::restoring(function ($category) {
            $category->products()->withTrashed()->each(function ($product) {
                $product->restore();
            });
        });
    }
}
