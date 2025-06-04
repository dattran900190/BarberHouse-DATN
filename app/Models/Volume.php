<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Volume extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    protected $table = 'volumes';

    // Mối quan hệ: Một volume có nhiều product variant
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'volume_id');
    }
}
