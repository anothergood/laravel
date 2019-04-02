<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\ChatMessage;

class Chat extends Model
{
    protected $fillable = [
        'title', 'type',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_chat', 'chat_id', 'user_id')->using('App\UserChat')->withPivot('unread_messages')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function chatPush($data)
    {
        event(new ChatMessage($this, $data));
    }
}
