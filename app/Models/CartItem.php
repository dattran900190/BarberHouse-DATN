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

    // Mối quan hệ với ProductVariant
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id', 'id');
    }
    public function product()
{
    return $this->belongsTo(Product::class, 'product_id', 'id');
}
}