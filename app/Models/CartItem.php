<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $primaryKey = 'id';
    protected $fillable = ['cart_id', 'product_variant_id', 'quantity', 'price'];

    // Mối quan hệ với Cart
    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id', 'id');
    }

    // Mối quan hệ với ProductVariant (bao gồm cả soft deleted)
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id')->withTrashed();
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /**
     * Kiểm tra sản phẩm có còn bán không
     */
    public function isProductAvailable()
    {
        return $this->productVariant && 
               !$this->productVariant->trashed() && 
               $this->productVariant->product && 
               !$this->productVariant->product->trashed();
    }

    /**
     * Lấy thông báo trạng thái sản phẩm
     */
    public function getProductStatusMessage()
    {
        if (!$this->productVariant) {
            return 'Sản phẩm không tồn tại';
        }
        
        if ($this->productVariant->trashed()) {
            return 'Sản phẩm không còn bán';
        }
        
        if (!$this->productVariant->product) {
            return 'Sản phẩm không tồn tại';
        }
        
        if ($this->productVariant->product->trashed()) {
            return 'Sản phẩm không còn bán';
        }
        
        return null; // Sản phẩm bình thường
    }
}