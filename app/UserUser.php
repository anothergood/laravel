<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserUser extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $table = 'user_user';

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
