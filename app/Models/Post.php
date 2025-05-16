<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'image',
        'author_id', 'status', 'published_at'
    ];

    protected $dates = ['published_at'];

    // Quan hệ với User
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
