<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use App\Events\ChatPrivateMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'username', 'email', 'password','verified',
    ];

    protected $hidden = [
        'password', 'remember_token', 'pivot',
    ];

    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    public function verifyUser()
    {
        return $this->hasOne(VerifyUser::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_user', 'user_initiator_id', 'user_id')->withPivot('status')->withTimestamps();
    }

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'user_post', 'user_id', 'post_id');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'user_chat', 'user_id')->using('App\UserChat')->withPivot('unread_messages')->withTimestamps();
    }

    public function userPush($data)
    {
        event(new ChatPrivateMessage($this, $data));
    }

}
