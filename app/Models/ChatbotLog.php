<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    protected $table = 'chatbot_logs';

    protected $fillable = [
        'user_id',
        'message',
        'reply',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
