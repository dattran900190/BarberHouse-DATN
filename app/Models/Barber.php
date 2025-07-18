<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barber extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barbers';

    protected $fillable = [
        'branch_id',
        'name',
        'profile',
        'skill_level',
        'avatar',
        'rating_avg',
        'status'
    ];
    
    protected $dates = ['deleted_at'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function schedules()
    {
        return $this->hasMany(BarberSchedule::class);
    }
}