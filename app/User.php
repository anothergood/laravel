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

    public function likes()
    {
        return $this->belongsToMany(Post::class, 'user_post', 'user_id', 'post_id');
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'user_chat', 'user_id')->using(UserChat::class)->withPivot('unread_messages')->withTimestamps();
    }

    public function userPush($data)
    {
        event(new ChatPrivateMessage($this, $data));
    }

    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'user_user', 'user_initiator_id', 'user_id')
                    ->withPivot('status')
                    ->wherePivot('status','approved')
                    ->withTimestamps();
    }

    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'user_user', 'user_id', 'user_initiator_id')
                    ->withPivot('status')
                    ->wherePivot('status','approved')
                    ->withTimestamps();
    }

    public function getFriendsAttribute()
    {
        if ( ! array_key_exists('user_user', $this->relations)) $this->loadFriends();

        return $this->getRelation('user_user');
    }

    protected function loadFriends()
    {
        if ( ! array_key_exists('user_user', $this->relations))
        {
            $friends = $this->mergeFriends();

            $this->setRelation('user_user', $friends);
        }
    }

    protected function mergeFriends()
    {
        return $this->friendsOfMine->merge($this->friendOf);
    }
    
}
