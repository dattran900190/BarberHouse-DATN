<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $primaryKey = 'id';
    protected $fillable = ['user_id'];

    // Mối quan hệ với CartItem
    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }

    // Mối quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}