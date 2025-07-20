<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    /** @use HasFactory<\Database\Factories\BranchFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'address',
        'phone',
        'google_map_url',
        'image',
        'content',
    ];

    public function barbers()
    {
        return $this->hasMany(Barber::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
