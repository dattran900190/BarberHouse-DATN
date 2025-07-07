<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug','short_description', 'content', 'image',
        'author_id', 'status', 'published_at','is_featured',
    ];

    protected $dates = ['published_at'];

    // Quan hệ với User
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
