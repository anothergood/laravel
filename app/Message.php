<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id', 'chat_id', 'body', 'is_read',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function Chat()
    {
        return $this->belongsTo(Chat::class);
    }
}
