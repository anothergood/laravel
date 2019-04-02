<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserChat extends Pivot
{
    protected $fillable = [
        'user_id', 'chat_id', 'unread_messages'
    ];

    

}
