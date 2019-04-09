<?php

namespace App;

use App\User;
use App\Events\ChatMessage;
use Illuminate\Database\Eloquent\Model;

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

    public function userNotification( $data, $user_id ) {

        $this->users()->where('id', $user_id)->first()->userPush($data);

    }

    public function usersNotification( $data, $except = null) {

        $this->users()->where('id', '<>', $except)->chunk(10, function ($users) use($data) {
            foreach ($users as $user) {
                $this->userNotification($data, $user->id);
            }
        });

    }

}
