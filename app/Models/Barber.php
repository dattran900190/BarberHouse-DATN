<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barber extends Model
{
    use HasFactory;

    protected $table = 'barbers';

    protected $fillable = [
        'name',
        'profile',
        'skill_level',
        'avatar',
        'rating_avg',
    ];
}
