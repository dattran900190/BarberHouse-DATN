<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Volume extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name'];
    protected $table = 'volumes';

    // Mối quan hệ: Một volume có nhiều product variant
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'volume_id');
    }
}
