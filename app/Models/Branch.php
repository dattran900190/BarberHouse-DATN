<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
    ];

    public function barbers()
    {
        return $this->hasMany(Barber::class,);
    }
}
